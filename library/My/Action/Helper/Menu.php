<?php
class My_Action_Helper_Menu extends Zend_Controller_Action_Helper_Abstract
{
    protected $view;

    public function preDispatch()
    {
        $controller = $this->getController();	    
		$helper =  $controller->getHelper('Layout');
		$layout = $helper->getLayoutInstance();
		if(!isset($layout->menu)){
		    $oPage = new Application_Model_DbTable_Page();
            $aPage = $oPage->getMenu();
            $aPageFooter = $oPage->getFooterMenu();
    		$layout->menu = $aPage;
    		$layout->menufooter = $aPageFooter;
    		
    		$request = $this->getRequest();
    		$name = $request->getParam('name');
    		if($name){
    		     $aBreadcrumb = array();
                 $oPageModel = new Application_Model_DbTable_Page();
                 $aBreadcrumb = $oPageModel->getBreadCrumb($name);
                 $aBreadcrumb = array_reverse($aBreadcrumb);
                 $action =Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                 if($action == 'contentpage'){
                     $id = $request->getParam('id');
                     $oContent = new Application_Model_DbTable_Content();
    	             $aContent = $oContent->getOneContent($id);
    	             if($aContent){
    	                 $aBreadcrumb[] = $aContent['title'];
    	             }
                 }elseif($action == 'jobspage'){
                     $view = $this->getView();
                     $aBreadcrumb[] = $view->translate('list.jobs');
                 }elseif($action == 'applyjobpage'){
                     $view = $this->getView();
                     $aBreadcrumb[] = $view->translate('list.jobs');
                     $aBreadcrumb[] = $view->translate('apply.offer.job');
                 }    
                 $layout->breadcrumb = $aBreadcrumb;
		    }
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