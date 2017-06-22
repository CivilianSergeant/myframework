<?php
session_start();

error_reporting(E_ALL | E_STRICT);

require 'lib/Loader.php';
$loader = Loader::getLoader();

$route  = Loader::getRouter(); 
try{
    $route->getRequest()->dispatchRequest();
}catch(\Exception $ex){
    echo '<h1 style="color:red;">ERROR MSG: '.$ex->getMessage().',<br/>File:'.$ex->getFile().'<br/>Line:'.$ex->getLine().'</h1>';
    echo '<hr/>';
    echo '<pre style="color:brown;font-size:15px;">';
    print_r($ex->getTraceAsString());
    echo '</pre>';
    die();
}