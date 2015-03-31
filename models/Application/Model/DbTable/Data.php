<?php

class Application_Model_DbTable_Data extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exchange_data';
	protected $_primary = 'id';
	protected $_views = array('cme'=>'courses_cme_last_day'); 
	
	
	public function isExist($name, $date) {
		
		return $this->fetchAll ( $this->select ()->from ( array ('d' => $this->_name ), array ('id' ) )->where ( 'd.date=?', $date )->where ( 'd.name=?', $name ) );
	
	}
	
	public function isExistSymbol($id_file, $date) {
		
		return $this->fetchAll ( $this->select ()->from ( array ('d' => $this->_name ), array ('id' ) )->where ( 'd.date=?', $date )->where ( 'd.id_file=?', $id_file ) );
	
	}
	
	

    
}