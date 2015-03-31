<?php

/**
 * @desc Formularz logowania
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Password extends Zend_Form
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


                
                  $password = new Zend_Form_Element_Password('password', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Stare Haslo',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(6, 50))
                      ),
                  'class' => 'simple_field tooltip'
                  ));
                  
                  
                  $password_new = new Zend_Form_Element_Password('password_new', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Nowe Haslo',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(6, 50))
                      ),
                  'class' => 'simple_field tooltip'
                  ));
                  $password_new_repeat = new Zend_Form_Element_Password('password_new_repeat', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Nowe Haslo powtórz',
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
                  
                  $submit = new Zend_Form_Element_Submit('login', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => 'Zapisz',
                  'class' => 'submitIt simple_buttons'
                  ));

                  $this->addElements(array(
                  $password,
                  $clear,
                  $password_new,
                  $clear1,
                  $password_new_repeat,
                  $clear2,
                  $submit,
                  $clear3
                  ));
                  
                   $this->addDisplayGroup(array('password','clear'), 'password_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                      );
                      
                 $this->addDisplayGroup(array('password_new','clear1'), 'password_new_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                      )
                      );
                 $this->addDisplayGroup(array('password_new_repeat','clear2'), 'password_new_repeat_group',
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
