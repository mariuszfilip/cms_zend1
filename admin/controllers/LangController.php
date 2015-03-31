<?php
/**
 * Zarządzanie tytułami i opisami stron
 * @author Mariusz Filipkowski
 *
 */

class LangController extends Zend_Controller_Action
{
	static public $labelTable = array("id" => "Id","title" => "Tytuł","controller" => "Controller","action" => "Akcja");
    static public $actionTable = array("edit" => "Edycja","delete" => "Usuń");

	public function init() {
		$this->_helper->redirector->setUseAbsoluteUri(true);
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function preDispatch()
	{

	}

	
	
	public function indexAction(){
	    
	    
	    $controllers = array();
	    $diritem = new DirectoryIterator(realpath(APPLICATION_PATH.'/../application/lang/'));
	    foreach($diritem as $item) {
	        if($item->isFile()) {
	            if(strstr($item->getFilename(),'.ini')!=FALSE) {
	                $pos = strpos($item->getFilename(),'.ini');
	                $language_name = substr($item->getFilename(), 0, $pos);
	                $languages[]=($language_name);
	            }
	        }
	    }
	    $this->view->languages = $languages;
	    $limit = $this->getRequest()->getParam('iDisplayLength', 20);
	    $offset = $this->getRequest()->getParam('iDisplayStart', 0);

	    $where = array();
	    $lang =$this->getRequest()->getParam('lang');
	    if(is_string($lang)){
	        $localeValue = $lang;
	    }else{
	        $localeValue = 'pl';
	    }
	    $this->view->lang = $localeValue;
	    if($this->_request->isXmlHttpRequest()) {
    	    $this->_helper->layout->disableLayout();
    	    $this->_helper->viewRenderer->setNoRender();

	        $translationFile = APPLICATION_PATH.'/../application' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR. $localeValue . '.ini';
	        $section=null;
	        $list_conf =  new Zend_Config_Ini($translationFile,$section,array('allowModifications'=>true));
	        $result = array();
	        $i=1;
	        $list_conf= $list_conf->toArray();
	        $rows = array();
	        foreach($list_conf as $key => $value){
	            $result_array = array();
                $result_array['key']=$key;
                if(is_array($value)){
                	$result = $this->getList($value,$localeValue,$result_array);
    	            if(is_array($result) && !empty($result)){
    	                $rows[] = $result;
    	            }
                }else{
                    $result_array['name_lang']=$localeValue;
                    $result_array['value']=$value;
                    $rows[] = $result_array;
                }              
	            
	        }
            $output = array_slice($rows, $offset, $limit); 
	        $answer['iTotalRecords'] = count($rows);
	        $answer['iTotalDisplayRecords'] = count($rows);
	        $answer['sEcho'] = (int)$this->getRequest()->getParam('sEcho');
	        $answer['aaData'] = $output ? $output : array();
	         
	        echo Zend_Json::encode($answer);
	    }
	    $this->view->title = "Lista tłumaczen na stronie";
	    $this->view->headTitle($this->view->title);

	    $this->view->page = $page;
	    $this->view->sort = $sortField;
	    $this->view->direction = $sortDir;
	    $this->view->messages = $this->_flashMessenger->getMessages();
	    $this->view->list = $list;

	}
	public function addAction(){
	    /*
	    $config = Zend_Registry::get('config');
		$this->view->jspath = $config->js->path;
		$this->view->syspath = $config->system->path;
	    $this->config = Zend_Registry::get('config');
	    $this->view->title = "Dodanie nowego opisu";
	    $this->view->headTitle($this->view->title);
	    $explode = explode('.', $index);
	                
	    if($this->getRequest()->getPost('controller') != ''){
	        $form = new Application_Form_Pagedescription($this->getRequest()->getPost('controller'));
	    }else{
	        $form = new Application_Form_Pagedescription();
	    }
	    
	    $this->view->form = $form;
	    if ($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {
	                $title = $form->getValue('title');
	                $description = $form->getValue('description');
	                $controller = $form->getValue('controller');
	                $action = $form->getValue('action');
	                $localeValue = $form->getValue('lang');
                    $translationFile = APPLICATION_PATH.'/../application' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR. $localeValue . '.ini';
                    $section=null;
                    $config =   new Zend_Config_Ini($translationFile,$section,array('allowModifications'=>true));
                    if(!isset($config->$controller->$action->title) || $config->$controller->$action->title == null){
                        $config->__set($controller.'.'.$action.'.title',$title);
                        $config->__set($controller.'.'.$action.'.description',$description);
                    }else{
                         $config->$controller->$action->title=$title;
                         $config->$controller->$action->description=$description;
                    }
                    $writer = new Zend_Config_Writer_Ini();
                    $writer->setRenderWithoutSections();
                    $writer->write($translationFile,$config); 
	                $this->view->success = 'Nowy opis został dodany.';
	                $this->_flashMessenger->addMessage('Nowy opis został dodany.');
	                $this->_helper->redirector('list');
	            
	             
	        } else {
	            $form->populate($formData);
	        }
	    }
	    */

	}
	public function editAction(){
	    $config = Zend_Registry::get('config');
	    $localeValue = 'pl';
		$this->view->jspath = $config->js->path;
		$this->view->syspath = $config->system->path;
	    $this->config = Zend_Registry::get('config');
	    $this->view->title = "Edycja tytułu i opisu";
	    $this->view->headTitle($this->view->title);
	    $index = $this->_getParam('id', 0);
	    $explode = explode('.', $index);
	               
	    $form = new Application_Form_Lang();
	    $this->view->form = $form;
        
	    if ($this->getRequest()->isPost()) {
	        $formData = $this->getRequest()->getPost();
	        if ($form->isValid($formData)) {
                    $localeValue = $form->getValue('lang');
                    $key = $form->getValue('key');
                    $value = $form->getValue('value');
                    $translationFile = APPLICATION_PATH.'/../application' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR. $localeValue . '.ini';
                    $section=null;
                    $config =  new Zend_Config_Ini($translationFile,$section,array('allowModifications'=>true));
                    if($config->__isset($key)){
                        $config->__set($key,$value);
                    }
                    $writer = new Zend_Config_Writer_Ini();
                    $writer->setRenderWithoutSections();
                    $writer->write($translationFile,$config); 

	                $this->view->success = 'Aktualizacja poprawna.';
	                $this->_flashMessenger->addMessage('Aktualizacja poprawna.');
	                $index = $localeValue.'.'.$controller.'.'.$action;
	                $this->_redirect('lang/index');	            
	             
	        } else {
	            $form->populate($formData);
	        }
	    }
	    else{

	        $index = $this->_getParam('id', 0);
	        $localeValue = $this->_getParam('lang', 0);
	        if($index !== 0){
	            $translationFile = APPLICATION_PATH.'/../application' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR. $localeValue . '.ini';
	            $section=null;
	            $conf = new Zend_Config_Ini($translationFile,$section,array('allowModifications'=>true));

	            $conf = $conf->toArray();
	            foreach($conf as $key => $value){
    	            $result_array = array();
                    $result_array['key']=$key;
                    if(is_array($value)){
                    	$result = $this->getList($value,$localeValue,$result_array);
        	            if(is_array($result) && !empty($result)){
        	                $rows[$result['key']] = $result['value'];
        	            }
                    }else{
                        $rows[$key] = $value;
                    }              	            
	            }
	            $data['key']=$index;
	            $data['value']=$rows[$index];
	            $data['lang']=$localeValue;
	            $form->populate($data);
	            
	        }
	    }
	
	}
	protected function getList($value,$lang ,$return = array()){
	    if(is_array($value)){
	        foreach($value as $key => $val){
	            if(is_array($val)){
	                if(isset($return['key'])){
	                    $return['key']=$return['key'].'.'.$key;
	                }else{
	                    $return['key']=$key;
	                }
	                
	                return $this->getList($val,$lang,$return);
	            }else{
	            	if(isset($return['key'])){
	                    $return['key']=$return['key'].'.'.$key;
	                }else{
	                    $return['key']=$key;
	                }
	                $return['value']=$val;
	                $return['name_lang']=$lang;
	                
	            }
	        }
	        return $return;
	    }else{
	        return $return;
	    }
	
	}
}