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
    protected $where;
    protected $orderBy;
    
    
    public function __construct($context) {
        $this->context = $context;
        $this->orderBy = null;
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
        $clause = $caller->getWhere();
        $clause->select("*")->where($this->foreignKey."=".$this->context->getId());
        if(!empty($this->where)){
            $clause->where($this->where);
        }
        if(!empty($this->orderBy)){
            $clause->orderBy($this->orderBy);
        }
        return $clause->get();
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
        if(!empty($this->where)){
            if(is_callable($sqlCommand)){
                $this->where .= " AND (";
                $this->where .= $sqlCommand($this);
                $this->where .= ")";
            }else{
                if(substr($this->where,-1) === "("){
                    $this->where .= "$sqlCommand";
                }else{
                    $this->where .= " AND $sqlCommand";
                }
            }
        }else{
            $this->where = " $sqlCommand";
        }
        return $this;
    }
    
    public function orderBy($orderBy)
    {
        $this->orderBy[] = $orderBy;
        return $this;
    }
}
