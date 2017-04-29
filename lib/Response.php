<?php
namespace Lib;

use Bootstrap\Bootstrap;
/**
 * Description of Response
 *
 * @author Himel
 */
class Response {
    
    protected $config;
    protected $controllerName;
    protected $getData;
    protected $headers;
    protected $layout;
    protected $methodName;
    protected $param;
    protected $postData;
    protected $sessionData;
    protected $segments;
    
    protected static $masterView;
    protected static $masterViewModel;
    protected static $view;
    protected static $viewModel;
    
    public function __construct() {
        $this->layout = new Layout($this);
        $this->config = new Config();
    }
    
    
    public function setMasterView($view,$data)
    {
        self::$masterView = $view;
        self::$masterViewModel = $data;
    }
    
    public function setSubView($view,$data)
    {
        self::$view = $view;
        self::$viewModel = $data;
    }
    
    public function getHeaders() 
    {
        return $this->headers;
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
        return $this->postData;
    }

    public function getGetData()
    {
        return $this->getData;
    }
    
    public function getControllerName() {
        return $this->controllerName;
    }

    public function getMethodName() {
        return $this->methodName;
    }

    public function getParam() {
        return $this->param;
    }
    
    public function getSegments() {
        return $this->segments;
    }

    
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function setSessionData($key,$sessionData)
    {
        $this->sessionData = $sessionData;
        if(isset($_SESSION)){
            $_SESSION[$key] = $this->sessionData;
        }
    }

    public function setPostData($postData)
    {
        $this->postData = $postData;
    }

    public function setGetData($getData)
    {
        $this->getData = $getData;
    }
    
    
    public function setControllerName($controllerName) {
        $this->controllerName = $controllerName;
    }
    
    public function setMethodName($methodName) {
        $this->methodName = $methodName;
    }

    public function setParam($param) {
        $this->param = $param;
    }
    
    public function setSegments($segments) {
        $this->segments = $segments;
    }
 
    public function getPost($name)
    {
        
        if(array_key_exists($name, $this->postData)){
            return $this->postData[$name];
        }
        
        return null;
    }
    
    public function get($name)
    {
        if(array_key_exists($name, $this->getData)){
            return $this->getData[$name];
        }
        
        return null;
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
    
    public function setLayoutData($key,$value)
    {
        $this->layout->add($key,$value);
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
    
    public function getConfig()
    {
        return $this->config;
    }

            
    public function render()
    {
        
        Bootstrap::init($this);
        
        if(!empty(self::$view)){
            
            ob_start();
            
            if(!empty(self::$view)){
             
                if(!file_exists('views/'.self::$view.'.php')){
                    throw new \Exception("Sub View not found",404);
                }
                     
                
                $this->layout->setData(self::$viewModel);
                
                $this->layout->config = $this->config;
                
                if(isset(self::$viewModel) && is_array(self::$viewModel)){
                    if(!empty(self::$viewModel)){
                        foreach(self::$viewModel as $key=>$value){
                            $this->layout->add($key,$value); 
                        }
                    }
                    self::$viewModel['config'] = $this->config;
                    extract(self::$viewModel);
                }

                include('views/'.self::$view.'.php');
                $this->layout->setView(ob_get_clean());
                
                if(isset(self::$masterViewModel) && is_array(self::$masterViewModel)){
                    if(!empty(self::$masterViewModel)){
                        foreach(self::$masterViewModel as $key=>$value){
                            $this->layout->add($key,$value); 
                        }
                    }
                    
                    self::$masterViewModel['layout'] = $this->layout;
                    extract(self::$masterViewModel);
                }
                
                
            }
            
            if(!empty(self::$masterView)){
                if(!file_exists('views/'.self::$masterView.'.php')){
                    throw new \Exception("View not found",404);
                }
                include('views/'.self::$masterView.'.php');
            }
            
        }else{
            
            if(isset(self::$masterViewModel) && is_array(self::$masterViewModel)){
                if(!empty(self::$masterViewModel)){
                    foreach(self::$masterViewModel as $key=>$value){
                        $this->layout->add($key,$value); 
                    }
                }
                self::$masterViewModel['config'] = $this->config;
                extract(self::$masterViewModel);
            }
            
            self::$masterViewModel['layout'] = $this->layout;
            
            if(!empty(self::$masterView)){
                if(!file_exists('views/'.self::$masterView.'.php')){
                    throw new \Exception("View not found",404);
                }
                include('views/'.self::$masterView.'.php');
            }
        }
        
        
    }
}
