<?php

class AuthController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{
	}
	public function indexAction(){
	    $this ->_helper->layout()->setLayout("layout_login");
	}
	
	public function loginAction(){
	    $this ->_helper->layout()->setLayout("layout_login");
	    $oAuthForm = new Application_Form_Auth();

        $this->view->title = 'Logowanie do panelu admina';
        $this->view->headTitle($this->view->title);

        $request = $this->getRequest();

      	$oUserModel = new Application_Model_DbTable_User();
        if ($request->isPost()) {

            if (isset($_POST['email']) && $oAuthForm->isValid($_POST)) {
                $adapter = new Zend_Auth_Adapter_DbTable($oUserModel->getAdapter());

                $adapter->setTableName('cms_users');
                $adapter->setIdentityColumn('email');
                $adapter->setCredentialColumn('password');
                $adapter->setCredentialTreatment('md5(?)');
                //$this->config = Zend_Registry::get('config');
                //$hash_password = md5($this->config->hash->password->grain).md5($oAuthForm->getValue('password'));
                $adapter->setIdentity($oAuthForm->getValue('email'));
                $adapter->setCredential($oAuthForm->getValue('password'));

                $auth = Zend_Registry::get('auth');
                $result = $auth->authenticate($adapter);

                switch ($result->getCode()) {

                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        $this->view->error = 'Nie znaleziono uzytkownika.';
                        break;

                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        $this->view->error = 'Nie prawidłowe hasło.';
                        break;

                    case Zend_Auth_Result::SUCCESS:
                        $rUser = $adapter->getResultRowObject(null, 'password');

                        $authNamespace = new Zend_Session_Namespace('admin_space');  
                        $authNamespace->admin=$rUser;
                              
                        Zend_Registry::set('groupAcl','super_admin');
                        $this->_redirect('/index/');
                        
                        break;

                    default:
                        $this->view->error = 'Złe hasło lub login';
                        break;
                }
            }
        }

       $this->view->form = $oAuthForm;
	
	}
	
    public function logoutAction() {
    	$authNamespace = new Zend_Session_Namespace('admin_space'); 
    	unset($authNamespace->admin);
        Zend_Registry::get('auth')->clearIdentity();
        $this->_redirect('/auth/login');
    }
	
	
	

}
