<?php

/**
 * @desc Model Jęzków
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_Lang extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_lang_list';
    	
    	
    	public function addLang($data){
    	    return $this->insert($data);
    	}
    	public function updateLang($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getAllLanguages(){
    	    $select = $this->select();
    	    $select->where('status=?',1);
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	}
    	public function getAllActiveLanguages(){
    	    $select = $this->select();
    	    $select->where('status=?',1);
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	}
    	public function getOne($id){
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	
    
}