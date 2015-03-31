<?php
class Zend_View_Helper_Page {

	private $id_page;

	
	public function page($id_page){
	     $id_page = (int)$id_page;
	     $oPageModel = new Application_Model_DbTable_Page();
         $aPageModel = $oPageModel->getOnePage($id_page);
         $html = '';
         $aPageModelLeft = array();
         $aPageModelCenter = array();
         $aPageModelRight = array();
         $oPageContentModel = new Application_Model_DbTable_PageContent();  
         $count = 0;
         if($aPageModel['id_left_column'] != 0){
             $aPageModelLeft = $oPageContentModel->getOnePageContent($aPageModel['id_left_column']);
             $count++;
             
         }
	     if($aPageModel['id_center_column'] != 0){
             $aPageModelCenter = $oPageContentModel->getOnePageContent($aPageModel['id_center_column']);
             $count++;
         }
	    if($aPageModel['id_right_column'] != 0){
             $aPageModelRight = $oPageContentModel->getOnePageContent($aPageModel['id_right_column']);
             $count++;
         }
         $column='';
	     if($aPageModel['general'] != 1){
              $column = 'column_';        
         }   
         if(!empty($aPageModelLeft)){

             $html .= $this->generateHtml($aPageModelLeft,$column.'left');
         } 
         if(!empty($aPageModelCenter)){
             if($count == 1){
                 $html .= $this->generateHtml($aPageModelCenter,$column.'center_all');                    
             }else{
                 $html .= $this->generateHtml($aPageModelCenter,$column.'center'); 
             }
                      
         } 
         if(!empty($aPageModelRight)){
             if($count == 2){
                 $html .= $this->generateHtml($aPageModelRight,$column.'center_right');    
             }else{
                 $html .= $this->generateHtml($aPageModelRight,$column.'right');    
             }
             
             
         }
         return $html;     
	}
	protected function generateHtml($data_page,$position){
	    $html = '<div class="'.$position.'">';
	    //$html.= '<h1>'.$data_page['title'].'</h1>';
	    $html.= $this->findspecialtags($data_page['content']);
	    $html.= '</div>';
	    return $html;
	    
	    
	}
	protected function findspecialtags($content){
	    $view = new Zend_View();
	    preg_match_all('/\[(.*)\]/',$content,$result);
	    if(!empty($result)){
	       $array_long = $result[0];
	       $array_short = $result[1];
	       foreach($array_short as $key => $value){
	           $action = explode('_',$value);
	           $count = count($action);
	            $conf = array();
	           if(isset($action[2]) && isset($action[3]) ){
	              $conf[$action[2]]=$action[3]; 
	           }
	           $conf['limit']=1;
	           $replace = $view->action($action[1],'show','default',$conf);
	           $content = str_replace($array_long[$key], $replace,$content);
	           
	       }
	       
	    }
	    return $content;
	    
	    
	}
}
?>