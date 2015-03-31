<?php

/**
 * @desc Ustawienia strony
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_SettingsPage extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_settings_page';
    	
    	
    	public function addSettings($data){
    	    return $this->insert($data);
    	}
    	public function updateSettings($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOne($id){
    	    $id = (int)$id;
            $select = $this->select();
            $select->from('id=?',$id);
            $row = $this->fetchRow($id);
            if($row instanceof Zend_Db_Table_Row){
                return $row->toArray();
            }
            return false;
    	}
    	
    
}