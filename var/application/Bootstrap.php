<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    protected function _init()
    {
        //Zend_Session::setOptions(array('remember_me_seconds' => 60*60*24*180));
        $configuration = new Zend_Config_Ini('config.ini', 'live');
        Zend_Session::setOptions($configuration->sessions->toArray());
    }
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
        return $config;
    }
    protected function _initAction()
    {
       
    }
    protected function _initAutoloading()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(new My_Loader_Autoloader_PHPExcel());
    }

    protected function _initRouterEngine()
    {

        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();  
        
        $route = new Zend_Controller_Router_Route_Regex(
                '(login|reset|logout|rpc)',
                        array(
                            'controller'=> 'auth',
                            'action'    => 'index',
                        ),array(
                            1 => 'action',
                        ),
                        '%s'
                );

     $router->addRoute('auth-actions', $route);
     
     $route = new Zend_Controller_Router_Route_Regex(
            'campaign/(\d+)/dashboard',
            array(
                'controller'    => 'campaign',
                'action'    => 'dashboard',
                                'id'            => NULL
            ),array(
                1=>'id'
            ),
            'campaign/%s/dashboard'
        );
       
     $router->addRoute('dashboard-actions', $route);
     
     $route = new Zend_Controller_Router_Route_Regex(
            'campaign/(\d+)/sign',
            array(
                'controller'    => 'sign',
                'action'    => 'index',
                                'id'            => NULL
            ),array(
                1=>'id'
            ),
            'campaign/%s/sign'
        );
       
     $router->addRoute('sign-actions', $route);
     
     $route = new Zend_Controller_Router_Route_Regex(
            'campaign/(\d+)/log',
            array(
                'controller'    => 'log',
                'action'    => 'index',
                                'id'            => NULL
            ),array(
                1=>'id'
            ),
            'campaign/%s/log'
        );
       
     $router->addRoute('log-actions', $route);
     
     $route = new Zend_Controller_Router_Route_Regex(
            'campaign/(\d+)/user',
            array(
                'controller'    => 'user',
                'action'    => 'index',
                                'id'            => NULL
            ),array(
                1=>'id'
            ),
            'campaign/%s/user'
        );
       
     $router->addRoute('user2-actions', $route);
//        $route = new Zend_Controller_Router_Route_Regex(
//            'user/handler/(\d+)/(\d+)/format/html',
//            array(
//                'controller'    => 'user',
//                'action'    => 'handler',
//                                'type'       => NULL,
//                'id'       => NULL
//            ),array(
//                1=>'type',
//                2=>'id'
//            ),
//            'user/handler/edit/(\d+)/format/html'
//        );
//        $router->addRoute('user-edit-actions', $route);

    }

}

