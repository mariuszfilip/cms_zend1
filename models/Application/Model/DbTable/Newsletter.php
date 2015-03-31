<?php

/**
 * @desc Model newslettera
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_Newsletter extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_newsletter_email';
    	
    	
    	public function addEmail($data){
    	    return $this->insert($data);
    	}
    	public function updateEmail($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOneEmail($id){
    	    $id = (int)$id;
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	public function deleteEmail($id){
    	    $id = (int)$id;
    	    $data['deleted']=1;
    	    return $this->update($data,'id='.$id);
    	}
    	public function countEmailNewsletter(){
    	    $select = $this->select();
    	    $select->from($this->_name,'COUNT(id) AS num');
    	    $select->where('deleted=?',0);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	
    	}
    	public function checkExist($email){
    	    $select = $this->select();
    	    $select->where('email=?',$email);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return false;
    	    }
    	    return true;
    	
    	}
    	
    	public function getExistEmail($email){
    	    $select = $this->select();
    	    $select->where('email=?',$email);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	
    	}
    	
    	public function getExistEmailUnsubscribe($email){
    	    $select = $this->select();
    	    $select->where('email=?',$email);
    	    $select->where('unsubscribe=?',0);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	
    	}
    	
    
}