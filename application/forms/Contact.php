<?php

/**
 * @desc Formularz usera  - dodawania/edycji
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Contact extends Zend_Form
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
		$this->setAttrib('id', 'contact_form');

		$email = new Zend_Form_Element_Text('email', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Email',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50)),
                       array('EmailAddress')
                      ),
                  'class' => 'simple_field tooltip'
         ));
         
         $first_name = new Zend_Form_Element_Text('first_name', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Imie',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
                      ),
                  'class' => 'simple_field tooltip'
          ));
         $last_name = new Zend_Form_Element_Text('last_name', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Nazwisko',
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
                  'label' => 'Tresc',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
                      ),
                  'class' => 'simple_field tooltip textarea_area'
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
                  $submit = new Zend_Form_Element_Submit('login', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => 'Zapisz',
                  'class' => 'submitIt simple_buttons',
                  'id' => 'contact_form_submit'
                  ));

                  $this->addElements(array(
                  $email,
                  $clear,
                  $first_name,
                  $clear3,
                  $last_name,
                  $clear4,
                  $content,
                  $submit,
                  $clear2
                  ));
                  
                   $this->addDisplayGroup(array('email','clear'), 'email_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                     );
                 $this->addDisplayGroup(array('first_name','clear3'), 'first_name_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                     );
                 $this->addDisplayGroup(array('last_name','clear4'), 'last_name_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                     );
                 $this->addDisplayGroup(array('content','clear2'), 'content_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                     );                     
                      
;                      
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
