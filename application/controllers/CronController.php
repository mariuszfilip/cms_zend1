<?php 

class CronController extends Zend_Controller_Action
{

    public function init()
    {
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->_helper->layout()->disableLayout();	
    	error_reporting(1);
    	set_time_limit(0);
    	ini_set('memory_limit', '2048M');

    }
    
    public function indexAction(){}
    
    // akutalizacja danych z ftp liffe
    public function updateDataLiffeAction(){
        try{
      		new My_Liffe_Update();
        }catch (Exception $e){
             $this->debug();
            var_dump($e->getMessage());
        }
        $this->debug();
    }
    public function updateDataCmeAction(){
      		new My_Cme_Update();
      		$this->debug();
    }
    public function updateDataNbpAction(){
            new My_Nbp_Update();
            $this->debug();
    }
    protected function debug(){
        
        echo '<div id="debug-panel" style="width:850px;font-family:arial;position:absolute;top:0px;left:0px;display:block;background:#efefef;font-size:11px;color:#000;padding:20px;">';
        $db = Zend_Registry::get('db');
        $profiler = $db->getProfiler();
        $totalTime= $profiler->getTotalElapsedSecs();
        $queryCount= $profiler->getTotalNumQueries();
        $longestTime = 0;
        $longestQuery = null;
        $queries='';
        foreach ($profiler->getQueryProfiles() as $query) {
        if ($query->getElapsedSecs() > $longestTime) {
        $longestTimeÂ  = $query->getElapsedSecs();
        $longestQuery = $query->getQuery();
        }
        $queries .= '<small>('.round($query->getElapsedSecs(),5).' seconds)</small> '.$query->getQuery().'<hr style="border-top:1px solid #cccccc" />';
        }
        echo 'Executed <strong>' . $queryCount . '</strong> queries in <strong>' . $totalTime . ' seconds</strong>' . "<br />";
        echo 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br />";
        echo 'Queries per second: ' . $queryCount / $totalTime . "<br />";
        echo 'Longest query length: ' . $longestTime . "<br />";
        echo "Longest query: \n" . $longestQuery . "<br />";
        echo '<hr />'.$queries;
        echo '</div>';
    
    }
    
}