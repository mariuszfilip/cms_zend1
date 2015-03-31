<?php 
class My_Nbp_Update extends My_Nbp_Abstract{
	public function __construct(){
		
		$this->insert();
	
	}
	protected function insert(){
	
					$go = $this->getFileContent();
					echo 'Sprawdz plik';
					if($go){
					    $count = 0;
					    $doc = new DOMDocument();
                        $doc->loadXML($this->_contentFile);
                        echo $doc->saveXml();
                        $pozycja= $doc->getElementsByTagName('pozycja');
                        $data_publikacji= $doc->getElementsByTagName('data_publikacji')->item(0)->nodeValue;
                        $data_waluty = strtotime($data_publikacji);
                        echo '<br/>';
                        echo $data_publikacji;
                        echo '<br/>';
                        echo $data_waluty;
                        $oFile = new Application_Model_DbTable_ExchangeFiles();
                        if($data_waluty){
                            foreach($pozycja as $element){
                                $kod_waluty = $element->getElementsByTagName('kod_waluty')->item(0)->nodeValue;
                                $aFile = $oFile->getFileBySymbolToNbpUpdate($kod_waluty);
                                if($aFile){
                                    $data_query = new Application_Model_DbTable_Data();
                                    $id_file = 0;
                                    if(isset($aFile['id'])){
                                        $id_file = $aFile['id'];
                                    }
                                    $data_query_result=$data_query->isExistSymbol($id_file,$data_waluty);
                                    $data_id=$data_query_result->current();
                                    if(!isset($data_id->id)){
                                        $kurs_sredni = $element->getElementsByTagName('kurs_sredni')->item(0)->nodeValue;
                                        $kurs_sredni = str_replace(',', '.', $kurs_sredni);
                                        $this->insertXmlFileToDatabase(trim($kurs_sredni), $aFile['id'], $data_waluty, $aFile['name']);
                                        $count++;
                                    }
                                }
                            }
                        }
                                      
					    echo 'Ilosc aktualizacji :: '.$count;
					}else{
					    echo 'Brak pliku.';
					}
						
	}
	protected function getFileContent(){
	    $oNbp = new Application_Model_DbTable_Nbp();
	    $aNbp = $oNbp->getFileToUpdate();
	    if($aNbp){
    		$name_file = $aNbp['link'];
    		$y = date('y');    		
    		$Y = date('Y');
    		$count = $aNbp['count']+1;
    		$count_name = $count;
    		if(strlen($count) < 3){
    		    $strlen = strlen($count);
    		    $count_no_found = 3 - $strlen;
    		    for($i=0;$i<$count_no_found;$i++){
    		        $count_name = '0'.$count_name; 
    		    }
    		}
    		$name_file = str_replace("{Y}", $Y, $name_file);
    		$name_file = str_replace("{y}", $y, $name_file);
    		$name_file = str_replace("{count}", $count_name, $name_file);
    		$ch = @curl_init($name_file);
    		curl_setopt($ch, CURLOPT_HEADER, 0);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		$this->_contentFile = curl_exec($ch);
    		$i = 1;
    		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        if ($statusCode >= 200 && $statusCode < 400) {
	            $info = curl_getinfo($ch,CURLINFO_FILETIME);
	            $strlen = strlen($this->_contentFile);
	            if($strlen > 1){
                    $oNbp->updateTime($aNbp['id']);
	            }
                curl_close($ch);
                return true;
            } else {
                curl_close($ch);
                return false;
            }
	    }else{
	        echo 'Brak pliku.';
	        die();
	    }
	
	}
	
	

}