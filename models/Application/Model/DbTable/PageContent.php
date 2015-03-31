<?php

/**
 * @desc Model Uzytkownikow
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_PageContent extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_pages_content';
    	
    	
    	public function addPageContent($data){
    	    return $this->insert($data);
    	}
    	public function addPageContentPosition($id_page,$position){
    	    $data['id_cms_pages_structure']=$id_page;
    	    $data['position_layout']=$position;
    	    $check = $this->checkExist($id_page,$position);
    	    if($check){
    	        $this->updatePageContent($data, $check['id']);
    	        return $check['id'];
    	    }else{
                 return $this->insert($data);    	    
    	    }

    	}
    	public function updatePageContent($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOnePageContent($id){
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	    
    	
    	}
    	public function checkExist($id_page,$position){
    	    $select = $this->select();
    	    $select->where('id_cms_pages_structure=?',$id_page);
    	    $select->where('position_layout=?',$position);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	    
    	
    	}
    	public function searchPage($search){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            } 
    	    $select = $this->select()->setIntegrityCheck(false);
    	    $select->from('cms_pages_content');
    	    $select->where("MATCH(`content`) AGAINST('".$search."') or MATCH(`title`) AGAINST('".$search."')");
    	    $select->join('cms_pages_structure', 'cms_pages_structure.id=cms_pages_content.id_cms_pages_structure', array('cms_pages_structure.name','cms_pages_structure.name_link'));
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	}
    	
    
}