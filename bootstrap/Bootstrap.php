<?php
namespace Bootstrap;
use Lib\Interfaces\BootstrapInterface;
use Lib\Layout;

/**
 * Write all common code that should execute 
 * before dispatch controller method
 * @author Himel
 */
class Bootstrap implements BootstrapInterface{
   
    public static function init(Layout &$layout) {
        $layout->add('menu','value');
    }

}
