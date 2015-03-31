<?php

class IndexController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{
		
	}
	public function indexAction(){
	    $oPageModel = new Application_Model_DbTable_Page();
	    $aPageModel = $oPageModel->getGeneralPage();
	    if($aPageModel){
	         if($aPageModel['general'] == 1){
             		$this->_helper->layout->setLayout('layout_general');
           }
	        $this->view->page = $this->view->Page($aPageModel['id']);
	    }else{
	        $this->_forward('error');
	    }
	}
	public function errorAction(){
	
	}
}
