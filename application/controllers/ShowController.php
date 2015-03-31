<?php

class ShowController extends Zend_Controller_Action
{

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{
		
	}
	public function indexAction(){
	}
	public function contactboxAction(){
	   $oContactForm = new Application_Form_Contact();
	   $request = $this->getRequest();
	   $oSettingsPage = new Application_Model_DbTable_SettingsPage();
	   $aSettingsPage = $oSettingsPage->getOne(1);
	   if($this->_request->isXmlHttpRequest()) {
       $this->_helper->layout()->disableLayout();
       $this->_helper->viewRenderer->setNoRender(true);
       $result = array();
	       try{
	           if ($request->isPost()){
	               if ($request->getPost()){
	                   if ($oContactForm->isValid($request->getPost())){
	                        $config = Zend_Registry::get('config');
	                        
	                        $first_name = $request->getPost('first_name');
	                        $last_name = $request->getPost('last_name');
                            $mail=new PHPMailer();

	                        $mail->SMTPAuth = true;
	                        $mail->SMTPSecure = $config->mailserver->smtpsecure;
	                        $mail->Host = $config->mailserver->host;
	                        $mail->Port =  $config->mailserver->port;
	                        $mail->Username = $config->mailserver->username;
	                        $mail->Password = $config->mailserver->password;
	                        $mail->CharSet = "UTF-8";
	                        $mail->FromName = $request->getPost('email');
	                        $mail->From = $request->getPost('email');
	                        $mail->Subject =$this->view->translate('contact-form');
	                        $mail->IsHTML(true);
	                        $mail->Body = '<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style>
			*{margin: 0; padding: 0;border: 0;}		
			table{background:#ffffff; border:0px; margin: 20px; width: 750px;}
			tr{ width: 750px; }
			td {width: 150px;  padding: 5px 0;}

		</style>
		
	</head>
	<body>
		
		<table BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<tbody>
			<tr>
				<td>
				<img src="'.$config->base->url.'page/css/image/email_baner.png" style="style="width: 750px; height: 240px; padding: 0;" />
				</td>
			
			</tr>
			<tr>
				
				<td style="padding: 5px 20px;background: #F1F1F1;">'.$this->view->translate('date.send').' : <strong>'.date('d-m-Y').'r</strong></td>
				
			</tr>
			<tr>
				<td style="padding: 5px 20px;">'.$this->view->translate('sender').': <strong>'.$first_name.' '.$last_name.'</strong></td>
			
			</tr>
			<tr>
				<td style="padding: 5px 20px; background: #F1F1F1;">'.$this->view->translate('email.address').': <strong>'.$request->getPost('email').'</strong></td>
			
			</tr>
			<tr>
				<td style="width: 100%; padding: 15px 20px; ;">'.$this->view->translate('content').': <strong>'.$request->getPost('content').'</strong></td>
			</tr>
			<tr>
				<td style="padding: 60px 20px 0 20px; color: #272E58; background: #F1F1F1; text-align: center;"> BZK Group ul. Swiętokrzyska 6, 96-515 Teresin tel. 500 478 598 fax. 862 186 456 </td>
			</tr>
			</tbody>
		</table>
		
	</body>
</html>';
	                        $mail->AddAddress($aSettingsPage['email_apply_form']);
	                        $mail->send();
                           $result['result']='success';
                           $result['messages']=$this->view->translate('email.was.send');
	                   }else{
                           $result['result']='failed';
                           $result['messages']=$this->view->translate('please-fill-all-fields');	                   
	                   }
	               }
	           }
	       }catch(Exception $e){
	           $result['result']='failed';
	           $result['messages']=$e->getMessage();
	       }
		   echo json_encode($result);
		   return;
	   }
	   $this->view->form = $oContactForm;
	
	}
	public function newsboxAction(){
	    
	    $limit = $this->_getParam('limit', 1);
	    $oContent = new Application_Model_DbTable_Content();
	    $aContent = $oContent->getContent('1',$limit);
	    $this->view->data  = $aContent;
	
	}
	public function offerboxAction(){
        $limit = $this->_getParam('limit', 1);	    
	    $oContent = new Application_Model_DbTable_Content();
	    $aContent = $oContent->getContent('2',$limit);
	    $this->view->data = $aContent;
	}
	public function offerspecialboxAction(){
        $limit = $this->_getParam('limit', 1);	    
	    $oContent = new Application_Model_DbTable_Content();
	    $aContent = $oContent->getContent('3',$limit);
	    $this->view->data = $aContent;
	}
	public function newsletterboxAction(){
	    if($this->_request->isXmlHttpRequest()) {
	        $this->_helper->layout()->disableLayout();
	        $this->_helper->viewRenderer->setNoRender(true);
	        $result = array();
	        $request = $this->getRequest();
	        try{
	            if ($request->isPost()){
	                if ($request->getPost()){
	                        $oNewsletterModel = new Application_Model_DbTable_Newsletter();
	                        $email = $request->getPost('email');
	                        $newslleter = $request->getPost('newsletter');
	                     if($email != ''){
	                        $validator = new Zend_Validate_EmailAddress();
	                        if ($validator->isValid($email)) {
	                            if($oNewsletterModel->getExistEmail($email) && $newslleter == 0){
	                                $data = array();
	                                $data['unsubscribe']=1;
	                                $aEmail = $oNewsletterModel->getExistEmailUnsubscribe($email);
	                                if($aEmail){
	                                    $oNewsletterModel->updateEmail($data, $aEmail['id']);
	                                    $result['result']='success';
	                                    $result['messages']=$this->view->translate('confirm.unsubscribe');    
	                                }else{
	                                    $result['result']='success';
	                                    $result['messages']=$this->view->translate('email.was.send.before');    
	                                }
	                            }else if($oNewsletterModel->checkExist($email) && $newslleter == 1){
	                                $data = array();
	                                $data['email']=$email;
	                                $oNewsletterModel->addEmail($data);
	                                $config = Zend_Registry::get('config');
	                                $oSettingsPage = new Application_Model_DbTable_SettingsPage();
	                                $aSettingsPage = $oSettingsPage->getOne(1);
                                    $mail=new PHPMailer();
        	                        $mail->SMTPAuth = true;
        	                        $mail->SMTPSecure = $config->mailserver->smtpsecure;
        	                        $mail->Host = $config->mailserver->host;
        	                        $mail->Port =  $config->mailserver->port;
        	                        $mail->Username = $config->mailserver->username;
        	                        $mail->Password = $config->mailserver->password;
        	                        $mail->CharSet = "UTF-8";
        	                        $mail->FromName = $aSettingsPage['email_sender'];
        	                        $mail->From = $aSettingsPage['email_sender'];
        	                        
        	                        $mail->Subject =$this->view->translate('cofirm-add-to-database');
        	                        $mail->IsHTML(true);
        	                        $mail->Body = '
        	                     <html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style>
			*{margin: 0; padding: 0;border: 0;}		
			table{background:#ffffff; border:0px; margin: 20px; width: 750px;}
			tr{ width: 750px; }
			td {width: 150px;  padding: 5px 0;}

		</style>
		
	</head>
	<body>
		
		<table BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<tbody>
			<tr>
				<td>
				<img src="'.$config->base->url.'page/css/image/email_baner.png" style="style="width: 750px; height: 240px; padding: 0;" />
				</td>
			
			</tr>
			<tr>
				
				<td style="padding: 5px 20px;">'.$this->view->translate('date.send').' : <strong>'.date('d-m-Y').'r</strong></td>
				
			</tr>
			<tr>
				<td style="padding: 5px 20px; background: #F1F1F1;">'.$this->view->translate('email.address').': <strong>'.$email.'</strong></td>
			
			</tr>
			<tr>
				<td style="width: 100%; padding: 15px 20px; ;">'.$this->view->translate('subject').': <strong>'.$this->view->translate('subject.newslleter.confirm').'</strong></td>
			</tr>
			<tr>
				<td style="padding: 60px 20px 0 20px; color: #272E58; background: #F1F1F1; text-align: center;"> BZK Group ul. Swiętokrzyska 6, 96-515 Teresin tel. 500 478 598 fax. 862 186 456 </td>
			</tr>
			</tbody>
		</table>
		
	</body>
</html>';
        	                         
        	                        $mail->AddAddress($email);
        	                        $mail->send();
	                                $result['result']='success';
	                                $result['messages']=$this->view->translate('email.was.add');
	                            }else{
	                                $result['result']='failed';
	                                if($newslleter == 0){
	                                    $result['messages']=$this->view->translate('you.cant.send.not.exist.email');
	                                }else{
	                                    $result['messages']=$this->view->translate('email.exist.in.database');
	                                }
	                                
	                            }
	                        }else{
	                            $result['result']='failed';
	                            $result['messages']=$this->view->translate('valid.email.format');
	                        }
	                    }else{
	                        $result['result']='failed';
	                        $result['messages']=$this->view->translate('no.email');
	                    }
	                }
	            }
	        }catch(Exception $e){
	            $result['result']='failed';
	            $result['messages']=$e->getMessage();
	        }
	        echo json_encode($result);
	        return;
	    }


	}
	
	public function applyjobpageAction(){
	    $this->view->headTitle('Aplikacja na oferte pracy');
	    $oJobModel = new Application_Model_DbTable_OfferJob();
        $this->view->menu = $oJobModel->getLast(5);
	    $oApplyJobForm = new Application_Form_ApplyJob();
	    $request = $this->getRequest();
	    $oSettingsPage = new Application_Model_DbTable_SettingsPage();
	    $aSettingsPage = $oSettingsPage->getOne(1);
	            if ($request->isPost()){
	                if ($request->getPost()){
	                    if ($oApplyJobForm->isValid($request->getPost())){
	                        $id = $request->getPost('id_offer');
	                        $aJobModelOne = $oJobModel->getOneOfferJob($id);
	                        $upload = new Zend_File_Transfer_Adapter_Http();
	                        $pathparts = pathinfo($upload->getFileName());
	                        $originalFilename = $pathparts['basename'];
	                        $filter = new My_FileFilter();
	                        $file_name = $filter->filter($originalFilename);
	                        $upload->addFilter('Rename', array('target' => APPLICATION_PATH.'/../public/files/'.$file_name,'overwrite' => true));
	                        $upload->receive();
	                        $first_name= $request->getPost('first_name');
	                        $last_name = $request->getPost('last_name');
	                        $config = Zend_Registry::get('config');
                            $mail=new PHPMailer();
	                        $mail->SMTPAuth = true;
	                        $mail->SMTPSecure = $config->mailserver->smtpsecure;
	                        $mail->Host = $config->mailserver->host;
	                        $mail->Port =  $config->mailserver->port;
	                        $mail->Username = $config->mailserver->username;
	                        $mail->Password = $config->mailserver->password;
	                        $mail->CharSet = "UTF-8";
	                        $mail->FromName = $request->getPost('email');
	                        $mail->From = $request->getPost('email');
	                        
	                        $mail->Subject =$this->view->translate('apply-offer-form');
	                        $mail->IsHTML(true);
	                        $mail->Body = '<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style>
			*{margin: 0; padding: 0;border: 0;}		
			table{background:#ffffff; border:0px; margin: 20px; width: 750px;}
			tr{ width: 750px; }
			td {width: 150px;  padding: 5px 0;}

		</style>
		
	</head>
	<body>
		
		<table BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<tbody>
			<tr>
				<td>
				<img src="'.$config->base->url.'page/css/image/email_baner.png" style="style="width: 750px; height: 240px; padding: 0;" />
				</td>
			
			</tr>
			<tr>
				
				<td style="padding: 5px 20px;">'.$this->view->translate('date.send').' : <strong>'.date('d-m-Y').'r</strong></td>
				
			</tr>
			<tr>
				<td style="padding: 5px 20px;background: #F1F1F1;">'.$this->view->translate('job-offer').': <strong>'.$aJobModelOne['title'].'</strong></td>
			
			</tr>
			<tr>
				<td style="padding: 5px 20px;">'.$this->view->translate('sender').': <strong>'.$first_name.' '.$last_name.'</strong></td>
			
			</tr>
			<tr>
				<td style="padding: 5px 20px; background: #F1F1F1;">'.$this->view->translate('email.address').': <strong>'.$request->getPost('email').'</strong></td>
			
			</tr>
			<tr>
				<td style="width: 100%; padding: 15px 20px; ;">'.$this->view->translate('content').': <strong>'.$request->getPost('content').'</strong></td>
			</tr>
			<tr>
				<td style="padding: 60px 20px 0 20px; color: #272E58; background: #F1F1F1; text-align: center;"> BZK Group ul. Swiętokrzyska 6, 96-515 Teresin tel. 500 478 598 fax. 862 186 456 </td>
			</tr>
			</tbody>
		</table>
		
	</body>
</html>';
	                        $mail->AddAttachment(APPLICATION_PATH.'/../public/files/'.$file_name, 'cv.doc'); 
	                        $mail->AddAddress($aSettingsPage['email_apply_form'],'<'.$first_name.' '.$last_name.'>');
	                        $mail->send();
	                        $result['result']='success';
	                        $result['messages']="Email został wysłany.";
	                        $this->view->error=$this->view->translate('email.was.send'); 
	                        $oApplyJobForm->reset();
	                    }else{
	                        $result['result']='failed';
	                        $result['messages']="Proszę uzupełnić wszystkie pola.";
	                        $this->view->error=$this->view->translate('please-fill-all-fields'); 
	                    }
	                }
	            }

	    $this->view->form = $oApplyJobForm;
	
	
	
	}

	public function quotesboxAction(){
	    $oCourseDataLiffe = new Application_Model_DbTable_CoursesDataLiffe();
	    $aCourseDataLiffe = $oCourseDataLiffe->getData();
	    $this->view->liffe = $aCourseDataLiffe;
	    
	    $oCourseDataNbp = new Application_Model_DbTable_CoursesDataNbp();
	    $aCourseDataNbp= $oCourseDataNbp->getData();
	    $this->view->nbp = $aCourseDataNbp;
	    
	    $oCourseDataCme = new Application_Model_DbTable_CoursesDataCme();
	    $aCourseDataCme= $oCourseDataCme->getData();
	    $this->view->cme = $aCourseDataCme;
	}
	
	public function contentpageAction(){
	    $id = $this->_getParam('id', 0);	    
	    $oContent = new Application_Model_DbTable_Content();
	    $aContent = $oContent->getOneContent($id);
	    $this->view->data = $aContent;
	    
	    $this->view->menu = $oContent->getContent($aContent['id_content'],5);
	    $this->view->headTitle($aContent['title']);
	}

	public function questionofferAction(){
	
	
	}
	public function submenuAction(){
	     $authNamespace = new Zend_Session_Namespace('cms_space');
         $aPage = $authNamespace->page;
         $oPageModel = new Application_Model_DbTable_Page();
         $aPageModel = $oPageModel->getPageByName($aPage['name_link']);  
         $this->view->page = $aPageModel;
         if($aPageModel){
                $aChild = $oPageModel->getAllChildren($aPageModel['id']);
                if($aChild){
                    $this->view->child = 1;
                    $this->view->submenu = $aChild;
                }else{
                    $this->view->child = 0;
                    $this->view->submenu = $oPageModel->getChildren($aPageModel['parent']);
                }
         }
	
	
	}
	public function jobboxAction(){
        $oJobModel = new Application_Model_DbTable_OfferJob();
        $aJobModel = $oJobModel->getLast(1);
	    $this->view->data = $aJobModel;
	}
	public function jobspageAction(){
	    $id = $this->_getParam('id', 0);	    
	    $oJobModel = new Application_Model_DbTable_OfferJob();
	    $aJobModel = $oJobModel->getOneOfferJob($id);
	    $this->view->data = $aJobModel;
	     $this->view->headTitle($aJobModel['title']);
	    $this->view->menu = $oJobModel->getLast(5);
	
	}
	public function searchAction(){
	    $search = $this->_getParam('search');
	    // content page
	    $oPageContent = new Application_Model_DbTable_PageContent();
	    $aPageContent = $oPageContent->searchPage($search);
	    $this->view->page = $aPageContent;
	    $oContent = new Application_Model_DbTable_Content();
	    $aContent = $oContent->searchContent($search);
	    $this->view->content = $aContent;
	    
	
	}
	public function notificationAction(){
	   $id = $this->_getParam('id');
	   $this->view->id = $id;
	   if($this->_request->isXmlHttpRequest()) {
	        $this->_helper->layout()->disableLayout();
	        $this->_helper->viewRenderer->setNoRender(true);
	        $result = array();
	        $request = $this->getRequest();
	        try{
	            if ($request->isPost()){
	                if ($request->getPost()){
	                     $email = $request->getPost('email');
	                     $id = $request->getPost('id');
	                     $oExchangeFiles = new Application_Model_DbTable_ExchangeFilesNotification();
	                     $aExchange =  $oExchangeFiles->getIdFileInfo($id);
	                     if($email != ''){
	                        $validator = new Zend_Validate_EmailAddress();
	                        if ($validator->isValid($email)) {
	                                $name =  $request->getPost('name');
	                                $phone =  $request->getPost('phone');
	                                $content =  $request->getPost('content');
	                                $config = Zend_Registry::get('config');
	                                $oSettingsPage = new Application_Model_DbTable_SettingsPage();
	                                $aSettingsPage = $oSettingsPage->getOne(1);
                                    $mail=new PHPMailer();
        	                        $mail->SMTPAuth = true;
        	                        $mail->SMTPSecure = $config->mailserver->smtpsecure;
        	                        $mail->Host = $config->mailserver->host;
        	                        $mail->Port =  $config->mailserver->port;
        	                        $mail->Username = $config->mailserver->username;
        	                        $mail->Password = $config->mailserver->password;
        	                        $mail->CharSet = "UTF-8";
        	                        $mail->FromName = $request->getPost('email');
        	                        $mail->From = $request->getPost('email');
        	                        $mail->Subject ='Zapytanie o oferte';
        	                        $mail->IsHTML(true);
        	                        $mail->Body = '<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style>
			*{margin: 0; padding: 0;border: 0;}		
			table{background:#ffffff; border:0px; margin: 20px; width: 750px;}
			tr{ width: 750px; }
			td {width: 150px;  padding: 5px 0;}

		</style>
		
	</head>
	<body>
		
		<table BORDER="0" CELLSPACING="0" CELLPADDING="0">
			<tbody>
			<tr>
				<td>
				<img src="'.$config->base->url.'page/css/image/email_baner.png" style="style="width: 750px; height: 240px; padding: 0;" />
				</td>
			
			</tr>
			<tr>
				
				<td style="padding: 5px 20px;">'.$this->view->translate('date.send').' : <strong>'.date('d-m-Y').'r</strong></td>
				
			</tr>
			<tr>
				<td style="padding: 5px 20px;background: #F1F1F1;">'.$this->view->translate('sender').': <strong>'.$name.'</strong></td>
			
			</tr>
			<tr>
				<td style="padding: 5px 20px;">'.$this->view->translate('number-phone').': '.$phone.'</td>
			
			</tr>
			<tr>
				<td style="padding: 5px 20px; background: #F1F1F1;">'.$this->view->translate('email.address').': <strong>'.$request->getPost('email').'</strong></td>
			
			</tr>
			
			<tr>
				<td style="width: 100%; padding: 15px 20px; ;">'.$this->view->translate('content').': <strong>'.$content.'</strong></td>
			</tr>
			<tr>
				<td style="padding: 60px 20px 0 20px; color: #272E58; background: #F1F1F1; text-align: center;"> BZK Group ul. Swiętokrzyska 6, 96-515 Teresin tel. 500 478 598 fax. 862 186 456 </td>
			</tr>
			</tbody>
		</table>
		
	</body>
</html>';
        	                        if(isset($aExchange['email'])){
        	                            $mail->AddAddress($aExchange['email']);
        	                        }else{
        	                            
        	                            $mail->AddAddress($aSettingsPage['email_notification']);
        	                        }
        	                        $mail->send();
	                                $result['result']='success';
	                                $result['messages']=$this->view->translate('email.was.add');
	                        }else{
	                            $result['result']='failed';
	                            $result['messages']=$this->view->translate('valid.email.format');
	                        }
	                    }else{
	                        $result['result']='failed';
	                        $result['messages']=$this->view->translate('no.email');
	                    }
	                }
	            }
	        }catch(Exception $e){
	            $result['result']='failed';
	            $result['messages']=$e->getMessage();
	        }
	        echo json_encode($result);
	        return;
	    }
	    
	
	}
	public function filesAction(){
		 $authNamespace = new Zend_Session_Namespace('cms_space');
         $aPage = $authNamespace->page;
         $oPageModel = new Application_Model_DbTable_Page();
         $oFilesModel = new Application_Model_DbTable_Files();
         $aPageModel = $oPageModel->getPageByName($aPage['name_link']);       
         if($aPageModel){
                $aFiles = $oFilesModel->getAllFilesFromPage($aPageModel['id']);
                if($aFiles){
                    $this->view->files = $aFiles;
                }else{
                     $this->view->files = false;
                }
         }
	
	
	}
	public function downloadAction(){
	      $id = $this->_getParam('id', 0);
	      $oFilesModel = new Application_Model_DbTable_Files();
	      $aFilesModel = $oFilesModel->getOneFile($id);
	      $file = realpath(APPLICATION_PATH.'/../public/files/'.$aFilesModel['name_file']);      
	      if (file_exists($file)) {
	          header('Content-Description: File Transfer');
	          header('Content-Type: application/octet-stream');
	          header('Content-Disposition: attachment; filename='.basename($file));
	          header('Content-Transfer-Encoding: binary');
	          header('Expires: 0');
	          header('Cache-Control: must-revalidate');
	          header('Pragma: public');
	          header('Content-Length: ' . filesize($file));
	          ob_clean();
	          flush();
	          readfile($file);
	          exit;
	      }
	
	}
    public function cooperationAction(){
        $limit = $this->_getParam('limit', 1);	    
	    $oContent = new Application_Model_DbTable_Content();
	    $aContent = $oContent->getContent('3',$limit);
	    $this->view->data = $aContent;
    }
    public function notificationpageAction(){
        $oExchangeFiles = new Application_Model_DbTable_ExchangeFilesNotification();
        $this->view->files = $oExchangeFiles->getFileList();
         $id = $this->_getParam('id');
         if($id){
             $aExchange =  $oExchangeFiles->getIdFileInfo(intval($id));
             $oPageModel = new Application_Model_DbTable_Page();
             $aPageModel = $oPageModel->getOnePage($aExchange['id_content']);
             $authNamespace = new Zend_Session_Namespace('cms_space');
             $authNamespace->page=$aPageModel;
             $this->view->content = $this->view->Page(intval($aExchange['id_content']));
         }
    
    }
    public function purchaseAction(){} 
}
	
	

