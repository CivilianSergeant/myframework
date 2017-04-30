<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Loader
 *
 * @author Himel
 */
class Loader {
    
    protected static $loader;
    protected static $router;

    private function __construct() 
    {
        spl_autoload_register(array($this,'_loadClasses'));
        
    }
    
    public static function getLoader()
    {
        if(!empty(self::$loader)){
            return self::$loader;
        }
        self::$loader = new self();
        return self::$loader;
    }
    
    private function _loadClasses($className)
    {
	$classNameSegments = explode("\\",$className);
	$i = count($classNameSegments);
	$className = $classNameSegments[$i-1];
	unset($classNameSegments[$i-1]);	
	$namespace = strtolower(implode("/",$classNameSegments));
	$file = $namespace.DIRECTORY_SEPARATOR.$className.'.php';
        if(file_exists($file)){
	      
	    include $file;
        }
        
    }
    
    public static function getRouter()
    {
        if(!empty(self::$router)){
            return self::$router;
        }
        self::$router = new Lib\Router();
        return self::$router;
    }
    
   
}
