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
        $config = include 'config/config.php';
        if(array_key_exists($name, $config)){
            return $config[$name];
        }
        return null;
    }
 
    public function getBaseUrl($param = null)
    {
        $config = include 'config/config.php';
        
        if(array_key_exists('base_url', $config)){
            
            return $config['base_url'] . $param;
        }
        return null;
    }
}
