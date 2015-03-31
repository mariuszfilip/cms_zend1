<?php

class ContentController extends Zend_Controller_Action
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
    	     
    	    $oContentModel = new Application_Model_DbTable_Content();
    	    $select = $oContentModel->select()->setIntegrityCheck(false);
    	    $select->distinct(true);
    	    $select->from('cms_content');
    	    $select->where('cms_content.deleted=?',0);
    	    $select->where('cms_content.lang=?',$localeValue);
    	    $select->joinLeft('cms_lang_list', 'cms_content.lang = cms_lang_list.id', array('cms_lang_list.name as name_lang'));
    	    $select->joinLeft('cms_content_list', 'cms_content.id_content = cms_content_list.id', array('cms_content_list.name as name_content'));
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
       			    $select->where("cms_content.title like '%$searchString%'");
       			}
       			$filteredRows = $oContentModel->fetchAll($select);
       			 
       			$select->limit($limit, $offset);
       			$uSelect = $oContentModel->select()->setIntegrityCheck(false);
       			$uSelect->where('cms_content.deleted=?',0);
       			$uSelect->where('cms_content.lang=?',$localeValue);
       			$allRows = $oContentModel->fetchAll($uSelect);
       			 
       			$rows = $oContentModel->fetchAll($select);
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
       $oContentForm = new Application_Form_Content();
       $request = $this->getRequest();
       $this->view->title = "Dodaj nową aktualnosc";
       $this->view->description = "Tytuł ,treść status aktualnosci";
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oContentForm->isValid($request->getPost())){
                   $oContentModel = new Application_Model_DbTable_Content();
                   $data = array (
    					'title' => $request->getPost('title'),
                        'status' => $request->getPost('status'),
                   		'content' => stripslashes($request->getPost('content')),
                        'id_content' => $request->getPost('id_content'),
                        'lang' => $request->getPost('lang'),
                        'date_add' => new Zend_Db_Expr('NOW()'),
                    	'date_modify' => new Zend_Db_Expr('NOW()')
                    
                   );
                   $iContent = $oContentModel->addContent($data);
                   $this->_forward('index');
               }
           }
       }
    	$oContentForm->populate($oContentForm->getValues());
        $this->view->form = $oContentForm;
	
	}
	public function editAction(){
	   $oContentForm = new Application_Form_Content();
       $request = $this->getRequest();
       $id = $this->_getParam('id', 0);
       $oContentModel = new Application_Model_DbTable_Content();
       $this->view->title = "Edycja aktualnosci";
       $this->view->description = "Tytuł ,treść status aktualnosci";       
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oContentForm->isValid($request->getPost())){
                   
                   $data = array (
    					'title' => $request->getPost('title'),
                        'status' => $request->getPost('status'),
                   		'content' => stripslashes($request->getPost('content')),
                        'id_content' => $request->getPost('id_content'),
                        'lang' => $request->getPost('lang'),
                    	'date_modify' => new Zend_Db_Expr('NOW()')
                    
                   );
                   $oContentModel->updateContent($data,$id);
                   $this->_forward('index');
               }else{
                    $oPageForm->populate($oPageForm->getValues());
               }
           }
       }else{
           if($id > 0){
               $aContentModel = $oContentModel->getOneContent($id);
               $oContentForm->populate($aContentModel);
           }
            
       }

        $this->view->form = $oContentForm;
	
	
	}
	public function deleteAction(){
		 if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
    	    $this->_helper->viewRenderer->setNoRender(true);
		    $selected = explode(",", $this->getRequest()->getPost('selected'));
		    
		    $oContentModel = new Application_Model_DbTable_Content();
			foreach($selected as $id) {
				$removed += $oContentModel->deleteContent($id);
			}
			
			$answer = array('countRemoved' => $removed);
			echo Zend_Json::encode($answer);
			return;
		}	
	}
}
