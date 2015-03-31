<?php


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{


    protected function _initIdentity() {

        Zend_Registry::set('auth', Zend_Auth::getInstance());
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/../configs/config.ini', 'general');
        Zend_Registry::set('config', $config);
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
    }

    protected function _initJquery() {
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



    /**
     * Initialize Locale and Translation
     *
     * @return void
     */
    public function _initLocale() {
        $request = new Zend_Controller_Request_Http();
        $localeValue = $request->getCookie('language');
        if (!$localeValue) {
             $localeValue = '1';
             setcookie("language", $localeValue, time()+(3600*24*60),  '/' );
             
        }
        $oLang= new Application_Model_DbTable_Lang();
        $aLang = $oLang->getOne($localeValue);
        $localeValue = $aLang['name_file'];
        $localeValue = My_Util::sanitize_file_name($localeValue);
        $locale = new Zend_Locale($localeValue);
        Zend_Registry::set('Zend_Locale', $locale);
        $translationFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR
                . $localeValue . '.ini';
        $translate = new Zend_Translate('ini', $translationFile, $localeValue);
        Zend_Registry::set('Zend_Translate', $translate);
    }

    /**
     * Initialize view
     *
     * @return void
     */
    public function _initViewFilter() {
        $view = $this->view;
        $view->addFilterPath('Zx/View/Filter', 'Zx_View_Filter');
        $view->setFilter('Translate');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
    }
	protected function _initHelper() {

            $autoloader = Zend_Loader_Autoloader::getInstance();
        	Zend_Controller_Action_HelperBroker::addHelper(new My_Action_Helper_Menu());
        	Zend_Controller_Action_HelperBroker::addHelper(new My_Action_Helper_Lang()); 
        	Zend_Controller_Action_HelperBroker::addHelper(new My_Action_Helper_Marquee());       
        	$autoloader->pushAutoloader(new My_Loader_Autoloader_PhpMailer());	
        
   
	}

}

