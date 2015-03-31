<?php

class Application_Model_DbTable_ExchangeFilesNotification extends Zend_Db_Table_Abstract
{

    protected $_name = 'exchange_files_notification';
    protected $_primary = 'id';
    
    
    public function getIdFileInfo($id_file){
            $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            }
            $select = $this->select();
            $select->where('id=?',intval($id_file));
            $select->where('lang=?',$localeValue);
            $row = $this->fetchRow($select);
            if($row instanceof Zend_Db_Table_Row){
                return $row->toArray();
            } 
            return false;
    }

    public function getFileList(){
            $request = new Zend_Controller_Request_Http();
            $localeValue = $request->getCookie('language'); 
    	    if(!$localeValue){
                $localeValue = 1;
            }
            $select = $this->select();
            $select->where('lang=?',$localeValue);
            $select->where('status=?',1);

            $rows = $this->fetchAll($select);
            if($rows instanceof Zend_Db_Table_Rowset){
                return $rows->toArray();
            } 
            return false;
    }
    
}