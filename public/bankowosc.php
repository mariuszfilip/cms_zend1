<?php
/**
 * Created by JetBrains PhpStorm.
 * User: admin
 * Date: 17.09.13
 * Time: 20:48
 * To change this template use File | Settings | File Templates.
 */
class bankowosc
{

    public $o_raport;
    public function __construct(){
        $this->o_raport = new raport();
    }
}
