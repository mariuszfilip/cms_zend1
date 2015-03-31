<?php

class My_Loader_Autoloader_SendCampaign implements Zend_Loader_Autoloader_Interface
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
        if ($class === 'SendCampaign') {
            include '../library/My/SendCampaign.php';
            return $class;
        }

        return false;
    }
}
