<?php

class LogController extends Zend_Controller_Action
{

    public function init()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('login', array('UseBaseUrl' => true));
        }
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('list', 'html')
                ->initContext();
    }

    public function indexAction()
    {
        
         $auth = Zend_Auth::getInstance();

        if ($auth->getIdentity()->campaign_id!=$this->getRequest()->getParam('id') && $auth->getIdentity()->campaign_id!=0) {
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/log.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.autotab-1.1b.js');
        $campaignTable = new Application_Model_DbTable_CampaignTable;
        $campaign= new Zend_Session_Namespace('campaign');
        $campaign->campaign_id =$this->getRequest()->getParam('id');
        $this->view->campaign = $campaignTable->getOneById($this->getRequest()->getParam('id'));
        $logTable  = new Application_Model_DbTable_LogTable();
        $this->view->users = $logTable->getUsersByCampaignId($this->getRequest()->getParam('id'));
    }
    
    public function listAction(){
        $campaign= new Zend_Session_Namespace('campaign');
        $logTable  = new Application_Model_DbTable_LogTable();
        $this->view->items =  $logTable->getAll($campaign->campaign_id);
    }
}



