<?php
namespace Lib\Interfaces;
use Lib\Response;
use Lib\Request;
/**
 * Description of BootstrapInterface
 *
 * @author Himel
 */
interface BootstrapInterface {
 
    public static function init(Request $req,Response $res);
    
    
    
}
