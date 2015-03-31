<?php
class My_Action_Helper_Marquee extends Zend_Controller_Action_Helper_Abstract
{
    protected $view;

    public function preDispatch()
    {
        $controller = $this->getController();	    
		$helper =  $controller->getHelper('Layout');
		$layout = $helper->getLayoutInstance();
		if(!isset($layout->marquee)){   
		    $oExchangeFilesNotification = new Application_Model_DbTable_ExchangeFilesNotification();  
            $aResult =  $oExchangeFilesNotification->getFileList();
            $layout->marquee = $aResult;   
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