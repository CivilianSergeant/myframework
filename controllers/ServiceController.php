<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controllers;
use Lib\Response;
use Models\User;
/**
 * Description of ServiceController
 *
 * @author Himel
 */
class ServiceController {
    
    public function index(Response $response)
    {
        print_r($response->getSegments());
       // return new \Lib\Response('default',['name'=>'Hello','content'=>'']);
    }
    
    public function about(Response $response)
    {
        $all = User::find(1);
        echo '<pre>';
        print_r($all);
        //$response->setMasterView('default', ['name'=>'about','content'=>null]);
        return $response;
        //return new \Lib\Response('default',['name'=>'About','content'=>'']);
    }
}
