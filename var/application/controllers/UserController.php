<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */ 
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('login', array('UseBaseUrl' => true));
        }
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
	$ajaxContext->addActionContext('list', 'html')
                ->addActionContext('edit', 'html')
                ->initContext();

    }
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $campaign= new Zend_Session_Namespace('campaign');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/user.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.autotab-1.1b.js');
        
        if ($auth->getIdentity()->campaign_id!=$this->getRequest()->getParam('id')  && $auth->getIdentity()->campaign_id!=0) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }else if($this->getRequest()->getParam('id')){
            $campaign->campaign_id = $this->getRequest()->getParam('id');
            $campaignTable = new Application_Model_DbTable_CampaignTable;
            $camp = $campaignTable->getOneById($this->getRequest()->getParam('id'));
            $this->view->campaign = $camp;
            $campaign->name = $camp['name'];
        }else{
           $campaign->campaign_id = 0; 
        }
    }
    
    public function listAction(){
        $userTable  = new Application_Model_DbTable_UserTable();
        $campaign= new Zend_Session_Namespace('campaign');
        if($campaign->campaign_id){
            $this->view->users =  $userTable->getAllbyCampaignId($campaign->campaign_id);
        }else{
            $this->view->users =  $userTable->getAllbyCampaignId(0);
        }
        
        
    }
    
  
    public function handlerAction(){
        $type = $this->getRequest()->getParam('type');
        $user_id = $this->getRequest()->getParam('id');
        $usertable = new Application_Model_DbTable_UserTable();
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        
        switch($type){
            case "get_user":
                $user=$usertable->getUserById($user_id);
                echo Zend_Json::encode($user);
                break;
            case "edit":
                $id = $usertable->edit($this->getRequest()->getPost());
                echo $id;
                break;
            case "delete":
                $auth = Zend_Auth::getInstance();
                if($this->getRequest()->getParam('user_id')!=$auth->getIdentity()->user_id){
                    $signtable = new Application_Model_DbTable_SignTable();
                    $signtable->unassignDeletedId($this->getRequest()->getParam('user_id'));
                    $usercampaigntable =  new Application_Model_DbTable_UserCampaignsTable();
                    $campaign= new Zend_Session_Namespace('campaign');
                    $usercampaigntable->deleteById($this->getRequest()->getParam('user_id'),$campaign->campaign_id);
                    //check if user is assigned to any other campaigns before deleting permanently
                    if($usercampaigntable->hasOtherCampaigns($this->getRequest()->getParam('user_id'))){
                         //user exists in other campaigns so don't delete
                        $id=1;
                        
                    }else {
                      
                        $id = $usertable->deleteById($this->getRequest()->getParam('user_id'));
                    }
                    
                }else{
                    $id=-2;
                }
                echo $id;
                break;
            case "add":
                //needs three scenarios: user exists, user exists in campaign, new user
                $auth = Zend_Auth::getInstance();
                $role = 'Volunteer';
                $id=0;
                $assign=0;
                //determine if Master
                $campaign= new Zend_Session_Namespace('campaign');
                
                if($campaign->campaign_id==0 && $auth->getIdentity()->role=='Master')$role='Master';
                
                //check if user exists
                if ($user = $usertable->getUserByEmail($this->getRequest()->getParam('username'),false)){
                    if($role == 'Master'){
                        //master already exists, return false
                        $id=-1;
                    }else{
                        //Volunteer exists in the database see if assignted to this campaign
                        $userCampaignsTable = new Application_Model_DbTable_UserCampaignsTable;
                        if($userCampaignsTable->hasCampaigns($user['user_id'], $campaign->campaign_id)){
                            //user is already assigned to this campaign
                            $id=-1;
                        }else{
                            //user has not been assigned
                            $id = $userCampaignsTable->addToCampaign($user['user_id'], $campaign->campaign_id, $role);
                            $assign=1;
                        }
                    }
                }else{
                    //user doesn't exist, add the user
                    $pass = $this->generateRandomString(6);
                    $id = $usertable->add($this->getRequest()->getPost(),$pass);
                    $userCampaignsTable = new Application_Model_DbTable_UserCampaignsTable;
                    $id2 = $userCampaignsTable->addToCampaign($id, $campaign->campaign_id, $role);
                }
                echo $id;
                if($id!=-1){
                        $mail = new Zend_Mail();
                        $campaign= new Zend_Session_Namespace('campaign');
                        if($assign==0){
                            $user = $usertable->getUserByEmail($this->getRequest()->getParam('username'),false);
                            //die(var_dump($user));
                            $link = base64_encode(date('zY', strtotime('00:00:00 +14 day')).'@1@'.md5($user['username']).'@1@'. substr($user['salt'], 0, 13)  );
                            $link = $this->view->serverUrl().$this->view->baseUrl().'/reset?h='.$link;
                            if($role=='Volunteer'){
                                $mail->setBodyText("Welcome,\n\nYou have been added as a Team Member to the SignTrack App campaign: ".$campaign->name.". Please download the app for iOS or Android and sign in using your email address and temporary password.\n\rEmail: ".$this->getRequest()->getParam('username')."\n\rTemporary Password: ".$pass."\n\rAndroid:\nhttps://play.google.com/store/apps/details?id=com.signtrackapp.mobile\n\riOS:\nhttps://itunes.apple.com/us/app/signtrack-app/id982585645?ls=1&mt=8\n\rThank You,\nSignTrack App Team");
                                $mail->setBodyHtml("Welcome,<BR><BR>you have been added as a Team Member to the SignTrack App campaign: ".$campaign->name.'.<BR><BR>Please download the app for iOS or Android and sign in using your email address and temporary password.<BR><BR>Email: '.$this->getRequest()->getParam('username').'<BR>Temporary Password: '.$pass.'<BR><BR>Android:<BR>https://play.google.com/store/apps/details?id=com.signtrackapp.mobile<BR><BR>iOS:<BR>https://itunes.apple.com/us/app/signtrack-app/id982585645?ls=1&mt=8<BR><BR>Thank You,<BR>SignTrack App Team');
                            }else{
                                $mail->setBodyText('Welcome,\n\nYou have been added as a '.$role.' to SignTrack App. Please click the following link to set your password: '.$link.'\n\nThank You,\nSignTrack App Team');
                                $mail->setBodyHtml('Welcome,<BR><BR>You have been added as a '.$role.' to SignTrack App.<BR><BR>Please click the following link to set your password:<BR>'.$link.'<BR><BR>Thank You,<BR>SignTrack App Team');
                            }
                            
                        }else{
                            //user already has an account and is being added to a new campaign
                            $mail->setBodyText("Welcome,\n\nYou have been added as a Team Member to the SignTrack App campaign: ".$campaign->name.".\n\nYou can select this campaign by updating the settings in your mobile app.\n\nThank You,\nSignTrack App Team");
                            $mail->setBodyHtml('Welcome,<BR><BR>You have been added as a Team Member to the SignTrack App campaign: '.$campaign->name.'.<BR><BR>You can select this campaign by updating the settings in your mobile app.<BR><BR>Thank You,<BR>SignTrack App Team');
                        }
                        $mail->setFrom(Zend_Registry::get('config')->sig->noreply, 'SignTrack App');
                        $mail->addTo($this->getRequest()->getParam('username'), $this->getRequest()->getParam('fname')." ".$this->getRequest()->getParam('lname'));
                        $mail->setSubject('Account Access');
                        $mail->send();
                    }
                break;   
            
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

}

