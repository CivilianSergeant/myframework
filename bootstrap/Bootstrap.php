<?php
namespace Bootstrap;
use Lib\Interfaces\BootstrapInterface;
use Lib\Response;

/**
 * Write all common code that should execute 
 * before dispatch controller method
 * @author Himel
 */
class Bootstrap implements BootstrapInterface{
   
    public static function init(Response $response) {
        $response->setLayoutData('menu','value');
    }

}
