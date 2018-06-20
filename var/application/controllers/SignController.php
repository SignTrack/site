<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class SignController extends Zend_Controller_Action {
    
    public function init() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('login', array('UseBaseUrl' => true));
        }
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('list', 'html')
                ->initContext();
    }
    public function getSignsUsed($id){
        $db = $this->getAdapter();
        $sql = "SELECT FORMAT(COUNT(sign_id),0) AS num_signs
        FROM sign
        LEFT JOIN inventory
        ON(sign.inventory_id=inventory.inventory_id)
        WHERE sign.status!='Deactivated' &&  sign.status!='Recovered' && campaign_id=".$id;
        return $db->fetchRow($sql);
    }
    public function indexAction() {
        $auth = Zend_Auth::getInstance();

        if ($auth->getIdentity()->campaign_id != $this->getRequest()->getParam('id') && $auth->getIdentity()->campaign_id != 0) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }


        $this->view->headScript()->appendFile('//maps.googleapis.com/maps/api/js?client=gme-jandjlaosllc');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/markerclusterer_compiled.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/sign.js?version=1.1');

        //get campaign inforamtion
        $campaignTable = new Application_Model_DbTable_CampaignTable;
        $campaign = new Zend_Session_Namespace('campaign');
        $campaign->campaign_id = $this->getRequest()->getParam('id');

        $obj = $campaignTable->getOneById($this->getRequest()->getParam('id'));
        $today = date("Y-m-d", time());
        $today > $obj['date_election'] ? $campaign->is_over = true : $campaign->is_over = false;

        $campaign->sign_limit = $obj['sign_limit'];
        $campaign->notifications = $obj['notifications'];
        $campaign->num_notify = $obj['num_notify'];
        $campaign->email = $obj['email'];
        $this->view->campaign = $obj;
        $userTable = new Application_Model_DbTable_UserTable;
        $this->view->users = $userTable->getAllbyCampaignId($this->getRequest()->getParam('id'));
        $inventoryTable = new Application_Model_DbTable_InventoryTable;
        $this->view->inventory = $inventoryTable->getTypes($this->getRequest()->getParam('id'));
        $this->view->sign_limit = $campaign->sign_limit;
        //pass the signs to view
        $signTable = new Application_Model_DbTable_SignTable();
        $this->view->items = $signTable->getAll($campaign->campaign_id, $campaign->is_over);
    }

    public function handlerAction() {
        $type = $this->getRequest()->getParam('type');
        $signTable = new Application_Model_DbTable_SignTable();
        $logTable = new Application_Model_DbTable_LogTable();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();
        $campaign = new Zend_Session_Namespace('campaign');
        switch ($type) {
            case "get_user":
                $user = $usertable->getUserById($user_id);
                echo Zend_Json::encode($user);
                break;
            case "edit":
                $notify = false;
                $sign = $signTable->getOneById($this->getRequest()->getParam('id'));
                $id = $signTable->edit($this->getRequest()->getPost());
                echo $id;
                $logTable->add($this->getRequest()->getParam('id'), $campaign->campaign_id, $auth->getIdentity()->user_id, $auth->getIdentity()->lname . ', ' . $auth->getIdentity()->fname, $this->getRequest()->getParam('actionlog'), $this->getRequest()->getParam('status'));

                $inventoryTable = new Application_Model_DbTable_InventoryTable;
                if ($this->getRequest()->getParam('num_signs') > 0) {
                    $this->getRequest()->getParam('status') == 'Recovered' ? $sign = '-' : $sign = '+';
                    $inventoryTable->debitInventory($this->getRequest()->getParam('sign_id'), $this->getRequest()->getParam('num_signs'), $sign);
                }
                if ($this->getRequest()->getParam('num_materials') > 0) {
                    $this->getRequest()->getParam('status') == 'Recovered' ? $sign = '-' : $sign = '+';
                    $inventoryTable->debitInventory($this->getRequest()->getParam('material_id'), $this->getRequest()->getParam('num_materials'), $sign);
                }
                if ($campaign->notifications == 1) {
                    $inv = $inventoryTable->getInventoriesBelowThreshohold($campaign->campaign_id, $campaign->num_notify);
                    if (sizeof($inv) > 0) {
                        //if below inventory threshold send email
                        $list = '';
                        for ($i = 0; $i < sizeof($inv); $i++) {
                            $list.= $inv[$i]['name'] . ':' . $inv[$i]['num'] . ' units<br>';
                        }
                        $mail = new Zend_Mail();
                        $mail->setBodyText("This is just a notice that you are below the inventory threshold you set up for your account. The following inventory is below " . $campaign->num_notify . " units:\n\r$list\n\rSignTrack App Team");
                        $mail->setBodyHtml("This is just a notice that you are below the inventory threshold you set up for your account. The following inventory is below " . $campaign->num_notify . " units:<br><br>$list<br><br>SignTrack App Team");
                        $mail->setFrom(Zend_Registry::get('config')->sig->noreply, 'SignTrack App');
                        $mail->addTo($campaign->email);
                        $mail->setSubject('Account Access');
                        $mail->send();
                    }
                }

                break;
            case "delete":
                echo $signTable->updateStatus($this->getRequest()->getParam('id'), 'Deactivated');
                $logTable->add($this->getRequest()->getParam('id'), $campaign->campaign_id, $auth->getIdentity()->user_id, $auth->getIdentity()->lname . ', ' . $auth->getIdentity()->fname, 'Deleted', 'Deactivated');
                break;
            case "add":
                $signs = $this->getRequest()->getParam('signs');
                for ($i = 0; $i < sizeof($signs); $i++) {
                    $addr = '';
                    $signs[$i]['status']='Place';
                    $sign_id = $signTable->add($signs[$i], $addr, '');
                    $log_id = $logTable->add($sign_id, $campaign->campaign_id, $auth->getIdentity()->user_id, $auth->getIdentity()->lname . ', ' . $auth->getIdentity()->fname, 'Added', 'Place');
                }

                echo Zend_Json::encode($signTable->getAll($campaign->campaign_id, $campaign->is_over));
                break;
            case "setMap":
                $campaignTable = new Application_Model_DbTable_CampaignTable;
                $id = $campaignTable->updateMap($this->getRequest()->getPost(),$campaign->campaign_id);
                echo $id;
                break;
            case "address":
                $address = $this->getRequest()->getParam('address').','.$this->getRequest()->getParam('zip');
                $sign_id=0;
                $sign;
                //for testing
                //$coords=array('lat'=>33.6898709, 'lng'=>-111.999033);
                //if(isset($coords)){
                if($coords = $this->geoCode($address)){
                    if(isset($coords['lat']) && $coords['lat']!='' && $coords['lat']!='0'){
                        if(isset($coords['address']) && $coords['address']!=''){
                            $address = $coords['address'];
                        }
                        $sign = array(
                            'lat' => $coords['lat'],
                            'lng' => $coords['lng'],
                            'note' => $address,
                            'user_id' => $this->getRequest()->getParam('user_id'),
                            'status' => 'Place',
                            'name' =>$this->getRequest()->getParam('name'),
                            'inventory_id' => $this->getRequest()->getParam('inventory_id')
                        );
                        $sign['status']='Place';
                        $sign_id = $signTable->add($sign, $this->getRequest()->getParam('address'), $address);
                        $sign['sign_id'] = $sign_id;
                    }else{
                        $sign = array('err_msg' => 'The address specified could not be processed. Please try again.');
                    }
                }
                if($sign_id>0){
                    $sign['err_msg'] = '';
                    $log_id = $logTable->add($sign_id, $campaign->campaign_id, $auth->getIdentity()->user_id, $auth->getIdentity()->lname . ', ' . $auth->getIdentity()->fname, 'Added', 'Place');
                }else if(!isset($sign['err_msg'])){
                    $sign = array('err_msg' => 'There was an error processing this sign. Please try again later.');
                }
                echo Zend_Json::encode($sign);
                break;
            case "assign":
                $signs = $this->getRequest()->getParam('signs');
                $user_id = $this->getRequest()->getParam('user_id');
                for ($i = 0; $i < sizeof($signs); $i++) {
                    $update = $signTable->editAssigned($signs[$i]['sign_id'], $user_id);
                }

                echo Zend_Json::encode($signTable->getAll($campaign->campaign_id, $campaign->is_over));
                break;
            case "listupload":
                ini_set('auto_detect_line_endings', true);
                $filename = Zend_Registry::get('config')->file->upload->location.$this->getRequest()->getParam('inventory_id').'.csv';
                if(($handle = fopen($filename, "r")) !== false) {
                $i=0;
                $added = array();
                $notadded = array();
                $over = array();
                $msg = 1;
                //get campaign so we can check the limit
                $campaign = new Zend_Session_Namespace('campaign');
                //get inventory so we can check used
                $limit = $signTable->getSignsUsed($campaign->campaign_id);
                $num_used = $limit['num_signs'];
                $num_left = $campaign->sign_limit;
                
                $userTable = new Application_Model_DbTable_UserTable;
                $user = $userTable->getOneById($this->getRequest()->getParam('user_id'));
                //cycle through each new address
                
                while (($data = fgetcsv($handle, 0, ",")) !== false) {
                    //var_dump($data);
                    if ($i != 0) {
                        if($num_used<=$num_left){
                        $address = $data[0] . ',' . $data[1];
                        if($coords = $this->geoCode($address)){
                            //if coordinates were found
                            if (isset($coords['lat']) && $coords['lat'] != '' && $coords['lat'] != '0') {
                                if (isset($coords['address']) && $coords['address'] != '') {
                                    $address = $coords['address'];
                                }
                                $sign = array(
                                    'address'=>$address,
                                    'lat' => $coords['lat'],
                                    'lng' => $coords['lng'],
                                    'note' => $address,
                                    'user_id' => $this->getRequest()->getParam('user_id'),
                                    'status' => $this->getRequest()->getParam('status'),
                                    'inventory_id' => $this->getRequest()->getParam('inventory_id')
                                );
                                $sign_id = $signTable->add($sign, $address, $address);
                                $sign['sign_id'] = $sign_id;
                                $sign['name']   =$user['lname'].', '.$user['fname'];
                                $log_id = $logTable->add($sign_id, $campaign->campaign_id, $auth->getIdentity()->user_id, $auth->getIdentity()->lname . ', ' . $auth->getIdentity()->fname, 'Added', 'Place');
                                $added[] =  $sign;
                                $num_used++;
                            } else {
                                $notadded[] = array('address'=>  $data[0],'zip'=>$data[1]);
                            }
                        }else{
                           //coordinates were not found
                           $notadded[] = array('address'=>  $data[0],'zip'=>$data[1]);
                        }
                        }else{
                            $over[] = array('address'=>  $data[0],'zip'=>$data[1]);
                        }
                    }
                    $i++;
                }
                if(sizeof($added)>0){
                    //update inventory levels if signs were added
                    $inventoryTable = new Application_Model_DbTable_InventoryTable;
                    $inventoryTable->debitInventory($this->getRequest()->getParam('inventory_id'), sizeof($added), '+');
                    $inventoryTable->debitInventory($this->getRequest()->getParam('material_id'), sizeof($added)*$this->getRequest()->getParam('num_materials'), '+');
                }
                $data = array(
                    'added' => $added,
                    'notadded' => $notadded,
                    'over' => $over
                );
                echo Zend_Json::encode($data);
                fclose($handle);
            }else{
                echo "unable to open import file.";
            }
                break;
        }
    }

    private function getAddress($lat, $lng) {

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng . "&key=AIzaSyAo8cgSlRDFOwojX1KiUdop95Ybq-lWVWo";
        $data = @file_get_contents($url);
        $jsondata = json_decode($data, true);

        if (is_array($jsondata) && $jsondata['status'] == "OK") {
            $address = $jsondata['results']['0']['formatted_address'];
            return $address;
        } else {
            return false;
        }
    }
    private function geoCode($string){
        $string = str_replace(" ", "+", urlencode($string));
        //$details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $string . "&key=AIzaSyCd1RrlrBNnzy4M4m-zn3XU6Ymm_DF29-c";
        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $string . "&key=AIzaSyAo8cgSlRDFOwojX1KiUdop95Ybq-lWVWo";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] != 'OK') {
            return null;
        }
        $geometry = $response['results'][0]['geometry'];
        $formatted_address = $response['results'][0]['formatted_address'];
        $longitude = $geometry['location']['lat'];
        $latitude = $geometry['location']['lng'];
        $array = array(
            'lat' => $geometry['location']['lat'],
            'lng' => $geometry['location']['lng'],
            'address' => $formatted_address
        );
        return $array;
    }
}
