<?php

/**
 * @desc Model Uzytkownikow
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_users';
    	
    	
    	public function addUser($data){
    	    return $this->insert($data);
    	}
    	public function updateUser($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOneUser($id){
    	    $id = (int)$id;
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	public function deleteUser($id){
    	    $id = (int)$id;
    	    $data['deleted']=1;
    	    return $this->update($data,'id='.$id);
    	}
    	public function countUser(){
    	    $select = $this->select();
    	    $select->from($this->_name,'COUNT(id) AS num');
    	    $select->where('deleted=?',0);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	
    	}
    
}