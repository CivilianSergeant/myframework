<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib\ORM\Driver;
use Lib\Interfaces\DatabaseInterface;
use Lib\Interfaces\RelationInterface;
use Lib\ORM\Clause;
use Lib\ORM\Model;
use Lib\ORM\Relation;
/**
 * Description of PDOOracle
 *
 * @author Himel
 */
class PDOOracle implements DatabaseInterface, RelationInterface{
    
    protected static $connection;
    protected static $sqlCommand;
    protected static $select;
    protected static $where;
    protected $orderBy;
    protected $take;
    protected $skip;
    protected $context;

    public function __construct() {
        self::$connection = Connection::getInstance();
    }
    
    protected function prepareQuery() {
        if (empty(self::$select)) {
            $select = static::$select = " * ";
        } else {
            if(!is_string(self::$select)){
                $select = static::$select->getSelectClause();
                $from = static::$select->getFromClause();
                $this->orderBy = static::$select->getOrderBy();
                $groupBy = static::$select->getGroupBy();
                $having = static::$select->getHaving();
                $this->take = static::$select->getLimit();
                $this->skip = static::$select->getOffset();
                $where = static::$select->getWhereClause();
            }else{
                $select = '*';
            }
        }
        static::$sqlCommand = "SELECT " . $select;

        if (!empty($from)) {
            static::$sqlCommand .= " " . $from;
        } else {
            static::$sqlCommand.= " FROM " . $this->context->getTableName();
        }

        if (!empty(self::$where)) {
            $this->orderBy = static::$where->getOrderBy();
            $groupBy = static::$where->getGroupBy();
            $having = static::$where->getHaving();
            $this->take = static::$where->getLimit();
            $this->skip = static::$where->getOffset();
            $where = static::$where->getWhereClause();
        }
       
        if (!empty($where)) {
            self::$sqlCommand .= " WHERE " . $where;
        }

        if (!empty($groupBy)) {
            static::$sqlCommand .= " GROUP BY " . implode(",", $groupBy);
        }

        if (!empty($having)) {
            static::$sqlCommand .= " HAVING " . $having;
        }
    }
    
    public function belongsTo($modelClassName, $foreignKey, $primaryKey) {
        $relation = new Relation($this->context,Relation::belongsToOne);
        $relation->setClass($modelClassName);
        $relation->setPrimaryKey($primaryKey);
        $relation->setForeignKey($foreignKey);
        return $relation;
    }

    public function belongsToMany($modelClassName, $foreignKey, $primaryKey) {
        $relation = new Relation($this->context,Relation::belongsToMany);
        $relation->setClass($modelClassName);
        $relation->setPrimaryKey($primaryKey);
        $relation->setForeignKey($foreignKey);
        return $relation;
    }

    public function count() {
        static::$sqlCommand = "SELECT COUNT(*) as total FROM " . $this->context->getTableName();
        if (self::$where) {
            static::$sqlCommand .= " WHERE " . self::$where->getWhereClause();
        }
        $stmt = self::$connection->query(static::$sqlCommand);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $count = $stmt->fetch();
        return (!empty($count)) ? $count['total'] : 0;
    }

    public function delete() {
        static::$sqlCommand = "DELETE FROM ".$this->context->getTableName();
        $id = $this->ID;
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
    
    public function find($id) {
        static::$sqlCommand = "SELECT * FROM " . $this->context->getTableName() . " WHERE id=" . $id;
        $stmt = self::$connection->query(static::$sqlCommand);
        if(empty($stmt)){
            throw new \Exception("SQL ERROR: [".static::$sqlCommand."]");
        }
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS, get_class($this->context));
        return $stmt->fetch();
    }

