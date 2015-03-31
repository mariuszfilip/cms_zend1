<?php

class NewsletterController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{
		$admin_session_space = new Zend_Session_Namespace('admin_space');
		if (!isset($admin_session_space) || !$admin_session_space->admin) {
	  			$this->_redirect('/auth/login');
		}
	}
	public function indexAction(){
	   if($this->_request->isXmlHttpRequest()) {
    	    $this->_helper->layout->disableLayout();
    	    $this->_helper->viewRenderer->setNoRender();
    	     
    	    $columnsMap = array(
    	    0 => 'id',
    	    1 => 'name',
    	    2 => 'status',
    	    3 => 'deleted'
    	    );
    	    
    	     
    	    $limit = $this->getRequest()->getParam('iDisplayLength', 20);
    	    $offset = $this->getRequest()->getParam('iDisplayStart', 0);
    	     
    	    $oNewsletterModel = new Application_Model_DbTable_Newsletter();
    	    $select = $oNewsletterModel->select()->setIntegrityCheck(false);
    	    $select->distinct(true);
    	    $select->from('cms_newsletter_email');
    	    $select->where('cms_newsletter_email.deleted=?',0);
    	    
            $sortingCols = $this->getRequest()->getParam('iSortingCols', 0);
       			if($sortingCols != 0) {
       			    $sortCol = (int)$this->getRequest()->getParam('iSortCol_0');
       			    if(!array_key_exists($sortCol, $columnsMap)) {
       			        $sortCol = 3;
       			    }
       			    $sortDir = $this->getRequest()->getParam('sSortDir_0');
       			    $select->order($columnsMap[$sortCol] .' '. $sortDir);
       			     
       			}
       			 
       			$searchString = $this->getRequest()->getParam('sSearch');
       			if(!empty($searchString)) {
       			    $select->where("cms_newsletter_email.email like '%$searchString%'");
       			}
       			$filteredRows = $oNewsletterModel->fetchAll($select);
       			 
       			$select->limit($limit, $offset);
       			$uSelect = $oNewsletterModel->select()->setIntegrityCheck(false);
       			$uSelect->where('cms_newsletter_email.deleted=?',0);
       			$allRows = $oNewsletterModel->fetchAll($uSelect);
       			 
       			$rows = $oNewsletterModel->fetchAll($select);
       			if($rows instanceof Zend_Db_Table_Rowset){
       			    $rows = $rows->toArray();
       			}else{
       			    $rows=array();
       			}
               
       			 
       			$answer['iTotalRecords'] = $allRows->count();
       			$answer['iTotalDisplayRecords'] = $filteredRows->count();
       			$answer['sEcho'] = (int)$this->getRequest()->getParam('sEcho');
       			$answer['aaData'] = $rows ? $rows : array();
       			 
       			echo Zend_Json::encode($answer);
	        }
	    
        
	}


}
