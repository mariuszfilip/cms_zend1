<?php

class FilesController extends Zend_Controller_Action
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
    	     
    	    $oFileModel = new Application_Model_DbTable_Files();
    	    $select =  $oFileModel->select()->setIntegrityCheck(false);
    	    $select->distinct(true);
    	    $select->from('cms_files_page');
    	    $select->where('cms_files_page.deleted=?',0);
    	    $select->where('cms_files_page.lang=?',$localeValue);
    	    $select->joinLeft('cms_lang_list', 'cms_files_page.lang = cms_lang_list.id', array('cms_lang_list.name as name_lang'));
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
       			    $select->where("cms_files_page.name like '%$searchString%'");
       			}
       			$filteredRows = $oFileModel->fetchAll($select);
       			 
       			$select->limit($limit, $offset);
       			$uSelect = $oFileModel->select()->setIntegrityCheck(false);
       			$uSelect->where('cms_files_page.lang=?',$localeValue);
       			$uSelect->where('cms_files_page.deleted=?',0);
       			$allRows = $oFileModel->fetchAll($uSelect);
       			 
       			$rows = $oFileModel->fetchAll($select);
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
       $oFilesForm = new Application_Form_Files();
       $request = $this->getRequest();
       $this->view->title = "Dodaj nowy plik";
       $this->view->description = "Pdf , excel ..";
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oFilesForm->isValid($request->getPost())){
                   $oFilesModel = new Application_Model_DbTable_Files();
               	    try{
            			$upload = new Zend_File_Transfer_Adapter_Http();
            			$pathparts = pathinfo($upload->getFileName());
						$originalFilename = $pathparts['basename'];
            			$filter = new My_FileFilter();
            			$file_name = $filter->filter($originalFilename);
            			$upload->addFilter('Rename', array('target' => APPLICATION_PATH.'/../public/files/'.$file_name,'overwrite' => false));     
						$upload->receive();
						$oFile= new Application_Model_DbTable_Files();
                        $data = array (
        					'name' => $request->getPost('name'),
                            'name_file' => $file_name,
                       		'description' => $request->getPost('description'),
                            'status' => $request->getPost('status'),
                        	'lang' => $request->getPost('lang'),
                        	'id_cms_pages_structure' => $request->getPost('id_cms_pages_structure'),
                            'date_add' => new Zend_Db_Expr('NOW()')
                        
                       );
						$oFile->addFile($data);
						
            		}
            		catch (Exception $e) {
        			}
                   $this->_forward('index');
               }
           }
       }
    	$oFilesForm->populate($oFilesForm->getValues());
        $this->view->form = $oFilesForm;
	
	}
	public function editAction(){
	   $oFilesForm = new Application_Form_Files();
	   $oFilesForm->setActionController('edit');
       $request = $this->getRequest();
       $id = $this->_getParam('id', 0);
       $oFilesModel = new Application_Model_DbTable_Files();
       $this->view->title = "Edycja pliku";
       $this->view->description = "Nazwa , opis  ...";       
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oFilesForm->isValid($request->getPost())){
                   
                   $data = array (
        					'name' => $request->getPost('name'),
                       		'description' => $request->getPost('description'),
                            'status' => $request->getPost('status'),
                        	'lang' => $request->getPost('lang'),
                        	'id_cms_pages_structure' => $request->getPost('id_cms_pages_structure'),
                    
                   );
                   $oFilesModel->updateFile($data,$id);
                   $this->_forward('index');
               }else{
                    $oFilesForm->populate($oFilesForm->getValues());
               }
           }
       }else{
           if($id > 0){               
               $aFilesModel = $oFilesModel->getOneFile($id);
               $oFilesForm->populate($aFilesModel);
           }    
            
       }

        $this->view->form = $oFilesForm;
	
	
	}
	public function deleteAction(){
		 if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
    	    $this->_helper->viewRenderer->setNoRender(true);
		    $selected = explode(",", $this->getRequest()->getPost('selected'));
		    
		    $oFilesModel = new Application_Model_DbTable_Files();
			foreach($selected as $id) {
			    $aFilesModel = $oFilesModel->getOneFile($id);
			    $link = realpath(APPLICATION_PATH.'/../public/files/'.$aFilesModel['name_file']);
			    unlink($link);
				$removed += $oFilesModel->deleteFile($id);
			}
			
			$answer = array('countRemoved' => $removed);
			echo Zend_Json::encode($answer);
			return;
		}	
	}
}
