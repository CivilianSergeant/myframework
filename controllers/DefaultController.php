<?php
namespace Controllers;
use Lib\Response;
use Lib\Request;
use Lib\ORM\Database;
use Models\User;
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

        //$response = new Response("default",['name'=>'Himel']);
        
//        for($i=0; $i<10000; $i++){
//            $user = new User();
//            $user->name = "Himel";
//            $user->email = "mehearaz.uddin@nexdecade.com";
//            $user->address = "Khilgaon,Dhaka,Bangladesh";
//            $user->contact = "+8801714112912";
//            $user->save();
//        }
//        $product = \Models\Product::find(1);
        $products = \Models\Product::select("id,product_name as name")->get();
        
        $order = new \Models\Order;
        $order->invoice_id = time();
        $order->amount = 139999.99;
        $order->order_date = date('Y-m-d H:i:s');
        $order->saveMany($products);
        
        echo '<pre>';
        
        print_r($products);
        $response->setMasterView('default', ['name'=>"Himel"]);
        $response->setSubView('index/home', ['designation'=>'Software Developer']);
        return $response;
    }
    
}
