<?php


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{


    protected function _initIdentity() {

        Zend_Registry::set('auth', Zend_Auth::getInstance());
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/../configs/config.ini', 'general');
        Zend_Registry::set('config', $config);


		$admin_session_space = new Zend_Session_Namespace('admin_space');
        if (isset($admin_session_space)) {
			Zend_Registry::set('username', $admin_session_space->admin->email);
            Zend_Registry::set('userId', $admin_session_space->admin->id);
            $_SESSION['ckeditor_base_url']=$config->ckeditor->base->url;
            $_SESSION['ckeditor_base_dir']=(realpath(APPLICATION_PATH.'/../'.$config->ckeditor->base->dir)).'/';
            $view->identity=true;
        } else {
            Zend_Registry::set('username', 'guest');
            $view->identity=false;
        }
    }
	 
	protected function _initForceSSL() {
	    /*
	    $isSSL = Zend_Registry::get('config')->force_ssl;
        if($isSSL && $_SERVER['SERVER_PORT'] != '443') {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit();
        }
        */
    }

    protected function _initDatabase() {
        $resource = $this->getPluginResource('db');
        $db = $resource->getDbAdapter();
        Zend_Registry::set("db", $db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
    }

    protected function _initAcl() {
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');
       // $acl = new My_Acl();
        //$front->registerPlugin(new My_Plugin_Acl($acl));
       // Zend_Registry::set('acl', $acl);
    }

    protected function _initJquery() {
        $view = $this->getResource('view');
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

    protected function _initList() {
        $view = $this->getResource('view');
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
	

    protected function _initConstants() {
        $registry = Zend_Registry::getInstance();
        $registry->constants = new Zend_Config($this->getApplication()->getOption('constants'));
    }

    protected function _initSession() {
        Zend_Session::setOptions();
        Zend_Session::start();
    }



}

