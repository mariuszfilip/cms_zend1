<?php

class UsersController extends Zend_Controller_Action
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
    	     
    	    $oUserModel = new Application_Model_DbTable_User();
    	    $select = $oUserModel->select()->setIntegrityCheck(false);
    	    $select->distinct(true);
    	    $select->from('cms_users');
    	    $select->where('cms_users.deleted=?',0);
    	    
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
       			    $select->where("cms_users.first_name like '%$searchString%' or cms_users.last_name like '%$searchString%'");
       			}
       			$filteredRows = $oUserModel->fetchAll($select);
       			 
       			$select->limit($limit, $offset);
       			$uSelect = $oUserModel->select()->setIntegrityCheck(false);
       			$uSelect->where('cms_users.deleted=?',0);
       			$allRows = $oUserModel->fetchAll($uSelect);
       			 
       			$rows = $oUserModel->fetchAll($select);
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
       $oUserForm = new Application_Form_User();
       $request = $this->getRequest();
       $this->view->title = "Dodaj nowego uzytkownika";
       $this->view->description = "Status, email , haslo ...";
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oUserForm->isValid($request->getPost())){
                   $oUserModel = new Application_Model_DbTable_User();
                   $password = $request->getPost('password');
                   $data = array (
    					'email' => $request->getPost('email'),
                        'first_name' => $request->getPost('first_name'),
                        'last_name' => $request->getPost('last_name'),
                   	    'password' => md5($password),
                        'status' => $request->getPost('status'),
                        'date_add' => new Zend_Db_Expr('NOW()')
                    
                   );
                   $iUser = $oUserModel->addUser($data);
                   $this->_forward('index');
               }
           }
       }
    	$oUserForm->populate($oUserForm->getValues());
        $this->view->form = $oUserForm;
	
	}
	public function editAction(){
	   $oUserForm = new Application_Form_User();
       $request = $this->getRequest();
       $id = $this->_getParam('id', 0);
       $oUserModel = new Application_Model_DbTable_User();
       $this->view->title = "Edycja uzytkownika";
       $this->view->description = "Status, email , haslo ...";       
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oUserForm->isValid($request->getPost())){
                   
                   $data = array (
    					'email' => $request->getPost('email'),
                        'first_name' => $request->getPost('first_name'),
                        'last_name' => $request->getPost('last_name'),
                        'status' => $request->getPost('status'),
                    
                   );
                   $oUserModel->updateUser($data,$id);
                   $this->_forward('index');
               }else{
                    $oUserForm->populate($oUserForm->getValues());
               }
           }
       }else{
           if($id > 0){
               $aUserModel = $oUserModel->getOneUser($id);
               $oUserForm->populate($aUserModel);
           }
            
       }

        $this->view->form = $oUserForm;
	
	
	}
	public function deleteAction(){
		 if ($this->getRequest()->isXmlHttpRequest()) {
			$this->_helper->layout()->disableLayout();
    	    $this->_helper->viewRenderer->setNoRender(true);
		    $selected = explode(",", $this->getRequest()->getPost('selected'));
		    
		    $oUserModel = new Application_Model_DbTable_User();
			foreach($selected as $id) {
				$removed += $oUserModel->deleteUser($id);
			}
			
			$answer = array('countRemoved' => $removed);
			echo Zend_Json::encode($answer);
			return;
		}	
	}
	public function changepasswordAction(){
	   $oPasswordForm = new Application_Form_Password();
       $request = $this->getRequest();
       $id = $this->_getParam('id', 0);
       $oUserModel = new Application_Model_DbTable_User();
       $this->view->title = "Edycja hasła użytkownika";
       $this->view->description = "Wpisz hasło";       
       $this->view->headTitle($this->view->title);
       if ($request->isPost()){
           if ($request->getPost()){
               if ($oPasswordForm->isValid($request->getPost())){
                   
                   $data = array (
    					'password' => $request->getPost('password'),
                    
                   );
                   $oUserModel->updateUser($data,$id);
                   $this->_forward('index');
               }else{
                    $oPasswordForm->populate($oPasswordForm->getValues());
               }
           }
       }

        $this->view->form = $oPasswordForm;
	
	
	
	}
}
