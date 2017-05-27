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
    protected $relationType;
    
    const oneToOne = 1;
    const oneToMany = 2;
    const belongsToOne = 3;
    const belongsToMany = 4;
    
    
    public function __construct($context,$relationType) {
        $this->context = $context;
        $this->orderBy = null;
        $this->relationType = $relationType;
    }
    
    public function first()
    {
        if(in_array($this->relationType,[self::oneToMany,self::belongsToMany])){
            throw new \Exception("Call get method");
        }
        
        $caller = new $this->class();
        $caller->getWhere()->select("*");
        if($this->relationType == self::oneToOne){
            $caller->where($this->foreignKey."=".$this->context->getId());
        }else if($this->relationType == self::belongsToOne){
            $caller->where($this->primaryKey."=".$this->context->getForeignKey());
        }
        return $caller->first();
    }
    
    public function getRelationType()
    {
        return $this->relationType;
    }

    
    public function get()
    {
        if(in_array($this->relationType,[self::oneToOne,self::belongsToOne])){
            throw new \Exception("Call first method");
        }
        
        $caller = new $this->class();
        $clause = $caller->getWhere();
        
        $clause->select("*");
        
        if($this->relationType == self::oneToMany){
            $clause->where($this->foreignKey."=".$this->context->getId());
        }else if($this->relationType == self::belongsToMany){
            $clause->where($this->primaryKey."=".$this->context->getForeignKey());
        }
        
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
