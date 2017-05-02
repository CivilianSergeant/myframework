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
    
    public function getWhereClause()
    {
        return $this->where;
    }
    
    public function orderBy($orderBy)
    {
        $this->orderBy[] = $orderBy;
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
