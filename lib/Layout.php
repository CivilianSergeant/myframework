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
        $request = self::$context->getRequest();
        $sessionData = $request->getSession($name);
        return $sessionData;
    }
    
    public function getFlashMessage($name)
    {
        $request = self::$context->getRequest();
        $sessionData = $request->getSession($name);
        $request->setSessionData($name,null);
        return $sessionData;
    }
    
    public function add($key,$value)
    {
        $this->{$key} = $value;
    }
    
    public function get($key)
    {
        return $this->$key;
    }
}
