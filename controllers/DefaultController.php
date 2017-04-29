<?php
namespace Controllers;
use Lib\Response;
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
    
    public function index(Response $response){
        
        //print_r($response->getLayout());
        
//        $user = \Models\User::select('id')->where("id=1")->where(function($q){
//            $q->where('abc')->where('def');
//        })->where("name='k'")->orderBy("id asc")->orderBy("name desc")->groupBy("id")->groupBy("name");
//        $user->get();
//        print_r(Database::lastQuery());
        
        //$response = new Response("default",['name'=>'Himel']);
        $response->setMasterView('default', ['name'=>"Himel"]);
        $response->setSubView('index/home', ['designation'=>'Software Developer']);
        return $response;
    }
    
}
