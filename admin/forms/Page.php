<?php

/**
 * @desc Formularz logowania
 * @author Mariusz Filipkowski
 *
 */
class Application_Form_Page extends Zend_Form
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

        $name = new Zend_Form_Element_Text('name', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Nazwa',
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
                $number_column= new Zend_Form_Element_Radio('number_column',array('disableLoadDefaultDecorators' => true,'decorators' => $this->checkboxDecorators,'class'=> 'simple_form'));
                 $number_column->setRequired(true)
                ->setLabel('Liczba kolumn')
                ->setMultiOptions(array('1'=>'1','2'=>'2','3'=>'3'));
                
                 $number_column->setValue(1);
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
                  $clear6 = new Zend_Form_Element_Note('clear6',array(
                    'decorators' => $this->elementClearDecorators
                  ));
                  $clear7 = new Zend_Form_Element_Note('clear7',array(
                    'decorators' => $this->elementClearDecorators
                  ));
                 $showinfooter= new Zend_Form_Element_Radio('show_in_footer',array('disableLoadDefaultDecorators' => true,'decorators' => $this->checkboxDecorators,'class'=> 'simple_form'));
                 $showinfooter->setRequired(true)
                  ->setLabel('Pokaz w stopce')
                  ->setMultiOptions(array('0'=>'Nie','1'=>'Tak'));
                 $showinfooter->setValue(0); 
                 $status= new Zend_Form_Element_Radio('status',array('disableLoadDefaultDecorators' => true,'decorators' => $this->checkboxDecorators,'class'=> 'simple_form'));
                 $status->setRequired(true)
                  ->setLabel('Status')
                  ->setMultiOptions(array('0'=>'Nie aktywny','1'=>'Aktywny'));
                  $status->setValue(1);
                  $oLangModel = new Application_Model_DbTable_Lang();
                  $aLangModel = $oLangModel->getAllLanguages(); 
                  $lang = new Zend_Form_Element_Select('lang',array('label' => 'Wybór języka','decorators' => $this->elementDecorators));
                  if($aLangModel){
                       
                      foreach($aLangModel as $key => $value){
                          $lang->addMultiOption($value['id'],$value['name']);
                      }
                  }
                  
                 $oPage = new Application_Model_DbTable_Page();
                 $aPage = $oPage->getAllRoot(); 
                  $parent = new Zend_Form_Element_Select('parent',array('label' => 'Rodzic','decorators' => $this->elementDecorators));
                  if($aPage){
                      $parent->addMultiOption(0,'root');
                      foreach($aPage as $key => $value){
                          $parent->addMultiOption($value['id'],$value['name']);
                          $aPageChildren = $oPage->getAllChildren($value['id']);
                          foreach($aPageChildren as $key2 => $value2){
                              $parent->addMultiOption($value2['id'],$value['name'].'->'.$value2['name']);
                              if(isset($value2['children'])){
                                 foreach($value2['children'] as $key3 => $value3){
                                      $parent->addMultiOption($value3['id'],$value['name'].'->'.$value2['name'].'->'.$value3['name']);    
                                  }
                              }    
                          }
                      }
                  }
                  
                          $order = new Zend_Form_Element_Text('order', array(
                  'decorators' => $this->elementDecorators,
                  'label' => 'Kolejność (czym wyższy numer tym bedzie dalej)',
                  'required' => true,
                  'filters' => array(
                      'StringTrim'
                      ),
                  'class' => 'simple_field tooltip'
                  ));

                  $clear = new Zend_Form_Element_Note('clear8',array(
                    'decorators' => $this->elementClearDecorators
                  ));
                  
                  
                  
                  $submit = new Zend_Form_Element_Submit('login', array(
                  'decorators' => $this->buttonDecorators,
                  'label' => 'Zapisz',
                  'class' => 'submitIt simple_buttons'
                  ));
                 $general= new Zend_Form_Element_Checkbox('general',array('disableLoadDefaultDecorators' => true,'decorators' => $this->checkboxDecorators,'class'=> 'simple_form'));
                 $general->setRequired(true)
                  ->setLabel('Ustaw jako głowną');

                  $this->addElements(array(
                  $name,
                  $clear,
                  $number_column,
                  $clear1,
                  $lang,
                  $clear2,
                  $status,
                  $clear3,
                  $showinfooter,
                  $clear7,
                  $parent,
                  $clear6,
                  $general,
                  $clear5,
                  $order,
                  $clear8,
                  $submit,
                  $clear14
                  ));

                  $this->addDisplayGroup(array('name','clear'), 'name_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                 $this->addDisplayGroup(array('number_column','clear1'), 'number_column_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                  $this->addDisplayGroup(array('lang','clear2'), 'lang_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                   $this->addDisplayGroup(array('parent','clear6'), 'parent_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                  $this->addDisplayGroup(array('status','clear3'), 'status_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                  $this->addDisplayGroup(array('show_in_footer','clear7'), 'footer_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                  $this->addDisplayGroup(array('general','clear5'), 'general_group',
                  array(
					'disableLoadDefaultDecorators' => true,
                      'class' => 'line_grid',
                      'decorators' => $this->_GroupDecorator,
                  )
                  );
                  $this->addDisplayGroup(array('order','clear8'), 'general_group',
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
