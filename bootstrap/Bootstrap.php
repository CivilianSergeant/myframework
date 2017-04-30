<?php
namespace Bootstrap;
use Lib\Interfaces\BootstrapInterface;
use Lib\Response;
use Lib\Request;

/**
 * Write all common code that should execute 
 * before dispatch controller method
 * @author Himel
 */
class Bootstrap implements BootstrapInterface{
   
    public static function init(Request $req,Response $res) {
        $res->setLayoutData('menu','value');
    }

}
