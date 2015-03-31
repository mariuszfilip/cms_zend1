<?php

class PageController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{
	}
	public function indexAction(){
         $name = $this->_getParam('name');
         $oPageModel = new Application_Model_DbTable_Page();
         $aPageModel = $oPageModel->getPageByName($name);
         if($aPageModel){
             $authNamespace = new Zend_Session_Namespace('cms_space');
             $authNamespace->page=$aPageModel;
             $this->view->page = $this->view->Page($aPageModel['id']);
             $this->view->headTitle($aPageModel['name']);
             $this->view->general = $aPageModel['general'];
			 if($aPageModel['general'] == 1){
             		$this->_helper->layout->setLayout('layout_general');
          		}
         }else{
             $this->_forward('error');
         }
	}
	
	public function errorAction(){
	
	
	}
}
