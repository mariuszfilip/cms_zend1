<?php 
class My_Liffe_Update extends My_Liffe_Abstract{
	public function __construct(){
		
		$this->insert();
	
	}
	protected function insert(){
	
					$go = $this->getFileContent();
					if($go){
    					$piec = preg_split("/(\r\n|\n|\r)/", $this->_contentFile);
    					$count=0;
    					$oFiles = new Application_Model_DbTable_ExchangeFiles();
                        var_dump(count($piec));
    						foreach ($piec as $piec_go):
    					    
    								$pieces=array();
    								$pieces = explode(",", $piec_go);
    									if($pieces[0] != '' && $pieces[1] != ''):
    									    $aFiles = array();
    									    $aFiles = $oFiles->getFileToLiffeUpdate($pieces[1]);
    									    if(!$aFiles){
    									        continue;
    									    }
    									    $date=strtotime($pieces[0]);
    										$data_query = new Application_Model_DbTable_Data();
    										$data_query_result=$data_query->isExist($pieces[1],$date);
    										$data_id=$data_query_result->current();
    											if(!empty($pieces) and isset($pieces[0]) and isset($pieces[1]) and $pieces[1] != '' and !isset($data_id->id) and $aFiles['id'] != null):
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
	    $oLiffe = new Application_Model_DbTable_Liffe();
	    $aLiffe = $oLiffe->getFileToUpdate();
	    if($aLiffe){
	        var_dump($aLiffe['date_update_file']);
	        $strotime = $this->getDateUpdateFile($aLiffe['date_update_file']);
    		$name_file = $aLiffe['link'];
    		$m_d = date('m',$strotime).date('d',$strotime);    		
    		$y_m_d = date('Y',$strotime).date('m',$strotime).date('d',$strotime);
    		$name_file = str_replace("{mmdd}", $m_d, $name_file);
    		$name_file = str_replace("{yyyymmdd}", $y_m_d, $name_file);
            if($aLiffe['id'] == 1){
                $path = '/paris/';
            }else{
                $path = '/liffe/';
            }
            echo $name_file;
    		///$exist = $this->checFileExist($name_file,$path);
    		if (!$ch = curl_init($name_file)){
    		    $oLiffe->setCount($aLiffe['id'],$strotime);
    		    die('Brak linku');
    		}
    		curl_setopt($ch, CURLOPT_HEADER, 0);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		$this->_contentFile = curl_exec($ch);
    		$i = 1;
    		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        if ($statusCode >= 200 && $statusCode < 400) {
	        	$strlen = strlen($this->_contentFile);
	            if($strlen > 1){
                    $oLiffe->updateTime($aLiffe['id'],date('Y-m-d',$strotime));
	            }

                curl_close($ch);
                return true;
            } else {
                curl_close($ch);
                return false;
            }
    		
	    }else{
	        die();
	    }
	
	}
	
    protected function getDateUpdateFile($date_update){
        $strotime = strtotime($date_update);
        $strotime = strtotime('+1 day',$strotime);
        $n = date('N',$strotime);
        $n = intval($n);
        if($n == 7 || $n == 6){
            return $this->getDateUpdateFile(date('Y-m-d',$strotime));
        }else{
            return $strotime;
        }
    }
	
	

}