<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib;

/**
 * Description of Response
 *
 * @author Himel
 */
class Response {
    
    protected static $view;
    protected static $viewModel;
    protected static $masterView;
    protected static $masterViewModel;
    
    protected $headers;
    protected $sessionData;
    protected $postData;
    protected $getData;
    protected $controllerName;
    protected $methodName;
    protected $param;
    protected $segments;
    
    public function __construct() {
        
        
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

    public function setSessionData($sessionData)
    {
        $this->sessionData = $sessionData;
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
        if(array_key_exists($name, $this->sessionData)){
            return $this->sessionData[$name];
        }
        
        return null;
    }
    
    

            
    public function render()
    {
        $layout = new Layout();
        \Bootstrap\Bootstrap::init($layout);
        if(!empty(self::$view)){
            
            ob_start();
            
            if(!empty(self::$view)){
             
                if(!file_exists('views/'.self::$view.'.php')){
                    throw new \Exception("Sub View not found",404);
                }
                     
                
                $layout->setData(self::$viewModel);
                $config = new Config();
                $layout->config = $config;
                
                if(isset(self::$viewModel) && is_array(self::$viewModel)){
                    if(!empty(self::$viewModel)){
                        foreach(self::$viewModel as $key=>$value){
                            $layout->{$key} =  $value; 
                        }
                    }
                    self::$viewModel['config'] = $config;
                    extract(self::$viewModel);
                }

                include('views/'.self::$view.'.php');
                $layout->setView(ob_get_clean());
                
                if(isset(self::$masterViewModel) && is_array(self::$masterViewModel)){
                    if(!empty(self::$masterViewModel)){
                        foreach(self::$masterViewModel as $key=>$value){
                            $layout->{$key} =  $value; 
                        }
                    }
                    
                    self::$masterViewModel['layout'] = $layout;
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
                        $layout->{$key} =  $value; 
                    }
                }
                self::$masterViewModel['config'] = new Config();
                extract(self::$masterViewModel);
            }
            
            self::$masterViewModel['layout'] = $layout;
            
            if(!empty(self::$masterView)){
                if(!file_exists('views/'.self::$masterView.'.php')){
                    throw new \Exception("View not found",404);
                }
                include('views/'.self::$masterView.'.php');
            }
        }
        
        
    }
}
