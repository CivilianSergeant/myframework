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
    
    protected static $context;
    protected static $data;
    protected static $subView;
    
    public function __construct(Response $context) {
        self::$context = $context;
    }
    
    public function setView($view)
    {
        self::$subView = $view;
    }
    
    public function setData($data)
    {
        static::$data = $data;
    }
    
    public function renderSubView()
    {
       echo self::$subView;
    }
    
    public function getSession($name)
    {
        return self::$context->getSession($name);
    }
    
    public function add($key,$value)
    {
        $this->{$key} = $value;
    }
}
