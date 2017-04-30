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
    * Authorize user accounts and accessibility
    */
   public function authorise(){
        if(self::$route == Config::get('default_login_route')){
            if($this->isLoggedIn()){
                $this->redirect('/');
            }
        }else{
            if($this->isLoggedIn()){
                $this->redirect(Config::get('default_login_route'));
            }
        }
    }
   
   /**
     * Clear all session data
     * @return boolean
     */
    public function clearSession()
    {
        session_destroy();
        return true;
    }
   
   /**
    * This mehtod should not call directly
    * @throws \Exception
    */
   public function dispatchRequest()
   {
       $response = new Response($this);
       
       $controller = new self::$controller($this);
       
       if(empty($controller) || empty(self::$method)){
           throw new \Exception("No Page Found",404);
       }
       
       if(!method_exists($controller, self::$method)){
           throw new \Exception("No Page Found, Does the method exist in the controller?",404);
       }
       
       $res = call_user_func(array($controller,self::$method), $this,$response);
       
       if(empty($res)){
           exit(0);
       }
       
       if($res instanceof \Lib\Response){
           $res->render();
       }else{
           throw new \Exception("Method did not return \Lib\Response Object",404);
       }
   }
   
   /**
    * Get specific param value of get request
    * @param string $name
    * @return mixed
    */
   public function get($name)
    {
        if(array_key_exists($name, $_GET)){
            return $_GET[$name];
        }
        
        return null;
    }
    
    /**
     * Get controller name
     * @return string
     */
    public function getControllerName() {
        return self::$controller;
    }
   
   /**
     * Get All data of get request params
     * @return array
     */
    public function getData()
    {
        return $_GET;
    }
   
   /**
     * Get all header information of request
     * @return array
     */
    public function getHeaders() 
    {
        return getallheaders();
    }
    
    /**
     * Get method name
     * @return string
     */
    public function getMethodName() {
        return self::$method;
    }
    
    /**
     * Get all url params
     * @return string
     */
    public function getParam() {
        return self::$params;
    }
    
    /**
     * Get specific param value of post request
     * @param string $name
     * @return mixed
     */
    public function getPost($name)
    {
        
        if(array_key_exists($name, $_POST)){
            return $_POST[$name];
        }
        
        return null;
    }
    
    /**
     * Get all post request data
     * @return array
     */
    public function getPostData() 
    {
        return $_POST;
    }
    
    /**
     * Get all url segments
     * @return type
     */
    public function getSegments() {
        return self::$segments;
    }
    
    /**
     * Get session data of specific assoc index
     * @param string $name
     * @return mixed
     */
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
    
    /**
     * Get All Session Data
     * @return array
     */
    public function getSessionData() 
    {
        if(isset($_SESSION)){
            $this->sessionData = $_SESSION;
        }
        return $this->sessionData;
    }
    
    /**
     * Check is user session exist
     * @return boolean
     */
    public function isLoggedIn()
    {
        if(isset($_SESSION)){ 
            if(!empty($_SESSION)){
                return true;
            }
            return false;
        }
        else{
            return false;
        }

    }

    /**
     * Redirect to given route
     * @param string $route
     */
    public function redirect($route)
    {
        header("Location: " . $this->config->getBaseUrl("$route"));
        exit();
    }

    /**
     * Set session data
     * @param string $key
     * @param mixed $sessionData
     */
    public function setSessionData($key,$sessionData)
    {
        $this->sessionData = $sessionData;
        if(isset($_SESSION)){
            $_SESSION[$key] = $this->sessionData;
            return true;
        }
    }
    
    
   
   
}
