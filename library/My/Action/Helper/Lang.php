<?php
class My_Action_Helper_Lang extends Zend_Controller_Action_Helper_Abstract
{
    protected $view;

    public function preDispatch()
    {
        $controller = $this->getController();
        $helper =  $controller->getHelper('Layout');
		$layout = $helper->getLayoutInstance();
        if(!isset($layout->lang)){
            $oLang = new Application_Model_DbTable_Lang();
            $aLang = $oLang->getAllActiveLanguages();            
            $layout->lang = $aLang;
        }
    }

    public function getView()
    {
        if (null !== $this->view)
        {
            return $this->view;
        }
        $controller = $this->getActionController();
        $this->view = $controller->view;
        return $this->view;
    }
    public function getController()
    {
    	$controller = $this->getActionController();
    	return $controller;
    }
} 