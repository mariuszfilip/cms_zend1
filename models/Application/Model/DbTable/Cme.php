<?php

class Application_Model_DbTable_Cme extends Zend_Db_Table_Abstract
{

    protected $_name = 'exchange_cme';
    protected $_primary = 'id';

    public function getFileToUpdate(){
        $select = $this->select();
        $select->where('date_update <= DATE_SUB(now( ),INTERVAL 1 DAY)');
        $select->limit(1);
        $row = $this->fetchRow($select);
        if($row instanceof Zend_Db_Table_Row){
            return $row->toArray();
        }
        return false;
    
    }
    public function updateTime($id){
        $id = (int)$id;
        $data = array();
        $data['date_update']=date('Y-m-d');
        $this->update($data,'id='.$id);
    }



}