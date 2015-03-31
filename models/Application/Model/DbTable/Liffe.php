<?php

class Application_Model_DbTable_Liffe extends Zend_Db_Table_Abstract
{

    protected $_name = 'exchange_liffe';
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
    public function updateTime($id,$date_file){
        $id = (int)$id;
        $data = array();
        $data['date_update']=date('Y-m-d');
        $data['date_update_file']=$date_file;
        $data['count_trail']=0;
        $this->update($data,'id='.$id);
    }
    public function setCount($id,$strotime){
        $data= array();
        $data['count_trail']=new Zend_Db_Expr('count_trail+1');
        $this->update($data,'id='.intval($id));
        $check = $this->getCountTrial($id);
        if(intval($check['count_trial']) > 3){
            $data = array();
            $data['count_trail']=0;
            $strotime = strtotime('+1 day',$strotime);
            $data['date_update_file']=date('Y-m-d',$strotime);
            $this->update($data,'id='.$id);
        }
    }
    public function getCountTrial($id){
        $select = $this->select();
        $select->where('id=?',$id);
        $row = $this->fetchRow($select);
        return $row->toArray();
    }


}