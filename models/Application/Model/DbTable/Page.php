<?php

/**
 * @desc Model stron zdefinowanych - charakterystyka ogolna bez contentu
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_Page extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_pages_structure';
    	
    	
    	public function addPage($data){
    	    return $this->insert($data);
    	}
    	public function updatePage($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOnePage($id){
    	    $id = (int)$id;
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	public function deletePage($id){
    	    $id = (int)$id;
    	    $data['deleted']=1;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getMenu(){
            $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language');
            if(!$localeValue){
                $localeValue = 1;
            }
    	    $select = $this->select()->setIntegrityCheck(false);
    	    $select->where('deleted=?',0);
    	    $select->where('status=?',1);
    	    $select->where('parent=?',0);
    	    $select->where('lang=?',$localeValue);
    	    $select->order('order');
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        $aResult = $rows->toArray();
    	        foreach($aResult as $key => $value){
    	            $aChild = $this->getChildren($value['id']);
    	            if($aChild){
    	                $aResult[$key]['children']=$aChild;
    	            }
    	        }
    	        return $aResult;
    	    }
    	    return false;
    	    
    	}
    	public function getFooterMenu(){
            $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language');
            if(!$localeValue){
                $localeValue = 1;
            }
    	    $select = $this->select()->setIntegrityCheck(false);
    	    $select->where('deleted=?',0);
    	    $select->where('status=?',1);
    	    $select->where('show_in_footer=?',1);
    	    $select->where('lang=?',$localeValue);
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        $result = array();
    	        $rows = $rows->toArray();
    	        $i = 0;
    	        foreach($rows as $key => $value){
    	             if($value['parent'] == 0){
    	                 $result[$value['id']][0]=$value;
    	             }else{
    	                
    	                 $result[$value['parent']][$value['id']]=$value;        
    	             }
    	             $i++;
    	        }
    	        return $result;
    	    }
    	    return false;
    	    
    	}    	
    	public function getPageByName($name){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language');
            if(!$localeValue){
                $localeValue = 1;
            }	
                
    	    $select = $this->select();
    	    $select->where('name_link=?',$name);
    	    $select->where('lang=?',$localeValue);    	    
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	public function setGeneral($id,$lang){
    	    $this->update(array('general'=>'0'),'lang='.$lang);
    	    $this->update(array('general'=>'1'),'id='.$id);
    	}
    	public function getGeneralPage(){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language');   
            if (!$localeValue) {
                $localeValue = '1';
            }
    	    $select = $this->select();
    	    $select->where('lang=?',$localeValue);    
    	    $select->where('general=?',1);	    
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	
    	
    	}
    	public function getAllRoot(){
    	    $select = $this->select();
    	    $select->where('parent=?',0);
    	    $select->where('deleted=?',0);
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }   
    	    return false;
    	 }
    	 public function getChildren($parent){
    	    $parent = (int)$parent;
    	    $select = $this->select();
    	    $select->where('parent=?',$parent);
    	    $select->where('deleted=?',0);
    	     $select->where('status=?',1);
    	     $select->order('order');
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }   
    	    return false;
    	 }
    	 public function getAllChildren($parent){
    	    $parent = (int)$parent;
    	    $select = $this->select();
    	    $select->where('parent=?',$parent);
    	    $select->where('deleted=?',0);
    	     $select->where('status=?',1);
    	     $select->order('order');
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        $aResult = $rows->toArray();
    	        if($aResult){
    	            foreach($aResult as $key => $result){
    	                  $child=$this->getChildren($result['id']);
    	                  if(child){
    	                      $aResult[$key]['children']=$child;
    	                  }
    	                
    	            }    
    	        }
    	        return $aResult;
    	    }   
    	    return false;
    	 }
    	 public function getBreadCrumb($name ,$parent = 0, $wynik = array()){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language');
            if(!$localeValue){
                $localeValue = 1;
            }
    	    $select = $this->select()->setIntegrityCheck(false);
    	    $select->where('deleted=?',0);
    	    $select->where('status=?',1);
    	    if($parent > 0){
    	        $select->where('id=?',$parent);
    	    }else{
    	         $select->where('name_link=?',$name);
    	    }
    	    $select->where('lang=?',$localeValue);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){ 
    	        $row = $row->toArray();
    	        $wynik[] = $row['name'];
    	        if($row['parent'] == 0){
    	            return $wynik;
    	        }else{
    	            return $this->getBreadCrumb($row['name_link'],$row['parent'],$wynik);
    	        }
    	       
    	    }
    	    return $wynik;
    	 } 
    	
    
}