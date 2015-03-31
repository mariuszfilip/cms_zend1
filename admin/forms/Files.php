<?php

class Application_Form_Files extends Zend_Form
{
	public $elementDecorators = array(
        'File',
        'Errors',
    array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'g_10 g_10M')),
    array('label', array('tag'=> 'div','class'=>'label', 'tagClass'=>'g_2 g_2M')),
    );
    
   private $elementDecorators_normal = array(
	          'FormErrors',
              'ViewHelper',
    array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'g_10 g_10M')),
    array('label', array('tag'=> 'div','class'=>'label', 'tagClass'=>'g_2 g_2M')),
    );
    private $elementClearDecorators = array(
              'ViewHelper',
    array(array('row' => 'HtmlTag'),array('tag' => 'div', 'class' => 'clear')),
    );
	
	public $elementDecorators_radio = array(
			'ViewHelper',
			'Errors',
			array(array('data' => 'HtmlTag'), array('tag' => 'dt')),
			array('Label', array('tag' => 'dt')),
			array(array('row' => 'HtmlTag'), array('tag' => 'dl ')),
	);
	
	public $elementPrefixPaths = array('filter' => array(
			'prefix' => 'My_Filter',
			'path' => 'My/Filter'
	));
	public $elementFilters = array(
			'StripSlashes'
	);
	 private $buttonDecorators = array(
              'ViewHelper',
    array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'g_6')),
    );
    protected $action;
    public function setActionController($action){
        if($action == 'edit'){
            $this->removeElement('uploadcontract');
        }
    }
    
    public function init()
    {       
    	$this->setAttrib('class', 'niceform');
    	$this->setAttrib('enctype', 'multipart/form-data');
    	//$this->addElementPrefixPaths($this->elementPrefixPaths);
    	//$this->setElementFilters($this->elementFilters);
    

       
          $this->addElement('file', 'uploadcontract', array(
            'destination' => APPLICATION_PATH.'/../public/files/',
            'validators' => array(
            	array('Size', false, 2097152),
                array('count', false, 1)
            ),
            'required' => true,
            'decorators' => $this->elementDecorators,
            'label' => 'Wyślij plik:'
        ));
          
          
          $this->addElement('text', 'name', array(
          		'decorators' => $this->elementDecorators_normal,
          		'required' => true,
          		'label' => 'Nazwa'
          ));
          $this->addElement('textarea', 'description', array(
          		 'decorators' => $this->elementDecorators_normal,
          		'required' => true,
          		'rows' => 10,
          		'cols' =>30,
          		'label' => 'Opis pliku'
          ));
          $oLangModel = new Application_Model_DbTable_Lang();
          $aLangModel = $oLangModel->getAllLanguages();
          $lang = new Zend_Form_Element_Select('lang',array('label' => 'Wybór języka','decorators' => $this->elementDecorators_normal));
          if($aLangModel){
               
              foreach($aLangModel as $key => $value){
                  $lang->addMultiOption($value['id'],$value['name']);
              }
          }
         $oPage = new Application_Model_DbTable_Page();
         $aPage = $oPage->getAllRoot();
         $parent = new Zend_Form_Element_Select('id_cms_pages_structure',array('label' => 'Rodzic','decorators' => $this->elementDecorators_normal));
         if($aPage){
             foreach($aPage as $key => $value){
                 $parent->addMultiOption($value['id'],$value['name']);
                 $aPageChildren = $oPage->getChildren($value['id']);
                 foreach($aPageChildren as $key2 => $value2){
                     $parent->addMultiOption($value2['id'],$value['name'].'->'.$value2['name']);
                 }
             }
         }
         
          $this->addElements(array(
                  $lang,
                  $parent
                  )
          );
          $this->addElement('radio', 'status', array(
          		'required' => true,
          		'label' => 'Status pliku',
           		'disableLoadDefaultDecorators' => true,
          		'multioptions' => array('1'=>'Aktywny', '0' => 'Nie aktywny'),
          		'separator' => '',
          		'value' => '0',
          		'decorators' => $this->elementDecorators_normal
          ));
        
        $this->addElement('submit', 'submit_file', array(
            'ignore' => true,
            'decorators' => $this->buttonDecorators,
            'label' => 'Wyslij',
        	'class' => 'submitIt simple_buttons'
        ));
        
              $this->setDecorators(array(
                  'FormErrors',
                  'FormElements',
                  'Form'
                  ));
    }
}

