<?php 
abstract class My_Cme_Abstract{
	protected $_files;
	protected function insertCsvFileToDatabase($pieces,$id_file,$date){
		$file = new Application_Model_DbTable_Data();
		$data = array (
												'id_file' => $id_file,
												'date' => $date,
												'name' => $pieces[5],
												'open' => floatval($pieces[13]),
												'high' => floatval($pieces[13]),
												'low' => floatval($pieces[13]),
												'close' => floatval($pieces[13])
		);
		$file->insert($data);
	
	}
	protected function insertTxtFileToDatabase($pieces,$id_file,$date){
		$file = new Application_Model_DbTable_Data();
		$data = array (
												'id_file' => $id_file,
												'date' => $date,
												'name' => $pieces[0],
												'volume' => floatval($pieces[6]),
												'open' => floatval($pieces[2]),
												'high' => floatval($pieces[3]),
												'low' => floatval($pieces[4]),
												'close' => floatval($pieces[5])
		);
		$file->insert($data);
	
	}
	
}