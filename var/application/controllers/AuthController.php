<?php

class AuthController extends Zend_Controller_Action {

    public function init() {
        
    }

    public function loginAction() {

        //if this is a post
        if ($this->getRequest()->isPost() && $this->getRequest()->getParam('username') != '' && $this->getRequest()->getParam('password') != '') {
            if ($this->_process($this->getRequest())) {
                $auth = Zend_Auth::getInstance();
                if ($auth->getIdentity()->role == "Admin") {
                    $campaignTable = new Application_Model_DbTable_CampaignTable;
                    $campaign = $campaignTable->getOneById($auth->getIdentity()->campaign_id);
                    if ($campaign['name'] == '') {
                        $this->_redirect('auth/setup', array('UseBaseUrl' => true));
                    } else {
                        $this->_redirect('campaign/' . $auth->getIdentity()->campaign_id . '/dashboard', array('UseBaseUrl' => true));
                    }
                } else if ($auth->getIdentity()->role == "Master") {
                    $this->_redirect('campaign', array('UseBaseUrl' => true));
                } else {
                    $this->view->msg = '<div class="alert alert-error">Sorry, you are not authorized to use the admin system.</div>';
                }
            } else {
                $this->view->msg = '<div class="alert alert-error">Email and password were not recognized.</div>';
            }
        } else {

            $auth = Zend_Auth::getInstance();
            //redirect to appropriate screen if credentials are stored in cookie
            if ($auth->hasIdentity()) {
                if ($auth->getIdentity()->role == "admin") {
                    $this->_redirect('campaign', array('UseBaseUrl' => true));
                } else {
                    $this->_redirect('campaign', array('UseBaseUrl' => true));
                }
            }
        }
    }

    public function setupAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/setup.js');
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('login', array('UseBaseUrl' => true));
        }
    }

    public function resetAction() {

        $verify_hash = $this->_request->getParam('h');
        $this->view->mode = "send_email";
        $this->view->error_msg = "";
        if ($verify_hash) {
            $decoded_hash = base64_decode($verify_hash);
            $parts = explode('@1@', $decoded_hash);
            $url = $this->view->domain . $_SERVER['REQUEST_URI'];
            if (sizeof($parts) == 3 && !preg_match('/\D/', $parts[0]) && strlen($parts[1]) == 32 && strlen($parts[2]) == 13) {

                $usertable = new Application_Model_DbTable_UserTable();
                $exptime = strtotime(substr($parts[0], strlen(substr($parts[0], 0, -4))) . '/01/01 +' . substr($parts[0], 0, -4) . ' days');
                $userDetails = $usertable->getUserByEmail($parts[1], true);


                //check if link is expired or already used
                if (time() <= $exptime && $parts[2] == substr($userDetails['salt'], 0, 13)) {
                    //check if user exists
                    if ($userDetails) {
                        if ($this->getRequest()->isPost()) {

                            if ($usertable->resetPassword($this->getRequest()->getParam('password'), $this->getRequest()->getParam('username')))
                                $this->view->mode = "complete";
                        }else {

                            $this->view->mode = "reset";
                            $this->view->username = $userDetails['username'];
                        }
                    } else {
                        $this->view->mode = "invalid";
                        $this->view->msg = '<div class="alert alert-error" style="margin-top:10px;margin-bottom:0px;">The URL you entered is not valid. If you pasted the URL into the browser, please ensure you copied all characters correctly.</div>';
                    }
                } else {
                    $this->view->mode = "expired";
                    $this->view->msg = '<div class="alert alert-error" style="margin-top:10px;margin-bottom:0px;">This link to reset your password has expired.</div>';
                }
            } else {
                $this->view->mode = "invalid";
                $this->view->msg = '<div class="alert alert-error" style="margin-top:10px;margin-bottom:0px;">The URL is not formatted correctly. If you pasted the URL into the browser, please ensure you copied all characters correctly.</div>';
            }
        } else if ($this->getRequest()->isPost()) {
            $usertable = new Application_Model_DbTable_UserTable();
            $user = $usertable->getUserByEmail($this->getRequest()->getParam('username'), false);

            if ($user) {
                $this->view->mode = "reset";
                $link = base64_encode(date('zY', strtotime('00:00:00 +14 day')) . '@1@' . md5($user['username']) . '@1@' . substr($user['salt'], 0, 13));
                $link = $this->view->serverUrl() . $this->view->baseUrl() . '/reset?h=' . $link;
                $mail = new Zend_Mail();
                $mail->setBodyText('You are receiveing this email because you requested to change your password. Please click the following link to reset your password: ' . $link);
                $mail->setBodyHtml('You are receiveing this email because you requested to change your password.<BR><BR>Please click the following link to reset your password:<BR>' . $link);
                $mail->setFrom(Zend_Registry::get('config')->sig->noreply, 'SignTrack App');
                $mail->addTo($user['username'], $user['fname'] . " " . $user['lname']);
                $mail->setSubject('Reset Password');

                $mail->send();
                $this->view->error_msg = "email sent";
                $this->view->output = $user['fname'] . " : " . $user['lname'] . " : " . $user['username'] . " : " . $link;
            } else {
                $this->view->msg = '<div class="alert alert-error" style="margin-top:10px;margin-bottom:0px;">The email entered was not recognized.</div>';
            }
        }
    }

    protected function _getAuthAdapter() {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('user')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password')
                ->setCredentialTreatment('SHA1(CONCAT(?,salt))');

        return $authAdapter;
    }

    //process login
    protected function _process($values) {

        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values->getParam('username'));
        $adapter->setCredential($values->getParam('password'));

        $auth = Zend_Auth::getInstance();
        $usertable = new Application_Model_DbTable_UserTable();

        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {

            //store session credentials
            $user = $adapter->getResultRowObject();
            $campaign = $usertable->getUserById($user->user_id);
            $user->role = $campaign['role'];
            $user->campaign_id = $campaign['campaign_id'];
            $auth->getStorage()->write($user);

            //store permanent cookie if user selected "Remember Me"
            Zend_Session::rememberMe();

            return true;
        }
        return false;
    }

    //when logout button is clicked
    public function logoutAction() {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        if ($auth = Zend_Auth::getInstance()) {

            Zend_Auth::getInstance()->clearIdentity();
            Zend_Session::forgetMe();
            Zend_Session::namespaceUnset('property');
        }
        $this->_helper->redirector('login'); // back to login page
    }

}
