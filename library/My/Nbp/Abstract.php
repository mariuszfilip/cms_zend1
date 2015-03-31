<?php 
abstract class My_Nbp_Abstract{
	protected $_files;
	protected function insertXmlFileToDatabase($kurs,$id_file,$date,$name){
		$file = new Application_Model_DbTable_Data();
		$data = array (
												'id_file' => $id_file,
												'date' => $date,
												'name' => $name,
												'open' => floatval($kurs),
												'high' => floatval($kurs),
												'low' => floatval($kurs),
												'close' => floatval($kurs)
		);
		$file->insert($data);
	
	}

	
}