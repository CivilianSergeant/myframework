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
   protected static $route;
   protected $controllerName;
   protected $getData;
   protected $sessionData;

   public function __construct($segments,$route,$controller,$method,$params) {
       self::$segments = $segments;
       self::$route    = $route;
       self::$controller = "Controllers\\".$controller;
       self::$method     = $method;
       self::$params     = (empty($params))? [] : (is_array($params)? $params : array($params)); 
   }
   
   /**
    * This mehtod should not call directly
    * @throws \Exception
    */
   public function dispatchRequest()
   {
       $response = new Response(self::$route);
//       $response->setPostData($_POST);
//       $response->setGetData($_GET);
//       $response->setSessionData('system',$_SESSION);
//       $response->setControllerName(self::$controller);
//       $response->setMethodName(self::$method);
//       $response->setParam(self::$params);
//       $response->setSegments(self::$segments);
       
       $controller = new self::$controller($this);
       
       if(empty($controller) || empty(self::$method)){
           throw new \Exception("No Page Found",404);
       }
       
       if(!method_exists($controller, self::$method)){
           throw new \Exception("No Page Found, Does the method exist in the controller?",404);
       }
       
       $response = call_user_func(array($controller,self::$method), $this,$response);
       
       if(empty($response)){
           exit(0);
       }
       
       if($response instanceof \Lib\Response){
           $response->render();
       }else{
           throw new \Exception("Method did not return \Lib\Response Object",404);
       }
   }
   
   /**
     * Get All Header Information of Request
     * @return array
     */
    public function getHeaders() 
    {
        return getallheaders();
    }
    
    public function getSessionData() 
    {
        if(isset($_SESSION)){
            $this->sessionData = $_SESSION;
        }
        return $this->sessionData;
    }

    public function getPostData() 
    {
        return $_POST;
    }

    public function getData()
    {
        return $_GET;
    }
    
    public function redirect($route)
    {
        header("Location: " . $this->config->getBaseUrl("$route"));
        exit();
    }


    public function clearSession()
    {
        session_destroy();
        return true;
    }
    
    public function getPost($name)
    {
        
        if(array_key_exists($name, $_POST)){
            return $_POST[$name];
        }
        
        return null;
    }
    
    public function get($name)
    {
        if(array_key_exists($name, $_GET)){
            return $_GET[$name];
        }
        
        return null;
    }
    
    public function getControllerName() {
        return self::$controller;
    }

    public function getMethodName() {
        return self::$method;
    }

    public function getParam() {
        return self::$params;
    }
    
    public function getSegments() {
        return self::$segments;
    }
    
    public function setSessionData($key,$sessionData)
    {
        $this->sessionData = $sessionData;
        if(isset($_SESSION)){
            $_SESSION[$key] = $this->sessionData;
        }
    }
    
    public function getSession($name)
    {
        if(isset($_SESSION)){
            $this->sessionData = $_SESSION;
            if(array_key_exists($name, $this->sessionData)){
                $session = (array)$this->sessionData[$name];
                array_shift($session);
                return $session;
            }
        }
        
        return null;
    }
   
   
}
