<?php

class Zend_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract 

{

    public function loggedInAs ()

    {

        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {

            //$username = $auth->getIdentity()->username;
            //$role = $auth->getIdentity()->role;
            
            //return $username .  ': ' . $role . ' - <a href="'.$logoutUrl.'">Logout</a>';
            if(isset($auth->getIdentity()->username)){
                $extra= '';
                if($auth->getIdentity()->role=='Master'){
                    $extra= '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'.$this->view->baseUrl().'/campaign/">Master Admin</a>';
                }
                $html = '<div class="logged"><strong>'. $auth->getIdentity()->fname .' '. $auth->getIdentity()->lname .'</strong>'.$extra.'&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="'.$this->view->baseUrl().'/docs/SignTrack_UserGuide_v1.0.pdf">User Guide</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'.$this->view->baseUrl().'/logout">Logout</a></div>';
                
                return $html;
                //return "Welcome <strong>" . $auth->getIdentity()->username . '</strong> | <a href="'.$logoutUrl.'">Logout</a><span style="color:#CCC;position:absolute;right:100px;top:-5px;font-size:36px;width:400px;">'.$auth->getIdentity()->property_name.'</span>';
            }else{
                $user = new Zend_Session_Namespace('user');
                return "Welcome " . $user->username . ' <a href="'.$this->view->baseUrl().'/logout">Logout</a>';
            }
            
        } 



        $request = Zend_Controller_Front::getInstance()->getRequest();

        $controller = $request->getControllerName();

        $action = $request->getActionName();

        if($controller == 'auth' && $action == 'index') {

            return '';

        }



        return '<a href="'.$this->view->baseUrl().'/login">Login</a>';

    }

}
?>
