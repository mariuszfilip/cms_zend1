<?php

/**
 * @desc Formularz logowania
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Auth extends Zend_Form
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

                
                  $password = new Zend_Form_Element_Password('password', array(
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
                  
                  $submit = new Zend_Form_Element_Submit('login', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => 'Zaloguj',
                  'class' => 'submitIt simple_buttons'
                  ));

                  $this->addElements(array(
                  $email,
                  $clear,
                  $password,
                  $clear1,
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
                      
                 $this->addDisplayGroup(array('password','clear1'), 'password_group',
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
