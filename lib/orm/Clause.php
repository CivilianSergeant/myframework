<?php
namespace Lib\ORM;

/**
 * Description of Clause
 *
 * @author Himel
 */
class Clause {
    
    protected $context;
    protected $groupBy;
    protected $having;
    protected $orderBy;
    protected $select;
    protected $from;
    protected $skip;
    protected $take;
    protected $where;
    
    
    
    function __construct($context,$sqlCommand,$type) {
        $this->context = $context;
        $this->{$type} = $sqlCommand;
        $this->groupBy = null;
        $this->having  = null;
        $this->orderBy = null;
        $this->skip    = null;
        $this->take    = null;
    }
    
    public function from($sqlCommand)
    {
        
        if(is_callable($sqlCommand)){
            $temp = $this->select;
            $tempWhere = $this->where;
            $sqlCommand($this,$temp);
           
            $this->from = " FROM ( SELECT ".$this->select . $this->from.")";
            if(!empty($this->where)){
                $this->from .= " WHERE ".$this->where;
            }
            
            $this->select = "";
            $this->select .= $temp;
            $this->where = "";
        }else{
            $this->from = " FROM $sqlCommand";
        }
        
        return $this;
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
    
    public function whereOr($sqlCommand)
    {
        if(!empty($this->where)){
            if(is_callable($sqlCommand)){
                $this->where .= " OR (";
                $this->where .= $sqlCommand($this);
                $this->where .= ")";
            }else{
                if(substr($this->where,-1) === "("){
                    $this->where .= "$sqlCommand";
                }else{
                    $this->where .= " OR $sqlCommand";
                }
            }
        }else{
            $this->where = " $sqlCommand";
        }
        return $this;
    }
    
    public function getSelectClause()
    {
        return $this->select;
    }
    
    public function getFromClause()
    {
        return $this->from;
    }
    
    public function getWhereClause()
    {
        return $this->where;
    }
    
    public function orderBy($orderBy)
    {
        if(is_array($orderBy)){
            $this->orderBy = $orderBy;
        }else{
            $this->orderBy[] = $orderBy;
        }
        return $this;
    }
    
    public function getOrderBy()
    {
        return $this->orderBy;
    }
    
    public function groupBy($groupBy)
    {
        $this->groupBy[] = $groupBy;
        return $this;
    }
    
    public function getGroupBy()
    {
        return $this->groupBy;
    }
    
    public function having($having)
    {
        $this->having = $having;
        return $this;
    }
    
    public function getHaving()
    {
        return $this->having;
    }
    
    public function take($limit)
    {
        $this->take = $limit;
        return $this;
    }
    
    public function getLimit()
    {
        return $this->take;
    }
    
    public function skip($offset)
    {
        $this->skip = $offset;
        return $this;
    }
    
    public function getOffset()
    {
        return $this->skip;
    }
    
    public function get()
    {
        return $this->context->get();
    }
    
    public function count()
    {
        return $this->context->count();
    }
    
    public function first()
    {
        return $this->context->first();
    }
    
    public function select($sqlCommand)
    {
        $this->select = $sqlCommand;
        return $this;
    }
}
