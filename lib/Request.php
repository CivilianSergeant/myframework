<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib;

/**
 * Description of Request
 *
 * @author Himel
 */
class Request {
   protected static $controller;
   protected static $method;
   protected static $params;
   protected static $segments;
   
   
   public function __construct($segments,$controller,$method,$params) {
       self::$segments = $segments;
       self::$controller = "Controllers\\".$controller;
       self::$method     = $method;
       self::$params     = (empty($params))? [] : (is_array($params)? $params : array($params));
       
       
   }
    
   public function dispatchRequest()
   {
       $controller = new self::$controller();
       
       if(empty($controller) || empty(self::$method)){
           throw new \Exception("No Page Found",404);
       }
       
       if(!method_exists($controller, self::$method)){
           throw new \Exception("No Page Found, Does the method exist in the controller?",404);
       }
       
       $response = new Response();
       
       $response->setHeaders(getallheaders());
       $response->setPostData($_POST);
       $response->setGetData($_GET);
       $response->setSessionData('system',$_SESSION);
       $response->setControllerName(self::$controller);
       $response->setMethodName(self::$method);
       $response->setParam(self::$params);
       $response->setSegments(self::$segments);
       
       $response = call_user_func(array($controller,self::$method), $response);
       
       if(empty($response)){
           exit(0);
       }
       
       if($response instanceof \Lib\Response){
           $response->render();
       }else{
           throw new \Exception("Method did not return \Lib\Response Object",404);
       }
   }
   
   
}
