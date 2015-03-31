<?php 
class My_Liffe_Update extends My_Liffe_Abstract{
	public function __construct(){
		
		$this->insert();
	
	}
	protected function insert(){
	
					$go = $this->getFileContent();
					var_dump($go);
					if($go){
					    var_dump(strlen($this->_contentFile));
    					$piec = preg_split("/(\r\n|\n|\r)/", $this->_contentFile);
    					$count=0;
    					$oFiles = new Application_Model_DbTable_ExchangeFiles();
    					$aFileList = $oFiles->getFilesToLiffeUpdate();
    					echo '<pre>';
    					print_r($aFileList);
    					echo '</pre>';
    						foreach ($piec as $piec_go):
    								$pieces=array();
    								$pieces = explode(",", $piec_go);
    								echo '<pre>';
                					print_r($pieces);
                					echo '</pre>';
    									if($pieces[0] != '' && $pieces[1] != ''):
    									    $aFiles = array();
    									    if(!in_array($pieces[1], $aFileList)){
    									        continue;
    									    }
    									    $aFiles = $oFiles->getFileToLiffeUpdateBySymbol($pieces[1]);
    									    if(!$aFiles){
    									        continue;
    									    }
    									    $date=strtotime($pieces[0]);
    										$data_query = new Application_Model_DbTable_Data();
    										$data_query_result=$data_query->isExistSymbol($aFiles['id'],$date);
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
	        echo $aLiffe['date_update_file'];
	        $strotime = $this->getDateUpdateFile($aLiffe['date_update_file']);
    		$name_file = $aLiffe['link'];
    		$name_file_save = $aLiffe['name_file'];
    		$m_d = date('m',$strotime).date('d',$strotime);    		
    		$y_m_d = date('Y',$strotime).date('m',$strotime).date('d',$strotime);
    		$name_file = str_replace("{mmdd}", $m_d, $name_file);
    		$name_file = str_replace("{yyyymmdd}", $y_m_d, $name_file);
            $name_file_save = str_replace("{mmdd}", $m_d, $name_file_save);
            $name_file_save = str_replace("{yyyymmdd}", $y_m_d, $name_file_save);
            echo '<br/>';
            echo $name_file;
            echo '<br/>';
            echo $name_file_save;
            $save_catalog = realpath(APPLICATION_PATH.'/../library/My/Liffe/zrodlo/').'/'.$name_file_save;
            echo '<br/>';
            echo $save_catalog;
            //exit;
            if(!file_exists($save_catalog)){
	        $foo = system("wget -O $save_catalog $name_file  ~",$output);
            }
	        if(file_exists($save_catalog)){
	            $oLiffe->setCount($aLiffe['id'],$strotime);
	            $this->_contentFile = file_get_contents($save_catalog);
	            $i = 1;
	            $strlen = strlen($this->_contentFile);
	            if($strlen > 1){
	                $oLiffe->updateTime($aLiffe['id'],date('Y-m-d',$strotime));
	                return true;
	            }else{
	                $oLiffe->setCount($aLiffe['id'],$strotime);
	                return false;
	            }
	        }else{
	            echo 'Brak pliku.';
	            $oLiffe->setCount($aLiffe['id'],$strotime);
	            return false;
	       }
    		
	    }else{
	        echo 'Brak update.';
	        return false;
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
	protected function rrmdir($dir) {
	    if (is_dir($dir)) {
	        $objects = scandir($dir);
	        foreach ($objects as $object) {
	            if ($object != "." && $object != "..") {
	                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
	            }
	        }
	        reset($objects);
	        rmdir($dir);
	    }
     }
	
	

}