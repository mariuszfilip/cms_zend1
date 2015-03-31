<?php

class Application_Model_DbTable_ExchangeFiles extends Zend_Db_Table_Abstract
{

    protected $_name = 'exchange_files';
    protected $_primary = 'id';

   		public function getFiles($id_calculations){
			
			return $this->fetchAll($this->select()->where('id_calculations = ?',$id_calculations));
			
		}
		public function getFile($id){
			
			return $this->fetchAll($this->select()->where('id = ?',$id));
			
		}
		public function getFilesAll(){
			
			return $this->fetchAll($this->select());
			
		}
		public function find($name){
			
			return $this->fetchAll($this->select()->from(array('f'=>'files'),array('id'))->where('f.name like "%'.$name.'%"'));
			
		}
		public function findCurrency($id){
			return $this->fetchAll($this->select()->where('id=?',$id));
			
		}
		public function getFilesSelect(){
			
			$select  = $this->_db->select()->from($this->_name,array('key' => 'id','value' => 'name'));
			$result = $this->getAdapter()->fetchAll($select);
			return $result;
			
		}
		public function checkGoogle($name){
			$date = strtotime(date('d-m-Y'));
			//->where('last_modified=?',$date)
			$select = $this->select()->from(array('f'=>'files'),array('id'))->where('f.name_file=?',$name);
			$row = $this->fetchAll($select);
			if($row instanceof Zend_Db_Table_Rowset){
					return $row;
			}
			return false;
		
		}
		public function updateModify($name){
			$result = $this->fetchAll($this->select()->from(array('f'=>'files'),array('id'))->where('f.name_file=?',$name));
			$result_files= $result->current();
			$date = strtotime(date('d-m-Y'));
			
			//$data = array('last_modify' => $date);
			$this->update($data,'id = '.(int)$result_files->id);
			
			return true;
		
		}
        public function updateModifyFile($id){
			$date = strtotime(date('d-m-Y'));

			
            $data = array('last_modified' => $date);
             $this->update($data,'id = '.(int)$id);
            return true;
		
		}
		public function updateModifyFileToNewSeries($id){
            $data = array('last_update_data' => new Zend_Db_Expr('NOW()'));
		    $this->update($data,'id = '.(int)$id);
		    return true;

		}
		public function getInfo($name){
			
			return $this->fetchAll($this->select()->from(array('f'=>'files'))->where('f.name_file=?',$name));
			
		}
		public function getFilesMaterials(){
		
			return $this->fetchAll($this->select()->where('type = ?',1));
		
		}
        public function getFilesMaterialsRand(){
		
			return $this->fetchAll($this->select()->where('type = ?',1)->order("rand()")->limit(2));
		
		}
        public function getFilesMaterialsRandGeneral(){
		
			return $this->fetchAll($this->select()->where('type = ?',1)->order("rand()")->limit(4));
		
		}
		public function getFilesProducts(){
		
			return $this->fetchAll($this->select()->where('type = ?',2));
		
		}
		public function getIdCurrencyDivision($first_currency,$second_currency){
	 		return $this->fetchAll($this->select()->where('to_currency=?',$first_currency)->where('from_currency=?',$second_currency));
		}
		public function getIdCurrencyMultiplication($first_currency,$second_currency){
	 		return $this->fetchAll($this->select()->where('to_currency=?',$second_currency)->where('from_currency=?',$first_currency));
		}
		public function getFilesMaterialsGroupYear(){
					return $this->fetchAll ( $this->select ()->from ( array ('f' => 'files' ), array ('id', 'type' ,'name') )->setIntegrityCheck ( false )->joinLeft ( array ('d' => 'data' ), 'd.id_file = f.id', array ('YEAR( FROM_UNIXTIME( d.date )) as year' ) )->where ( 'f.type=?', 1)->group('f.id,YEAR( FROM_UNIXTIME( d.date ))') );
					//return $this->fetchAll ( $this->select ()->from ( array ('f' => 'files' ), array ('id', 'type' ,'name') )->where ( 'f.type=?', 1));
		
		}
		public function getFileNoModified(){
		    $date = date('d-m-Y');
		    $select = $this->select();
		    $select->where('last_modified <?',strtotime($date));
		    $select->where('name_catalog_file !=?','');
		    $select->limit(1);
		    $row = $this->fetchRow($select);
		    if($row instanceof Zend_Db_Table_Row){
		        return $row->toArray();
		    } 
		    return false;
		}
	    public function insertFile($data){
	        return $this->insert($data);
	    }
         public function updateFile($data,$id){
            $id = (int)$id; 
	        return $this->update($data,'id='.$id);
	    }
	    public function getOneById($id){
	        $select = $this->select();
	        $select->where('id=?',$id);
	        $row = $this->fetchRow($select);
	        if($row instanceof Zend_Db_Table_Row){
	            return $row->toArray();
	        }
	        return false;
	    }
	    public function getFilePredefine(){
	        $select = $this->select();
	        $select->where('predefine=?',1);
	        $select->where('last_update_data <= DATE_SUB( now( ) , INTERVAL 10 MINUTE )');
	        $select->limit(1);
	        $row = $this->fetchRow($select);
	        if($row instanceof Zend_Db_Table_Row){
	            return $row->toArray();
	        }
	        return false;
	    }
	    public function getFilesByList($list){
	        $select  = $this->_db->select()->from($this->_name,array('key' => 'id','value' => 'name'))->where('id in ('.$list.')');
			$result = $this->getAdapter()->fetchAll($select);
			return $result;
	    
	    }
        public function getFilesTrading(){
            $select = $this->select();
            $select->where('name_catalog_file!=?','');
            $rows = $this->fetchAll($select);
            if($rows instanceof Zend_Db_Table_Rowset){
                return $rows;
            }
            return false;
        }
		public function checkExistFile($name){
			return $this->fetchAll($this->select()->where('name_file=?', $name));
		
		}
		public function getFilesAllBossa(){
			$select = $this->select();
			$select->where('source=?','bossa');
			$select->where('last_update_data <= DATE_SUB( now( ) , INTERVAL 10 MINUTE )');
			$rows = $this->fetchAll($select);
			return $rows;
			
		}
		public function updateBoosaDate($id){
		    $id = (int)$id;
		    $data = array('last_update_data' => new Zend_Db_Expr('NOW()'));
		    $this->update($data,'id='.$id);
		}
		public function getFileToBossaUpdate($name){
		    $select = $this->select();
		    $select->where('name=?',$name);
		    $select->where('source=?','bossa');
		    $select->limit(1);
		    $row = $this->fetchRow($select);
		    if($row instanceof Zend_Db_Table_Row){
		        return $row->toArray();
		    }
		    return false;
		}
		public function getFileToLiffeUpdate($name){
		    $select = $this->select();
		    $select->where('name=?',$name);
		    $select->where('source=?','liffe');
		    $select->limit(1);
		    $row = $this->fetchRow($select);
		    if($row instanceof Zend_Db_Table_Row){
		        return $row->toArray();
		    }
		    return false;
		}
		public function getFileToLiffeUpdateBySymbol($name){
		    $select = $this->select();
		    $select->where('symbol=?',$name);
		    $select->where('source=?','liffe');
		    $select->limit(1);
		    $row = $this->fetchRow($select);
		    if($row instanceof Zend_Db_Table_Row){
		        return $row->toArray();
		    }
		    return false;
		}
        public function getFileToCmeUpdate($name){
		    $select = $this->select();
		    $select->where('name=?',$name);
		    $select->where('source=?','cme');
		    $select->limit(1);
		    $row = $this->fetchRow($select);
		    if($row instanceof Zend_Db_Table_Row){
		        return $row->toArray();
		    }
		    return false;
		}
		
        public function getFileBySymbolToCmeUpdate($name){
		    $select = $this->select();
		    $select->where('symbol=?',$name);
		    $select->where('source=?','cme');
		    $select->limit(1);
		    $row = $this->fetchRow($select);
		    if($row instanceof Zend_Db_Table_Row){
		        return $row->toArray();
		    }
		    return false;
		}
		
        public function getFileBySymbolToNbpUpdate($name){
		    $select = $this->select();
		    $select->where('symbol=?',$name);
		    $select->where('source=?','nbp');
		    $select->limit(1);
		    $row = $this->fetchRow($select);
		    if($row instanceof Zend_Db_Table_Row){
		        return $row->toArray();
		    }
		    return false;
		}
		public function getFilesToLiffeUpdate(){
		    $select = $this->select();
		    $select->where('source=?','liffe');
		    $rows = $this->fetchAll($select);
		    if($rows instanceof Zend_Db_Table_Rowset){
		        $aRows = $rows->toArray();
		        $aResult = array();
		        foreach($aRows as $row){
		            $aResult[]=$row['symbol'];
		        }
		        return $aResult;
		    }
		    return false;
		}

}