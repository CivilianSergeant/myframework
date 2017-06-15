<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib\ORM\Driver;
use Lib\Interfaces\DatabaseInterface;
/**
 * Description of AbstractMysqlDriver
 *
 * @author Himel
 */
class AbstractMysqlDriver implements DatabaseInterface{
    
    public static $connection;
    public static $sqlCommand;
    public static $select;
    public static $where;
    
    
    public function __construct($connection) {
        self::$connection = $connection;
    }
    
    protected function prepareQuery()
    {
        if(empty(self::$select)){
            $select = static::$select = " * ";
            
        }else{
            $select  = static::$select->getSelectClause();
            $from    = static::$select->getFromClause();
            $orderBy = static::$select->getOrderBy();
            $groupBy = static::$select->getGroupBy();
            $having  = static::$select->getHaving();
            $take    = static::$select->getLimit();
            $skip    = static::$select->getOffset();
            
            $where   = static::$select->getWhereClause();
        }
        static::$sqlCommand = "SELECT ".$select;
        
        if(!empty($from)){
            static::$sqlCommand .= " ".$from;
        }else{
            static::$sqlCommand.= " FROM ".static::table;
        }
        
        if(!empty(self::$where)){
            $orderBy = static::$where->getOrderBy();
            $groupBy = static::$where->getGroupBy();
            $having  = static::$where->getHaving();
            $take    = static::$where->getLimit();
            $skip    = static::$where->getOffset();
            $where = static::$where->getWhereClause();
        }
        
        if(!empty($where)){
            self::$sqlCommand .= " WHERE ".$where;
        }
        
        if(!empty($groupBy)){
            static::$sqlCommand .= " GROUP BY ".implode(",",$groupBy);
        }
        
        if(!empty($having)){
            static::$sqlCommand .= " HAVING ".$having;
        }
        
        if(!empty($orderBy)){
            static::$sqlCommand .= " ORDER BY ".implode(",",$orderBy);
        }
        
        
        if(!empty($take)){
            $offset = (!empty($skip))? $skip: 0;
            static::$sqlCommand .= " LIMIT ".$offset." , ".$take;
        }

    }
    
    public static function where($sqlCommand)
    {
        $self = new static;
        self::$select = null;
        self::$where = new Clause($self,$sqlCommand,self::CLAUSE_WHERE);
        return self::$where;
    }
    
    public function count() {
        static::$sqlCommand = "SELECT COUNT(*) as total FROM ".static::table;
        if(self::$where){
            static::$sqlCommand .= " WHERE ".self::$where->getWhereClause();
        } 
        $stmt = self::$connection->query(static::$sqlCommand);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        return $stmt->fetch();
    }

    public function delete() {
        static::$sqlCommand = "DELETE FROM ".static::table;
        
        $id = $this->id;
        if(!empty($id)){
            static::$sqlCommand .= " WHERE id=".$id;
        }
        if(self::$where){
            static::$sqlCommand .= " WHERE ".self::$where->getWhereClause();
        }
        $stmt = self::$connection->query(static::$sqlCommand);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        return $stmt->fetch();
    }

    public function first() {
        $this->prepareQuery();
        $stmt = self::$connection->query(static::$sqlCommand);
        if(!empty($stmt)){
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS,  get_class($this));
            return $stmt->fetch();
        }else{
            throw new \Exception("SQL ERROR: [".static::$sqlCommand."]");
        }
    }

    public function get() {
        $this->prepareQuery();
        $stmt = self::$connection->query(static::$sqlCommand);
        if(!empty($stmt)){
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS,  get_class($this));
            return $stmt->fetchAll();
        }else{
            throw new \Exception("SQL ERROR: [".static::$sqlCommand."]");
        }
    }

    public function save() 
    {
        $columnNames  = [];
        $columnValues = [];
        $bindWildCard = [];
        
        $fields = get_object_vars($this);
        foreach($fields as $field => $fieldVal){
            if(property_exists($this, $field)){
                if(isset($fieldVal)){
                    $columnNames[]  = "`".$field."`";
                    $columnValues[] = $fieldVal;
                    $bindWildCard[] = "?";
                }
            }
        }
        
        if(!isset($this->id)){
            return $this->create($bindWildCard, $columnNames, $columnValues);
        }else{
            return $this->update($columnNames, $columnValues);
        }
        
        return null;
    }
    
    private function create($bindWildCard,$columnNames,$columnValues)
    {
        self::$sqlCommand = "INSERT INTO ".static::table." (".implode(",",$columnNames).") VALUES (".implode(",",$bindWildCard).")"; 
        $stmt = self::$conn->prepare(static::$sqlCommand);
        if(!empty($stmt)){
            $stmt->execute($columnValues);
            
            $this->id = self::$connection->lastInsertId();
            
            return $this;
        }else{
            throw new \Exception("SQL ERROR: [".self::$sqlCommand."]");
        }
    }
    
    private function update($columnNames,$columnValues)
    {
        self::$sqlCommand = "UPDATE ".static::table." SET ";
        $updateData = [];
        foreach($columnNames as $i => $col){
            $updateData[]  = "$col=?"; 
        }
        self::$sqlCommand .= implode(",",$updateData)." WHERE id=".$id;
        $stmt = self::$conn->prepare(static::$sqlCommand);
        if(!empty($stmt)){
            $stmt->execute($columnValues);
            return $stmt->rowCount();
        }else{
            throw new \Exception("SQL ERROR: [".self::$sqlCommand."]");
        }
    }
    
    public function __destruct() {
        if(self::$connection != null){
            self::$connection = null;
        }
    }

}
