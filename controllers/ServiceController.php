<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Controllers;
use Lib\Response;
use Lib\Request;
use Models\User;
/**
 * Description of ServiceController
 *
 * @author Himel
 */
class ServiceController {
    
    public function __construct(Request $req) {
        
    }

    public function index(Request $req, Response $response)
    {
       
        //print_r($response->getSegments());
       // return new \Lib\Response('default',['name'=>'Hello','content'=>'']);
    }
    
    
}
