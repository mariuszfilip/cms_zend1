<?php

class My_Loader_Autoloader_ExcelWriter implements Zend_Loader_Autoloader_Interface
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
        if ($class === 'ExcelWriter') {
            include '../library/My/Excel/writer.php';
            return $class;
        }

        return false;
    }
}
