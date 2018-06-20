<?php //

class CampaignController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */ 
        //date_default_timezone_set('America/Phoenix');
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
	$ajaxContext->addActionContext('list', 'html')
                ->addActionContext('edit', 'html')
                ->addActionContext('dashboard', 'html')
                ->addActionContext('handler', 'html')
                ->initContext();
        $auth = Zend_Auth::getInstance();
        $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $type = $this->getRequest()->getParam('type');
        if (!$auth->hasIdentity() && $action!='handler') {
            //echo "THIS IS AN ERROR: ".$action;
            $this->_redirect('login', array('UseBaseUrl' => true));
        }
    }
    
    public function indexAction()
    {
        
        $auth = Zend_Auth::getInstance();

        if ($auth->getIdentity()->campaign_id>0) {
            $campaignTable = new Application_Model_DbTable_CampaignTable;
            $camp = $campaignTable->getOneById($auth->getIdentity()->campaign_id);
            if($camp['name']==''){
                $this->_redirect('logout', array('UseBaseUrl' => true));
            }else{
                $this->_redirect('campaign/'.$auth->getIdentity()->campaign_id.'/dashboard', array('UseBaseUrl' => true));
            }
            
        }else{
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/campaign.js');
            $this->view->date = date("Y/m/d 00:00:00",time());
            $this->view->date90 = date("Y/m/d 00:00:00",time()-60*60*24*90);
            $this->view->tomorrow = date("Y/m/d 00:00:00",time()+60*60*24);
            $this->view->role = $auth->getIdentity()->role;
        }
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.autotab-1.1b.js');
    }
    //after user has purchased and on first login
    public function setupAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $campaignTable = new Application_Model_DbTable_CampaignTable;
        $auth = Zend_Auth::getInstance();
        
        $postvars = $this->getRequest()->getPost();
        $coords = $this->getBounds($postvars['locale']);
        $postvars['nelat'] = $coords['nelat'];
        $postvars['nelng'] = $coords['nelng'];
        $postvars['swlat'] = $coords['swlat'];
        $postvars['swlng'] = $coords['swlng'];
        
        $id = $campaignTable->completeSetup($postvars,$auth->getIdentity()->campaign_id);
        if($id>=0){
            $userTable = new Application_Model_DbTable_UserTable;
            $id = $userTable->resetPassword($this->getRequest()->getParam('password'),$auth->getIdentity()->username);
        }
        echo $id;
    }
    public function dashboardAction()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->getIdentity()->campaign_id!=$this->getRequest()->getParam('id') && $auth->getIdentity()->campaign_id!=0) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/css/datepicker.css');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/dashboard.js');
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('login', array('UseBaseUrl' => true));
        }
        $campaignTable = new Application_Model_DbTable_CampaignTable;
        $signTable = new Application_Model_DbTable_SignTable;
        $inventoryTable = new Application_Model_DbTable_InventoryTable;
        $campaign= new Zend_Session_Namespace('campaign');
        $campaign->campaign_id=$this->getRequest()->getParam('id');
        $camp = $campaignTable->getOneById($this->getRequest()->getParam('id'));
        $this->view->campaign = $camp;
        
        $dashboard1 = $campaignTable->getDashboard($this->getRequest()->getParam('id'));
        $dashboard2 = $signTable->getDashboard($this->getRequest()->getParam('id'));
        
        $dashboard = array_merge($dashboard1,$dashboard2);
        foreach ($dashboard as &$value) {
    if(!$value)$value=0;
}
        
        $this->view->dashboard = $dashboard;
        
        $this->view->inventory = $inventoryTable->getDashboard($this->getRequest()->getParam('id'));
        $this->view->role = $auth->getIdentity()->role;
        $today =  date("Y-m-d",time());
        $today>$dashboard1['date_election_raw']?$this->view->is_over = true:$this->view->is_over = false;
        
    }
    public function listAction(){
        $campaignTable = new Application_Model_DbTable_CampaignTable;
                
                $data = $campaignTable->getCampaigns();
                if($this->getRequest()->getParam('is_export')==0){
                    $this->view->items = $data;
                }
                
    }
    public function handlerAction(){
        $type = $this->getRequest()->getParam('type');
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity() && $type!='setup') {
            //echo "THATS A HANDLER ERROR: ".$type;
            $this->_redirect('login', array('UseBaseUrl' => true));
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $campaignTable = new Application_Model_DbTable_CampaignTable();
        switch($type){
            case "editInventory":
                $inventoryTable = new Application_Model_DbTable_InventoryTable;
                $inv = $inventoryTable->getOneById($this->getRequest()->getParam('id'));
                $notified = $inv['notified'];
                $vars = $this->getRequest()->getPost();
                $campaign= new Zend_Session_Namespace('campaign');
                if($notified==1 && $vars['num_total']-$inv['num_used']>$campaign->num_notify){
                    $notified=0;
                }
                $vars['notified']=$notified;
                echo $inventoryTable->edit($vars);
                break;
           
            case "delete":
                $signTable = new Application_Model_DbTable_SignTable();
                $inventoryTable = new Application_Model_DbTable_InventoryTable;
                $userTable = new Application_Model_DbTable_UserTable;
                $logTable = new Application_Model_DbTable_LogTable;
                $userCampaignsTable = new Application_Model_DbTable_UserCampaignsTable;
                //delete image files
                $files = glob('/var/www/manage/images/upload/'.$this->getRequest()->getParam('campaign_id').'-*');
                for($i=0;$i<sizeof($files);$i++){
                    unlink($files[$i]);
                }
                
                //delete signs
                $signTable->deleteAll($this->getRequest()->getParam('campaign_id'));
                
                //delete inventory
                $inventoryTable->deleteByCampaignId($this->getRequest()->getParam('campaign_id'));
                
                //delete logs
                $logTable->deleteByCampaignId($this->getRequest()->getParam('campaign_id'));
                
                //delete user campaign
                $userCampaignsTable->deleteByCampaignId($this->getRequest()->getParam('campaign_id'));
                
                //delete user
                $userTable->deleteUnassigned();
                        
                //delete campaign
                $id = $campaignTable->deleteById($this->getRequest()->getParam('campaign_id'));
                echo $id;
                break;
            case "addInventory":
                $campaign= new Zend_Session_Namespace('campaign');
                $postvars = $this->getRequest()->getPost();
                $inventoryTable = new Application_Model_DbTable_InventoryTable;
                $id=$inventoryTable->add($this->getRequest()->getPost(),$campaign->campaign_id);
                echo $id;
                break;   
            case "editCampaign":
                $campaign= new Zend_Session_Namespace('campaign');
                echo $campaignTable->edit($this->getRequest()->getPost(),$campaign->campaign_id);
                break;
            case "addCampaign":
                $campaign= new Zend_Session_Namespace('campaign');
                echo $campaignTable->add($this->getRequest()->getPost(),$campaign->campaign_id);
                break;  
            case "resetAll":
                $campaign= new Zend_Session_Namespace('campaign');
                $inventoryTable = new Application_Model_DbTable_InventoryTable;
                $signTable = new Application_Model_DbTable_SignTable;
                $logTable = new Application_Model_DbTable_LogTable;
                $logTable->delete("campaign_id = " . $campaign->campaign_id);
                $signTable->deleteAll($campaign->campaign_id);
                $inventoryTable->delete("campaign_id = " . $campaign->campaign_id);
                $signTable->resetCardinality();
                break;
            case "setup":
                $temp_password=$this->generateRandomString(6);
                $postvars = $this->getRequest()->getPost();
                if($id = $campaignTable->setup($postvars)){
                    $postvars['campaign_id']=$id;
                    $postvars['username']=$this->getRequest()->getParam('email');
                    $userTable = new Application_Model_DbTable_UserTable;
                    $userCampaignsTable = new Application_Model_DbTable_UserCampaignsTable();
                    if($user = $userTable->add($postvars,$temp_password)){
                        if($id2 = $userCampaignsTable->addToCampaign($user, $id, 'Admin')){
                            echo $temp_password;
                            //send email
                            try {
                                $mail = new Zend_Mail();
                                $mail->setBodyText("Welcome,\n\nTo begin using SignTrack App, please visit https://signtrackapp.com/manage and use the following credentials to login.\n\rEmail: ".$this->getRequest()->getParam('email')."\n\rTemporary Password: ".$temp_password."\n\rIf you have any problems logging in or need assistance, please call (520) 240-8430 or email us at support@signtrackapp.com.\n\rThank You,\nSignTrack App Team");
                                $mail->setBodyHtml("Welcome,<BR><BR>To begin using SignTrack App, please visit https://signtrackapp.com/manage and use the following credentials to login.<BR><BR>Email: ".$this->getRequest()->getParam('email')."<BR><BR>Temporary Password: ".$temp_password."<BR><BR>If you have any problems logging in or need assistance, please call (520) 240-8430 or email us at support@signtrackapp.com.<BR><BR>Thank You,<BR>SignTrack App Team");
                                $mail->setFrom('support@signtrackapp.com', 'SignTrack App');
                                $mail->addTo($this->getRequest()->getParam('email'), $this->getRequest()->getParam('fname')." ".$this->getRequest()->getParam('lname'));
                                $mail->setSubject('Account Access');
                                $mail->send();
                            } catch (Exception $e) {
                                echo 'Caught exception: ',  $e->getMessage(), "\n";
                            }
                        }else{
                            echo -3;
                        }
                    }else{
                        echo -2;
                    }
                }else{
                    echo -1;
                }
                
        }   
    }
    public function generateRandomString($length = 4) {
        $characters = '123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    private function geoCode($string){
 
        $string = str_replace(" ", "+", urlencode($string));
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

        $longitude = $geometry['location']['lat'];
        $latitude = $geometry['location']['lng'];
        $array = array(
            'lat' => $geometry['location']['lat'],
            'lng' => $geometry['location']['lng']
        );

        return $array;
    }
    
    private function getBounds($string){
 
        $string = str_replace(" ", "+", urlencode($string));
        //$details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $string;
        $details_url = $this->signUrl("https://maps.google.com/maps/api/geocode/json?address=" . $string . "&client=gme-jandjlaosllc", 'NUxuOEqCFw4ML4LYW42_u7c9Njo=');        
        //$details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $string."&client=gme-jandjlaosllc";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = json_decode(curl_exec($ch), true);
        
        // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
        if ($response['status'] != 'OK') {
            return null;
        }

        $geometry = $response['results'][0]['geometry']['bounds'];

        $array = array(
            'nelat' => $geometry['northeast']['lat'],
            'nelng' => $geometry['northeast']['lng'],
            'swlat' => $geometry['southwest']['lat'],
            'swlng' => $geometry['southwest']['lng']
        );

        return $array;
    }
    private function encodeBase64UrlSafe($value)
    {
      return str_replace(array('+', '/'), array('-', '_'),
        base64_encode($value));
    }
    private function decodeBase64UrlSafe($value)
    {
      return base64_decode(str_replace(array('-', '_'), array('+', '/'),
        $value));
    }
    private function signUrl($myUrlToSign, $privateKey)
{
  // parse the url
  $url = parse_url($myUrlToSign);

  $urlPartToSign = $url['path'] . "?" . $url['query'];

  // Decode the private key into its binary format
  $decodedKey = $this->decodeBase64UrlSafe($privateKey);

  // Create a signature using the private key and the URL-encoded
  // string using HMAC SHA1. This signature will be binary.
  $signature = hash_hmac("sha1",$urlPartToSign, $decodedKey,  true);

  $encodedSignature = $this->encodeBase64UrlSafe($signature);

  return $myUrlToSign."&signature=".$encodedSignature;
}

}

