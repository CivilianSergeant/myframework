<?php
session_start();

error_reporting(E_ALL | E_STRICT);

require 'lib/Loader.php';
$loader = Loader::getLoader();

$route  = Loader::getRouter(); 
try{
$route->getRequest()->dispatchRequest();
}catch(\Exception $ex){
    echo '<h1 style="color:red;">Error: '.$ex->getMessage().'</h1>';
    die();
}