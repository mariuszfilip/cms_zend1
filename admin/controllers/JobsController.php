<?php

class JobsController extends Zend_Controller_Action
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
	    $oLang = new Application_Model_DbTable_Lang();
        $aLang = $oLang->getAllActiveLanguages();
	    $this->view->languages = $aLang; 
	    
		$lang =$this->getRequest()->getParam('lang');
	    if($lang){
	        $localeValue = $lang;
	    }else{
	        $localeValue = 1;
	    }
	    $localeValue = intval($localeValue);
	   $this->view->lang = $localeValue;
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
    	     
    	    $oOfferJobModel = new Application_Model_DbTable_OfferJob();
    	    $select = $oOfferJobModel->select()->setIntegrityCheck(false);
    	    $select->distinct(true);
    	    $select->from('cms_offer_jobs');
    	    $select->where('cms_offer_jobs.deleted=?',0);    
    	    $select->where('cms_offer_jobs.lang=?',$localeValue);	    
    	    $select->joinLeft('cms_lang_list', 'cms_offer_jobs.lang = cms_lang_list.id', array('cms_lang_list.name as name_lang'));
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
       			    $select->where("cms_offer_jobs.title like '%$searchString%'");
       			}
       			$filteredRows = $oOfferJobModel->fetchAll($select);
       			 
       			$select->limit($limit, $offset);
       			$uSelect = $oOfferJobModel->select()->setIntegrityCheck(false);
       			$uSelect->where('cms_offer_jobs.deleted=?',0);
       			$uSelect->where('cms_offer_jobs.lang=?',$localeValue);
       			$allRows = $oOfferJobModel->fetchAll($uSelect);
       			 
       			$rows = $oOfferJobModel->fetchAll($select);
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
	public function addAction(){
       $oOfferJobForm = new Application_Form_OfferJob();
       $request = $this->getRequest();
       $this->view->title = "Dodaj nową oferte pracy";
       $this->view->description = "Tytuł ,treść status";
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oOfferJobForm->isValid($request->getPost())){
                   $oOfferJobModel = new Application_Model_DbTable_OfferJob();
                   $data = array (
    					'title' => $request->getPost('title'),
                        'status' => $request->getPost('status'),
                   		'content' => stripslashes($request->getPost('content')),
                        'short_content' => stripslashes($request->getPost('short_content')),
                        'lang' => $request->getPost('lang'),
                        'date_add' => new Zend_Db_Expr('NOW()'),
                    	'date_modify' => new Zend_Db_Expr('NOW()')
                    
                   );
                   $iJob = $oOfferJobModel->addOfferJob($data);
                   $this->_forward('index');
               }
           }
       }
    	$oOfferJobForm->populate($oOfferJobForm->getValues());
        $this->view->form = $oOfferJobForm;
	
	}
	public function editAction(){
	   $oOfferJobForm = new Application_Form_OfferJob();
       $request = $this->getRequest();
       $id = $this->_getParam('id', 0);
       $oOfferJobModel = new Application_Model_DbTable_OfferJob();
       $this->view->title = "Edycja aktualnosci";
       $this->view->description = "Tytuł ,treść status aktualnosci";       
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oOfferJobForm->isValid($request->getPost())){
                   
                   $data = array (
    					'title' => $request->getPost('title'),
                        'status' => $request->getPost('status'),
                   		'content' => stripslashes($request->getPost('content')),
                       'short_content' => stripslashes($request->getPost('short_content')),
                        'lang' => $request->getPost('lang'),
                        'date_add' => new Zend_Db_Expr('NOW()'),
                    	'date_modify' => new Zend_Db_Expr('NOW()')
                    
                   );
                   $oOfferJobModel->updateOfferJob($data,$id);
                   $this->_forward('index');
               }else{
                    $oOfferJobForm->populate($oOfferJobForm->getValues());
               }
           }
       }else{
           if($id > 0){
               $aOfferJobModel = $oOfferJobModel->getOneOfferJob($id);
               $oOfferJobForm->populate($aOfferJobModel);
           }
            
       }

        $this->view->form = $oOfferJobForm;
	
	
	}
	public function deleteAction(){
		 if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
    	    $this->_helper->viewRenderer->setNoRender(true);
		    $selected = explode(",", $this->getRequest()->getPost('selected'));
		    
		    $oOfferJobModel = new Application_Model_DbTable_OfferJob();
			foreach($selected as $id) {
				$removed += $oOfferJobModel->deleteOfferJob($id);
			}
			
			$answer = array('countRemoved' => $removed);
			echo Zend_Json::encode($answer);
			return;
		}	
	}
}
