<?php

class My_Loader_Autoloader_PHPExcel implements Zend_Loader_Autoloader_Interface
{
    /**
     * Autoload a class
     *
     * @param   string $class
     * @return  mixed
     *          False [if unable to load $class]
     *          $class [if $class is successfully loaded]
     */
    public function autoload($class)
    {
        if ($class === 'PHPExcel') {
            include APPLICATION_PATH.'/../library/PHPExcel/PHPExcel.php';
            return $class;
        }
        
        if($class === 'PHPExcel_IOFactory') {
        	include APPLICATION_PATH.'/../library/PHPExcel/PHPExcel/IOFactory.php';
        	return $class;
        }

        return false;
    }
}
