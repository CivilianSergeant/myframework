<?php
namespace Controllers;
use \Lib\Response;
/**
 * Description of DefaultController
 *
 * @author Himel
 */
class DefaultController {
    //put your code here

    public function __construct() {
        
    }
    
    public function index(Response $response){
        
        //$response = new Response("default",['name'=>'Himel']);
        $response->setMasterView('default', ['name'=>"Himel"]);
        $response->setSubView('index/home', ['designation'=>'Software Developer']);
        return $response;
    }
    
}
