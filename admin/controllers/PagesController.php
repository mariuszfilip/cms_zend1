<?php

class PagesController extends Zend_Controller_Action
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
    	    
    	   if(isset($_SESSION['iDisplayLength'])){
               $limit = $_SESSION['iDisplayLength'];
    	       $offset = $_SESSION['iDisplayStart'];
    	       unset($_SESSION['iDisplayLength']); 
    	       unset($_SESSION['iDisplayStart']); 
           }else{
                $limit = $this->getRequest()->getParam('iDisplayLength', 20);
    	        $offset = $this->getRequest()->getParam('iDisplayStart', 0);    
           }
    	   	    
    	    

    	     
    	    $oPageModel = new Application_Model_DbTable_Page();
    	    $select = $oPageModel->select()->setIntegrityCheck(false);
    	    $select->distinct(true);
    	    $select->from('cms_pages_structure');
    	    $select->where('cms_pages_structure.deleted=?',0);
    	    $select->where('cms_pages_structure.lang=?',$localeValue);
    	    $select->joinLeft('cms_lang_list', 'cms_pages_structure.lang = cms_lang_list.id', array('cms_lang_list.name as name_lang'));
            $sortingCols = $this->getRequest()->getParam('iSortingCols', 0);
       			if($sortingCols != 0) {
       			    $sortCol = (int)$this->getRequest()->getParam('iSortCol_0');
       			    if(!array_key_exists($sortCol, $columnsMap)) {
       			        $sortCol = 3;
       			    }
       			    $sortDir = $this->getRequest()->getParam('sSortDir_0');
       			    $select->order($columnsMap[$sortCol] .' '. $sortDir);
       			     
       			}else{
       			    $select->order('cms_pages_structure.order desc');
       			}
       			 
       			$searchString = $this->getRequest()->getParam('sSearch');
       			if(!empty($searchString)) {
       			    $select->where("cms_pages_structure.name like '%$searchString%'");
       			}
       			$filteredRows = $oPageModel->fetchAll($select);
       			 
       			$select->limit($limit, $offset);
       			$uSelect = $oPageModel->select()->setIntegrityCheck(false);
       			$uSelect->where('cms_pages_structure.deleted=?',0);
       			$uSelect->where('cms_pages_structure.lang=?',$localeValue);
       			$uSelect->order('cms_pages_structure.order');
       			$allRows = $oPageModel->fetchAll($uSelect);
       			 
       			$rows = $oPageModel->fetchAll($select);
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
	   $this->view->title = "Dodaj strone";
       $this->view->description = "Nazwa,liczba kolumn , jezyk";       
       $this->view->headTitle($this->view->title);
       $oPageForm = new Application_Form_Page();
       $request = $this->getRequest();
    	
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oPageForm->isValid($request->getPost())){
                   $oPageModel = new Application_Model_DbTable_Page();
                   $name_link = $request->getPost('name');
                   $name_link = str_replace(" ", "_", $name_link);
                   $name_link = $this->remove_pl($name_link, 'utf8');
                   $name_link = strtolower($name_link);
                   $data = array (
    					'name' => $request->getPost('name'),
                        'status' => $request->getPost('status'),
                   		'order' => $request->getPost('order'),
                        'number_column' => $request->getPost('number_column'),
                    	'parent' => $request->getPost('parent'),
                        'lang' => $request->getPost('lang'),
                   		'show_in_footer' => $request->getPost('show_in_footer'),
                        'date_add' => new Zend_Db_Expr('NOW()'),
                    	'date_modify' => new Zend_Db_Expr('NOW()'),
                        'name_link'=>$name_link
                    
                   );
                   $iIdPage = $oPageModel->addPage($data);
                   if($request->getPost('general') == 1){
                       
                       $oPageModel->setGeneral($iIdPage,$request->getPost('lang'));
                   }
                   $iNumberColumn = $request->getPost('number_column');
                   $this->createStructurePage($iIdPage,$iNumberColumn);
                   $this->_forward('index');
               }
           }
       }
    	$oPageForm->populate($oPageForm->getValues());
        $this->view->form = $oPageForm;
	
	}
	public function editAction(){
	   $this->view->title = "Edycja strony";
       $this->view->description = "Nazwa,liczba kolumn , jezyk";       
       $this->view->headTitle($this->view->title);	    
	   $oPageForm = new Application_Form_Page();
       $request = $this->getRequest();
       $id = $this->_getParam('id', 0);
       $oPageModel = new Application_Model_DbTable_Page();

       if ($request->isPost()){
           if ($request->getPost()){
               if ($oPageForm->isValid($request->getPost())){
                   $name_link = $request->getPost('name');
                   $name_link = str_replace(" ", "_", $name_link);
                   $name_link = $this->remove_pl($name_link, 'utf8');
                   $name_link = strtolower($name_link);
                   $data = array (
    					'name' => $request->getPost('name'),
                        'status' => $request->getPost('status'),
                       'order' => $request->getPost('order'),
                        'number_column' => $request->getPost('number_column'),
                   		'show_in_footer' => $request->getPost('show_in_footer'),
                        'parent' => $request->getPost('parent'),
                        'lang' => $request->getPost('lang'),
                    	'date_modify' => new Zend_Db_Expr('NOW()'), 
                        'name_link'=>$name_link                
                    
                   );
                   $oPageModel->updatePage($data,$id);
                   $iNumberColumn = $request->getPost('number_column');
                   $this->createStructurePage($id,$iNumberColumn);
                   $this->_forward('index');
               }else{
                    $oPageForm->populate($oPageForm->getValues());
               }
           }
       }else{
           if($id > 0){
               $aPageModel = $oPageModel->getOnePage($id);
               $oPageForm->populate($aPageModel);
           }
            
       }

        $this->view->form = $oPageForm;
	
	
	
	}
	protected function createStructurePage($iPage,$iNumberColumn){
	                $data_update = array();
	                $oPageModel = new Application_Model_DbTable_Page();
	                if($iNumberColumn == 1){
                       $oPageContentModel = new Application_Model_DbTable_PageContent();
                       $data_update['id_center_column'] = $oPageContentModel->addPageContentPosition($iPage,'center');
                   }elseif($iNumberColumn == 2){
                       $oPageContentModel = new Application_Model_DbTable_PageContent();
                       $data_update['id_left_column'] = $oPageContentModel->addPageContentPosition($iPage,'left');
                       $data_update['id_right_column'] = $oPageContentModel->addPageContentPosition($iPage,'right');
                   }elseif($iNumberColumn == 3){
                       $oPageContentModel = new Application_Model_DbTable_PageContent();
                       $data_update['id_left_column'] = $oPageContentModel->addPageContentPosition($iPage,'left');
                       $data_update['id_right_column'] = $oPageContentModel->addPageContentPosition($iPage,'right');                   
                       $data_update['id_center_column'] = $oPageContentModel->addPageContentPosition($iPage,'center');
                   }
                   $oPageModel->updatePage($data_update, $iPage);
	              
	}
	public function editcontentAction(){
	   $limit = $this->getRequest()->getParam('iDisplayLength', 20);
       $offset = $this->getRequest()->getParam('iDisplayStart', 0);     
       if(!isset($_SESSION['iDisplayLength'])){
           $_SESSION['iDisplayLength']= $limit;
	       $_SESSION['iDisplayStart']= $offset;       
       }

	    
	   $this->view->title = "Edycja strony - content";
       $this->view->description = "Zawartosc strony";       
       $this->view->headTitle($this->view->title);		    
	    $oPageContent = new Application_Model_DbTable_PageContent();
	    $id = $this->_getParam('id', 0);
	    if($id > 0){
	        $aPageContent = $oPageContent->getOnePageContent($id);
	        if($aPageContent){
	            $oPageContentForm = new Application_Form_PageContent();
	            $this->view->form = $oPageContentForm;
	            $request = $this->getRequest();
	            if ($request->isPost()){
	                if ($request->getPost()){
	                    if ($oPageContentForm->isValid($request->getPost())){
                             $data = array (
    							'title' => $request->getPost('title'),
                        		'content' => stripslashes($request->getPost('content'))                   
                            );
	                        $oPageContent->updatePageContent($data,$id);
	                        $this->_forward('index');
	                    }else{
	                        $oPageContentForm->populate($oPageContentForm->getValues());
	                    }
	                }
	            }else{
	                 $oPageContentForm->populate($aPageContent);
	            }
	        
	        }
	    }
	
	
	
	
	}
	public function deleteAction(){
		 if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
    	    $this->_helper->viewRenderer->setNoRender(true);
		    $selected = explode(",", $this->getRequest()->getPost('selected'));
		    
		    $oPageModel = new Application_Model_DbTable_Page();
			foreach($selected as $id) {
				$removed += $oPageModel->deletePage($id);
			}
			
			$answer = array('countRemoved' => $removed);
			echo Zend_Json::encode($answer);
			return;
		}	
	}
    protected function remove_pl($text,$from) {
    	if($from == 'utf8') {
    		$from = array(
    			"\xc4\x85", "\xc4\x87", "\xc4\x99",
    			"\xc5\x82", "\xc5\x84", "\xc3\xb3",
    			"\xc5\x9b", "\xc5\xba", "\xc5\xbc",
    			"\xc4\x84", "\xc4\x86", "\xc4\x98",
    			"\xc5\x81", "\xc5\x83", "\xc3\x93",
    			"\xc5\x9a", "\xc5\xb9", "\xc5\xbb",
    		);
    	}elseif($from == 'latin2') {
    		$from = array(
    			"\xb1", "\xe6", "\xea",
    			"\xb3", "\xf1", "\xf3",
    			"\xb6", "\xbc", "\xbf",
    			"\xa1", "\xc6", "\xca",
    			"\xa3", "\xd1", "\xd3",
    			"\xa6", "\xac", "\xaf", 
    		);
    	}elseif($from == 'cp1250') {
    		$from = array(
    			"\xb9", "\xe6", "\xea",
    			"\xb3", "\xf1", "\xf3",
    			"\x9c", "\x9f", "\xbf",
    			"\xa5", "\xc6", "\xca",
    			"\xa3", "\xd1", "\xd3",
    			"\x8c", "\x8f", "\xaf",
    		);
    	}
    	$clear = array(
    		"\x61", "\x63", "\x65",
    		"\x6c", "\x6e", "\x6f",
    		"\x73", "\x7a", "\x7a",
    		"\x41", "\x43", "\x45",
    		"\x4c", "\x4e", "\x4f",
    		"\x53", "\x5a", "\x5a",
    	);
    	if(is_array($text)) {
    		foreach($text as $key => $value) {
    			$array[str_replace($from, $clear, $key)]= str_replace($from, $clear, $value);
    		}
    		return $array;
    	}else {
    		return str_replace($from, $clear, $text);
    	}
    }
}
