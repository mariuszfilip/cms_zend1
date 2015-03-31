<?php 
class My_Cme_Update extends My_Cme_Abstract{
	public function __construct(){
		
		$this->insert();
	
	}
	protected function insert(){
	
					$go = $this->getFileContent();
					if($go){
    					$piec = preg_split("/(\r\n|\n|\r)/", $this->_contentFile);
    					$count=0;
    					$count_foreach = 0;
    					$oFiles = new Application_Model_DbTable_ExchangeFiles();
    					var_dump(count($piec));
    						foreach ($piec as $piec_go):
    						        if($count_foreach == 0){
    						            $count_foreach++;
    						            continue;
    						        }
    					            
    								$pieces=array();
    								$pieces = explode(",", $piec_go);
    									if($pieces[0] != ''):
    									    $aFiles = array();
    									    $aFiles = $oFiles->getFileBySymbolToCmeUpdate(trim($pieces[0]));
    									    if(!$aFiles){
    									        continue;
    									    }
    									    $key = end(array_keys($pieces));
    									    $date=strtotime($pieces[$key]);
    
    										$data_query = new Application_Model_DbTable_Data();
    										$id_file = 0;
    										if(isset($aFiles['id'])){
    										    $id_file = $aFiles['id'];
    										}
    										$data_query_result=$data_query->isExistSymbol($id_file,$date);
    										$data_id=$data_query_result->current();
    											if(!empty($pieces) and isset($pieces[0]) and isset($pieces[11]) and $pieces[13] != '' and !isset($data_id->id) and $aFiles['id'] != null):
    												$this->insertCsvFileToDatabase($pieces,$aFiles['id'],$date);
    												$count++;
    											endif;
    									endif;
    						endforeach;
    						echo $count;
					}else{
					    echo 'Brak pliku.';
					}
	}
	protected function getFileContent(){
	    $oCme = new Application_Model_DbTable_Cme();
	    $aCme = $oCme->getFileToUpdate();
	    if($aCme){
    		$name_file = $aCme['link'];
    		$ch = @curl_init($name_file);
    		curl_setopt($ch, CURLOPT_HEADER, 0);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		$this->_contentFile = curl_exec($ch);
    		$i = 1;
    		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        if ($statusCode >= 200 && $statusCode < 400) {
	            $strlen = strlen($this->_contentFile);
	            if($strlen > 1){
                    $oCme->updateTime($aCme['id']);
	            }
                curl_close($ch);
                return true;
            } else {
                curl_close($ch);
                return false;
            }
    		curl_close($ch);
	    }else{
	        die();
	    }
	
	}
	
	

}