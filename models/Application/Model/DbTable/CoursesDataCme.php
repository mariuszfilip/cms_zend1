<?php

class Application_Model_DbTable_CoursesDataCme extends Zend_Db_Table_Abstract
{

    protected $_name = 'courses_cme_last_day';
    
    protected function _setupPrimaryKey() 
    { 
        $this->_primary = 'name'; /* name of colum of my view mysql */ 
        parent::_setupPrimaryKey(); 
    } 

    
    public function getData(){
        $select = $this->select();
        $select->where('close is not null');
        $select->where('date is not null');
        $rows = $this->fetchAll($select);
        if($rows instanceof Zend_Db_Table_Rowset){
            return $rows->toArray();
        }
        return array();
    }



}