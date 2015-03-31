<?php

/**
 * @desc Formularz usera  - dodawania/edycji
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Lang extends Zend_Form
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

		$key = new Zend_Form_Element_Text('key', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Klucz',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
                      ),
                  'class' => 'simple_field tooltip'
         ));
         $key->setAttrib('readonly', 'readonly');        
         $value = new Zend_Form_Element_Text('value', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Content',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
                      ),
                  'class' => 'simple_field tooltip'
          ));
         $lang = new Zend_Form_Element_Text('lang', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Jezyk',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'class' => 'simple_field tooltip'
          ));
          $lang->setAttrib('readonly', 'readonly');           
           $submit = new Zend_Form_Element_Submit('wyslij', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => 'Zapisz',
                  'class' => 'submitIt simple_buttons'
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
                 $clear3 = new Zend_Form_Element_Note('clear2',array(
                    'decorators' => $this->elementClearDecorators
                  ));   


                  $this->addElements(array(
                  $key,
                  $clear,
                  $value,
                  $clear1,
                  $lang,
                  $clear3,
                  $submit,
                  $clear2
                  ));
                  
                   $this->addDisplayGroup(array('key','clear'), 'email_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                     );
                 $this->addDisplayGroup(array('value','clear1'), 'first_name_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                     );
                      $this->addDisplayGroup(array('lang','clear3'), 'lang_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                     );             
                  $this->addDisplayGroup(array('wyslij','clear2'), 'submit_group',
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
