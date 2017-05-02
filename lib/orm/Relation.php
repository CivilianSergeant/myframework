<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib\ORM;

/**
 * Description of Relation
 *
 * @author Himel
 */
class Relation {
    
    protected $class;
    protected $context;
    protected $foreignKey;
    protected $primaryKey;
    
    
    public function __construct($context) {
        $this->context = $context;
    }
    
    public function first()
    {
        $caller = new $this->class();
        $caller->getWhere()->select("*")->where($this->foreignKey."=".$this->context->getId());
        return $caller->first();
    }

        
    public function get()
    {
        $caller = new $this->class();
        $caller->getWhere()->select("*")->where($this->foreignKey."=".$this->context->getId());
        return $caller->get();
        
    }
    
    function setClass($class) {
        $this->class = $class;
    }

    function setContext($context) {
        $this->context = $context;
    }

    function setForeignKey($foreignKey) {
        $this->foreignKey = $foreignKey;
    }

    function setPrimaryKey($primaryKey) {
        $this->primaryKey = $primaryKey;
    }
    
    public function where($sqlCommand)
    {
        $this->context->getWhere()->where($sqlCommand);
        return $this;
    }
}
