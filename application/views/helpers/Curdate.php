<?php
class Zend_View_Helper_Curdate {

     function curdate() { 
       switch(date("n")) { 
          case 1: $m = "stycznia"; break; 
          case 2: $m = "lutego"; break; 
          case 3: $m = "marca"; break; 
          case 4: $m = "kwietnia"; break; 
          case 5: $m = "maja"; break; 
          case 6: $m = "czerwca"; break; 
          case 7: $m = "lipca"; break; 
          case 8: $m = "sierpnia"; break; 
          case 9: $m = "września"; break; 
          case 10: $m = "października"; break; 
          case 11: $m = "listopada"; break; 
          case 12: $m = "grudnia"; break; 
       } 
       switch(date("w")) { 
          case 0: $d = "Niedziela"; break; 
          case 1: $d = "Poniedziałek"; break; 
          case 2: $d = "Wtorek"; break; 
          case 3: $d = "Środa"; break; 
          case 4: $d = "Czwartek"; break; 
          case 5: $d = "Piątek"; break; 
          case 6: $d = "Sobota"; break; 
       } 
       return $d.", ".date("j")." ".$m." ".date("Y")."r."; 
    } 
}
?>