    public function first() {
        $this->prepareQuery();

        static::$sqlCommand .= " ORDER BY ID ASC";

        $stmt = self::$connection->query(static::$sqlCommand);
        if (!empty($stmt)) {
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS, get_class($this->context));
            return $stmt->fetch();
        } else {
            throw new \Exception("SQL ERROR: [" . static::$sqlCommand . "]");
        }
    }
    
    public function last() {
        $this->prepareQuery();

        static::$sqlCommand .= " ORDER BY ID DESC";

        $stmt = self::$connection->query(static::$sqlCommand);
        if (!empty($stmt)) {
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS, get_class($this));
            return $stmt->fetch();
        } else {
            throw new \Exception("SQL ERROR: [" . static::$sqlCommand . "]");
        }
    }
    
    public function lastQuery()
    {
        return static::$sqlCommand;
    }

    public function get() {
        $this->prepareQuery();
        if (!empty($this->orderBy)) {
            static::$sqlCommand .= " ORDER BY " . implode(",", self::$orderBy);
        }

        if (!empty($this->take)) {
            $skip = (!empty($this->skip)) ? $this->skip : 0;
            static::$sqlCommand .= " LIMIT " . $skip . " , " . $this->take;
        }
        $stmt = self::$connection->query(static::$sqlCommand);
        if (!empty($stmt)) {
            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS, get_class($this->context));
            return $stmt->fetchAll();
        } else {
            throw new \Exception("SQL ERROR: [" . static::$sqlCommand . "]");
        }
    }

    public function hasMany($modelClassName, $primaryKey, $foreignKey) {
        $relation = new Relation($this->context,Relation::oneToMany);
        $relation->setClass($modelClassName);
        $relation->setPrimaryKey($primaryKey);
        $relation->setForeignKey($foreignKey);
        return $relation;
    }

    public function hasOne($modelClassName, $primaryKey, $foreignKey) {
        $relation = new Relation($this->context,Relation::oneToOne);
        $relation->setClass($modelClassName);
        $relation->setPrimaryKey($primaryKey);
        $relation->setForeignKey($foreignKey);
        return $relation;
    }
    
    protected function extractObjectMembers(Model $model, &$columnNames, &$columnValues, &$bindWildCard,&$isDateExist) {
        $fields = get_object_vars($model);
        
        foreach ($fields as $field => $fieldVal) {
            if (property_exists($model, $field)) {
                if(isset($fieldVal)){
                    $columnNames[]  = $field;
                    if(isset($fieldVal) && !preg_match('/[a-zA-Z0-9]+\(/',$fieldVal)){
                        if(preg_match('/^[0-9]*$/',$fieldVal)){
                        $columnValues[] = $fieldVal;
                    }else{
                            
                            $columnValues[] =  "'".$fieldVal."'";
                            
                        }
                    }else{
                        if(isset($fieldVal)){
                            $isDateExist = true;    
                            $columnValues[] =  $fieldVal;
                        }else{
                            $columnValues[] =  "''";
                        }
                        
                    }
                    $bindWildCard[] = "?";
                }
            }
        }
    }
    
    private function filterQuotes(&$columnValues)
    {
        foreach($columnValues as $i=>$value){
            $valArr = str_split($value);
            $valLen = strlen($value);

            $firstChar = $valArr[0];
            $lastChar  = $valArr[$valLen-1];
            if($firstChar == "'" && $lastChar == "'"){
                unset($valArr[0]);
                unset($valArr[$valLen-1]);
                $columnValues[$i] = implode("",$valArr);
            }
        }
    }

    private function create($bindWildCard,$columnNames,$columnValues,$isDateExist)
    {
        $currentTimesTamp = time();
        self::$sqlCommand = "SELECT ".static::sequence.".nextval as id from dual";
        $stmt = self::$conn->prepare(static::$sqlCommand);
        if(!empty($stmt)){
            $stmt->execute();
            $lastInsertId = $stmt->fetchColumn(0);

            if(!in_array("ID",$columnNames,TRUE)){
                array_unshift($columnNames,"ID");
                array_unshift($bindWildCard, "?");
                array_unshift($columnValues,$lastInsertId);
                array_push($columnNames,"TOKEN");
                array_push($bindWildCard, "?");

                if($isDateExist){ 
                    array_push($columnValues,"'".md5($lastInsertId.$currentTimesTamp)."'");
                }else{
                    array_push($columnValues,md5($lastInsertId.$currentTimesTamp));
                }
            }
            
            if($isDateExist){ 
                self::$sqlCommand = "INSERT INTO ".static::table." (".implode(",",$columnNames).") VALUES (".implode(",",$columnValues).")";
                $insertStmt = self::$conn->query(static::$sqlCommand);
                
            }else{
                
                 $this->filterQuotes($columnValues);
 
                self::$sqlCommand = "INSERT INTO ".static::table." (".implode(",",$columnNames).") VALUES (".implode(",",$bindWildCard).")";
                $insertStmt = self::$conn->prepare(static::$sqlCommand);
                
                if(!empty($insertStmt)){
                    if($isDateExist){ 
                        $insertStmt->execute();
                    }else{
                        $insertStmt->execute($columnValues);
                    }

                    $this->context->ID = $lastInsertId;
                    $this->context->TOKEN = md5($lastInsertId.$currentTimesTamp);
                    
                    return $this;
                }else{
                    throw new \Exception("SQL ERROR: [".$this->lastQuery()."]"); 
                }
            }
            
        }else{
            throw new \Exception("SQL ERROR: [".$this->lastQuery()."]"); 
        }
    }
    
    private function update()
    {
        
    }
    
    public function save() {
        $columnNames  = [];
        $columnValues = [];
        $bindWildCard = [];
        $isDateExist = false;
        
        $this->extractObjectMembers($this->context, $columnNames, $columnValues, $bindWildCard,$isDateExist);
        if (!isset($this->context->ID)) {
            return $this->create($bindWildCard, $columnNames, $columnValues);
        } else {
            return $this->update($this->context->id, $columnNames, $columnValues);
        }

        return null;
    }

    public function saveMany($collection, $optionalData = NULL) {
        
    }
    
    public function setEntity(Model $context) {
        $this->context = $context;
    }

    public function setClause($clause, $type) {
        switch ($type) {
            case Clause::SELECT:
                self::$select = $clause;
                break;
            case Clause::WHERE:
                self::$where = $clause;
                break;
        }
    }

}
