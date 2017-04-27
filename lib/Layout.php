<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib;

/**
 * Description of Layout
 *
 * @author Himel
 */
class Layout {
    
    protected static $subView;
    protected static $data;
    
    public function setView($view)
    {
        
        self::$subView = $view;
    }
    
    public function setData($data)
    {
        static::$data = $data;
    }
    
    public function renderSubView(){
        
       echo self::$subView;
    }
}
