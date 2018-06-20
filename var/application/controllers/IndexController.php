<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('dashboard', 'html')
                ->addActionContext('updateprop', 'html')
                ->addActionContext('checkavail', 'html')
                ->initContext();
    }

    public function indexAction()
    {
         $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $this->_redirect('login', array('UseBaseUrl' => true));
        }else{
            $this->_redirect('campaign', array('UseBaseUrl' => true));
        }
    }
}



