<?php

class SettingsController extends Zend_Controller_Action
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
	    $id = 1;
	    $oSettingsModel = new Application_Model_DbTable_SettingsPage();
	    $aSettingsModel = $oSettingsModel->getOne($id);
	    $this->view->settings = $aSettingsModel;
        
	}
	public function editAction(){
	    $id = 1;
	   $this->view->title = "Ustawienia strony";
       $this->view->description = "";       
       $this->view->headTitle($this->view->title);	    
	   $oSettingsForm = new Application_Form_Settings();
       $request = $this->getRequest();
       $oSettingsModel = new Application_Model_DbTable_SettingsPage();

       if ($request->isPost()){
           if ($request->getPost()){
               if ($oSettingsForm->isValid($request->getPost())){
                   
                   $data = array (
    					'email_contact_form' => $request->getPost('email_contact_form')
                    
                   );
                   $oSettingsModel->updateSettings($data,$id);
                   $this->_forward('index');
               }else{
                    $oSettingsForm->populate($oSettingsForm->getValues());
               }
           }
       }else{
           if($id > 0){
               $aSettingsModel = $oSettingsModel->getOne($id);
               $oSettingsForm->populate($aSettingsModel);
           }
            
       }

        $this->view->form = $oSettingsForm;
	
	
	
	}

}
