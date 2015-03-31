<?php

/**
 * @desc Model Nowosci
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_ContentList extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_content_list';
    	
    	
    	public function addContent($data){
    	    return $this->insert($data);
    	}
    	public function updateContent($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getAllActiveContentList(){
    	    $id = (int)$id;
    	    $select = $this->select();
    	    $select->where('status=?',1);
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	}
    	
    
}