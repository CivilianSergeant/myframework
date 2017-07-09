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
 * Description of AbstractMysqlDriver
 *
 * @author Himel
 */
class Mysql implements DatabaseInterface,  RelationInterface {

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
    
    public function query($sqlCommand)
    {
        static::$sqlCommand = $sqlCommand;
        $stmt = self::$connection->query(static::$sqlCommand);
        if(!empty($stmt)){
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_CLASS,  get_class($this->context));
        }else{
            throw new \Exception("SQL ERROR: [".$this->lastQuery()."]"); 
        }
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
    
    public function lastQuery()
    {
        return static::$sqlCommand;
    }

    protected function extractObjectMembers(Model $model, &$columnNames, &$columnValues, &$bindWildCard) {
        $fields = get_object_vars($model);
        foreach ($fields as $field => $fieldVal) {
            if (property_exists($model, $field)) {
                if (isset($fieldVal)) {
                    $columnNames[] = "`" . $field . "`";
                    $columnValues[] = $fieldVal;
                    $bindWildCard[] = "?";
                }
            }
        }
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
        static::$sqlCommand = "DELETE FROM " . static::table;

        $id = $this->id;
        if (!empty($id)) {
            static::$sqlCommand .= " WHERE id=" . $id;
        }
        if (self::$where) {
            static::$sqlCommand .= " WHERE " . self::$where->getWhereClause();
        }
        $stmt = self::$connection->query(static::$sqlCommand);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
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

    public function save() {
        $columnNames = [];
        $columnValues = [];
        $bindWildCard = [];
//        $fields = get_object_vars($this->context);
//        foreach($fields as $field => $fieldVal){
//            if(property_exists($this->context, $field)){
//                if(isset($fieldVal)){
//                    $columnNames[]  = "`".$field."`";
//                    $columnValues[] = $fieldVal;
//                    $bindWildCard[] = "?";
//                }
//            }
//        }

        $this->extractObjectMembers($this->context, $columnNames, $columnValues, $bindWildCard);

        if (!isset($this->context->id)) {
            return $this->create($bindWildCard, $columnNames, $columnValues);
        } else {
            return $this->update($this->context->id, $columnNames, $columnValues);
        }

        return null;
    }

    public function saveMany($collection, $optionalData = NULL) {
        
        $columnNames = [];
        $columnValues = [];
        $bindWildCard = [];
        $this->extractObjectMembers($this->context, $columnNames, $columnValues, $bindWildCard);

        if (!isset($this->context->id)) {
            $object = $this->create($bindWildCard, $columnNames, $columnValues);
        }
        
        if ($object->id) {
            $objectClassName = explode("\\", get_class($this->context));
            $objectName = array_pop($objectClassName);
            $namespace = implode("\\", $objectClassName);
            $objectForeignKey = strtolower($objectName) . "_id";
            foreach ($collection as $model) {
                if(!$model instanceof Model){
                    throw new \Exception(get_class($model)." should be instance of Lib\ORM\Model",400);
                }
                $modelClassName = explode("\\", get_class($model));
                $modelName = array_pop($modelClassName);
                $mapEntityClass = $namespace . "\\" . $objectName . $modelName;
                $modelForeignKey = strtolower($modelName) . "_id";

                $mapEntityObject = new $mapEntityClass;

                $mapEntityObject->$objectForeignKey = $object->id;
                $mapEntityObject->$modelForeignKey = $model->id;

                if (!empty($optionalData) && is_array($optionalData)) {
                    foreach ($optionalData as $key => $val) {
                        if (property_exists($mapEntityObject, $key)) {
                            $mapEntityObject->$key = $val;
                        }
                    }
                }

                $modelColumnNames = [];
                $modelColumnValues = [];
                $modelBindWildCard = [];
                $this->extractObjectMembers($mapEntityObject, $modelColumnNames, $modelColumnValues, $modelBindWildCard);
                self::$sqlCommand = "INSERT INTO " . $mapEntityObject->getTableName() . " (" . implode(",", $modelColumnNames) . ") VALUES (" . implode(",", $modelBindWildCard) . ")";

                $stmt = self::$connection->prepare(static::$sqlCommand);
                if (!empty($stmt)) {
                    $stmt->execute($modelColumnValues);
                    $object->order_products[] = $mapEntityObject;
                }
            }
            return $object;
        }
    }

    private function create($bindWildCard, $columnNames, $columnValues) {
        self::$sqlCommand = "INSERT INTO " . $this->context->getTableName() . " (" . implode(",", $columnNames) . ") VALUES (" . implode(",", $bindWildCard) . ")";

        $stmt = self::$connection->prepare(static::$sqlCommand);
        if (!empty($stmt)) {
            $stmt->execute($columnValues);

            $this->context->id = self::$connection->lastInsertId();

            return $this->context;
        } else {
            throw new \Exception("SQL ERROR: [" . self::$sqlCommand . "]");
        }
    }

    private function update($id, $columnNames, $columnValues) {
        self::$sqlCommand = "UPDATE " . $this->context->getTableName() . " SET ";
        $updateData = [];
        foreach ($columnNames as $i => $col) {
            $updateData[] = "$col=?";
        }
        self::$sqlCommand .= implode(",", $updateData) . " WHERE id=" . $id;
        $stmt = self::$connection->prepare(static::$sqlCommand);
        if (!empty($stmt)) {
            $stmt->execute($columnValues);
            return $stmt->rowCount();
        } else {
            throw new \Exception("SQL ERROR: [" . self::$sqlCommand . "]");
        }
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

}
