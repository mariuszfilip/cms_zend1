<?php

/**
 * @desc Formularz ustawien strony
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Settings extends Zend_Form
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

        $emailContactForm = new Zend_Form_Element_Text('email_contact_form', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Email - formularz kontaktowy',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'validators' => array(
                      array('StringLength', false, array(3, 50))
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
                  
                 
                  $submit = new Zend_Form_Element_Submit('login', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => 'Zapisz',
                  'class' => 'submitIt simple_buttons'
                  ));

                  $this->addElements(array(
                  $emailContactForm,
                  $clear,
                  $submit,
                  $clear14
                  ));

                  $this->addDisplayGroup(array('email_contact_form','clear'), 'email_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
            

                  $this->addDisplayGroup(array('login','clear4'), 'submit_group',
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
