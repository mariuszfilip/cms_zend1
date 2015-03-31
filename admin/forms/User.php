<?php

/**
 * @desc Formularz usera  - dodawania/edycji
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_User extends Zend_Form
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

		$email = new Zend_Form_Element_Text('email', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Email',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
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

                
                  $password = new Zend_Form_Element_Text('password', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Haslo',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(6, 50))
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
                  $email,
                  $clear,
                  $password,
                  $clear1,
                  $first_name,
                  $clear3,
                  $last_name,
                  $clear4,
                  $status,
                  $clear5,
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
                      
                 $this->addDisplayGroup(array('password','clear1'), 'password_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                      );
                 $this->addDisplayGroup(array('status','clear5'), 'status_group',
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
