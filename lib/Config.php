<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib;

/**
 * Description of Config
 *
 * @author Himel
 */
class Config {
    
    public static function get($name){
        $configs = [];
        if ($handle = opendir('config')) {
            
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
              if(in_array($entry,[' ','.','..'])){
                  continue;
              }
              $config = include "config/$entry";
              foreach($config as $key=>$c){
                  $configs[$key]= $c;
              }
            }
            closedir($handle);
        }

        if(array_key_exists($name, $configs)){
            return $configs[$name];
        }
        return null;
    }
    
    
 
    public function getBaseUrl($param = null)
    {
        $config = include 'config/config.php';
        
        if(array_key_exists('base_url', $config)){
            if (!filter_var($param, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED) === false) {
                return $config['base_url'] . $param;
            }
            else{
                return $param;
            }
        }
        return null;
    }
}
