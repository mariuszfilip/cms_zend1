<?php

/**
 * @desc Model stron zdefinowanych - charakterystyka ogolna bez contentu
 * @autor Mariusz Filipkowski
 * 
 **/
class Application_Model_DbTable_OfferJob extends Zend_Db_Table_Abstract
{
    	protected $_name = 'cms_offer_jobs';
    	
    	
    	public function addOfferJob($data){
    	    return $this->insert($data);
    	}
    	public function updateOfferJob($data,$id){
    	    $id = (int)$id;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getOneOfferJob($id){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            } 
    	    $id = (int)$id;
    	    $select = $this->select();
    	    $select->where('id=?',$id);
    	    $select->where('lang=?',$localeValue);
    	    $row = $this->fetchRow($select);
    	    if($row instanceof Zend_Db_Table_Row){
    	        return $row->toArray();
    	    }
    	    return false;
    	}
    	public function deleteOfferJob($id){
    	    $id = (int)$id;
    	    $data['deleted']=1;
    	    return $this->update($data,'id='.$id);
    	}
    	public function getLast($limit=1){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            } 
    	    $select = $this->select();
    	    $select->where('deleted=?',0);
    	    $select->where('status=?',1);
    	    $select->where('lang=?',$localeValue);
    	    $select->limit($limit);
    	    $select->order('date_add desc');
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;
    	}
    	public function getAllActiveOffer(){
    	    $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            } 
    	    $select = $this->select();
    	    $select->where('deleted=?',0);
    	    $select->where('status=?',1);
    	    $select->where('lang=?',$localeValue);
    	    $select->limit($limit);
    	    $select->order('date_add desc');
    	    $rows = $this->fetchAll($select);
    	    if($rows instanceof Zend_Db_Table_Rowset){
    	        return $rows->toArray();
    	    }
    	    return false;    	
    	
    	
    	}
    
}