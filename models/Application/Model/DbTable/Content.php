<?php

/**
 * @desc Model Nowosci
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_Content extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_content';
    	
    	
    	public function addContent($data){
    	    return $this->insert($data);
    	}
    	public function updateContent($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOneContent($id){
    	    $id = (int)$id;
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	public function deleteContent($id){
    	    $id = (int)$id;
    	    $data['deleted']=1;
    	    return $this->update($data,'id='.$id);
    	}
    	public function countContent($id_content){
    	    $select = $this->select();
    	    $select->from($this->_name,'COUNT(id) AS num');
    	    $select->where('deleted=?',0);
    	    $select->where('id_content=?',$id_content);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	
    	}
    	public function getContent($type,$limit){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            } 
    	    $select = $this->select();
    	    $select->where('id_content=?',(int)$type);
    	    $select->where('deleted=?',0);
    	    $select->where('lang=?',$localeValue);    	  
    	    $select->order('date_add desc');
    	    $select->limit($limit);
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	}
    	public function searchContent($search){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            } 
    	    $select = $this->select();
    	    $select->where("MATCH(`content`) AGAINST('".$search."') or MATCH(`title`) AGAINST('".$search."')");
    	    $select->where('lang=?',$localeValue); 
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	
    	
    	}

    	
    
}