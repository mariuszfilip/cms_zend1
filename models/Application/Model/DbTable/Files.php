<?php

/**
 * @desc Model plików przypisanych do stron
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_Files extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_files_page';
    	
    	
    	public function addFile($data){
    	    return $this->insert($data);
    	}
    	public function updateFile($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOneFile($id){
    	    $id = (int)$id;
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	public function deleteFile($id){
    	    $id = (int)$id;
    	    $data['deleted']=1;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getAllFilesFromPage($id_page){
    	    $select = $this->select();
    	    $select->where('id_cms_pages_structure=?',$id_page);
    	    $select->where('status=?',1);
    	    $select->where('deleted=?',0);
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	}

 	    
}