<?php
namespace Controllers;
use Lib\Response;
use Lib\Request;
use Lib\ORM\Database;
/**
 * Description of DefaultController
 *
 * @author Himel
 */
class DefaultController {
    //put your code here

    public function __construct() {
        
    }
    
    public function index(Request $req,Response $response){
        echo '<pre>';
        $user = \Models\User::select("*")->first();
      //  $user->Pages;
        print_r($user);
        //$response = new Response("default",['name'=>'Himel']);
        $response->setMasterView('default', ['name'=>"Himel"]);
        $response->setSubView('index/home', ['designation'=>'Software Developer']);
        return $response;
    }
    
}
