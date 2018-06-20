<?php //

class PopulateController extends Zend_Controller_Action
{

    public function init()
    {
        //delete this controller for sure
    }
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $logTable = new Application_Model_DbTable_LogTable();
        $inventoryTable = new Application_Model_DbTable_InventoryTable();
        $signTable = new Application_Model_DbTable_SignTable();
        $userTable = new Application_Model_DbTable_UserTable();
        $inventory=$inventoryTable->getTypes(1);
        $users=$userTable->getAllbyCampaignId(1);
        $type = array('Place','OK','Replace','Fix','Recover','Recovered');
        $signs = $this->getRequest()->getParam('signs');
                for($i=0;$i<1500;$i++){
                    $sign = Array(
                    'inventory_id' => $inventory[rand(0, sizeof($inventory)-1)]['inventory_id'],
                    'lat'=> mt_rand(332219800,338287399)*.0000001,
                    'lng'=> mt_rand(-112581177,-111705017)*.000001,
                    'user_id'=>$users[rand(0,sizeof($users)-1)]['user_id']
                    );
                    $status = $type[rand(0,sizeof($type)-1)];
                    $addr = $this->getAddress($sign['lat'],$sign['lng']);
                    $sign_id = $signTable->populate($sign,$addr,$status);
                    $log_id = $logTable->add($sign_id,1,$auth->getIdentity()->user_id,$auth->getIdentity()->lname.', '.$auth->getIdentity()->fname,'Added',$status);
                }
        
        
       
    }
    private function getAddress($lat,$lng){
            
        $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$lng."&sensor=true";
        $data = @file_get_contents($url);
        $jsondata = json_decode($data,true);
        
         if(is_array($jsondata) && $jsondata['status'] == "OK")
    {
          $data = array();
            foreach($jsondata['results']['0']['address_components'] as $element){
                $data[ implode(' ',$element['types']) ] = $element['short_name'];
            }

           
          $address = $jsondata['results']['0']['formatted_address'];
          
          return $address;
    }else{
            return false;
        }
    }
}

