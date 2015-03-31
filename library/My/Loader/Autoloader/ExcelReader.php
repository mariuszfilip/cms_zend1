<?php

class My_Loader_Autoloader_ExcelReader implements Zend_Loader_Autoloader_Interface
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
        if ($class === 'Spreadsheet_Excel_Reader') {
            include '../library/My/Excel/reader.php';
            return $class;
        }

        return false;
    }
}
