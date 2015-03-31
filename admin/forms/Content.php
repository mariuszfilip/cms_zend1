<?php

/**
 * @desc Formularz aktualnosci
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Content extends Zend_Form
{
	private $elementDecorators = array(
	          'FormErrors',
              'ViewHelper',
	array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'g_10 g_10M')),
    array('label', array('tag'=> 'div','class'=>'label', 'tagClass'=>'g_2 g_2M')),
	);
    private $elementClearDecorators = array(
              'ViewHelper',
               array(array('row' => 'HtmlTag'),array('tag' => 'div', 'class' => 'clear')),
	);
	
	private $buttonDecorators = array(
              'ViewHelper',
	array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'g_6')),
	);
	
	private $_GroupDecorator = array(
			'FormElements',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'line_grid'))
      );

   private $checkboxDecorators = array(
	          'FormErrors',
              'ViewHelper',
    array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'g_10 g_10M')),
    array('label', array('tag'=> 'div','class'=>'label', 'tagClass'=>'g_2 g_2M')),
    );

	public function init()
	{
		$this->setMethod('post');

		$title = new Zend_Form_Element_Text('title', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Tytuł',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
                      ),
                  'class' => 'simple_field tooltip'
       ));
       $content = new Zend_Form_Element_Textarea('content', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Treść',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'class' => 'simple_field tooltip'
       ));

              

                 $clear = new Zend_Form_Element_Note('clear',array(
                    'decorators' => $this->elementClearDecorators
                  ));
                  $clear1 = new Zend_Form_Element_Note('clear1',array(
                    'decorators' => $this->elementClearDecorators
                  ));  
                 $clear2 = new Zend_Form_Element_Note('clear2',array(
                    'decorators' => $this->elementClearDecorators
                  )); 
                  $clear3 = new Zend_Form_Element_Note('clear3',array(
                    'decorators' => $this->elementClearDecorators
                  ));
                  $clear4 = new Zend_Form_Element_Note('clear4',array(
                    'decorators' => $this->elementClearDecorators
                  ));
                  $clear5 = new Zend_Form_Element_Note('clear5',array(
                    'decorators' => $this->elementClearDecorators
                  ));                         
	             $oLangModel = new Application_Model_DbTable_Lang();
                  $aLangModel = $oLangModel->getAllLanguages(); 
                  $lang = new Zend_Form_Element_Select('lang',array('label' => 'Wybór języka','decorators' => $this->elementDecorators));
                  if($aLangModel){
                      foreach($aLangModel as $key => $value){
                          $lang->addMultiOption($value['id'],$value['name']);
                      }
                  }
		          $oContentListModel = new Application_Model_DbTable_ContentList();
                  $aContentListModel = $oContentListModel->getAllActiveContentList(); 
                  $id_content = new Zend_Form_Element_Select('id_content',array('label' => 'Typ','decorators' => $this->elementDecorators));
                  if($aContentListModel){
                      foreach($aContentListModel as $key => $value){
                          $id_content->addMultiOption($value['id'],$value['name']);
                      }
                  }
                 $status= new Zend_Form_Element_Radio('status',array('disableLoadDefaultDecorators' => true,'decorators' => $this->checkboxDecorators,'class'=> 'simple_form'));
                 $status->setRequired(true)
                  ->setLabel('Status')
                  ->setMultiOptions(array('0'=>'Nie aktywny','1'=>'Aktywny'));
                  $status->setValue(1);
                  
                  $submit = new Zend_Form_Element_Submit('login', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => 'Zapisz',
                  'class' => 'submitIt simple_buttons'
                  ));
        
                  $this->addElements(array(
                  $title,
                  $clear,
                  $content,
                  $clear1,
                  $lang,
                  $status,
                  $id_content,
                  $submit,
                  $clear2,
                  $clear3,
                  $clear4,
                   $clear5
                  ));
                  
                   $this->addDisplayGroup(array('title','clear'), 'title_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                      );
                  $this->addDisplayGroup(array('content','clear3'), 'content_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );     
                  $this->addDisplayGroup(array('lang','clear1'), 'lang_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                 $this->addDisplayGroup(array('id_content','clear5'), 'id_content_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                 $this->addDisplayGroup(array('status','clear4'), 'status_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                  $this->addDisplayGroup(array('login','clear2'), 'submit_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                      );
	}

	public function loadDefaultDecorators()
	{
		$this->setDecorators(array(
                  'FormErrors',
                  'FormElements',
                  'Form'
                  ));
	}
}
?>
