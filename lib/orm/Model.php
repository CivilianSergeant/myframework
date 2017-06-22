<?php

namespace Lib\ORM;
use Lib\Interfaces\DatabaseInterface;
use Lib\Interfaces\RelationInterface;
use Lib\ORM\Driver\Connection;
use Lib\ORM\Driver\Mysql;
/**
 * Description of DatabaseAdapter
 *
 * @author Himel
 */
class Model implements DatabaseInterface,  RelationInterface{
    
    protected static $driver;
    protected static $select;
    protected static $where;
    
    public function __construct() {
        
        $this->getDriver();
        static::$driver->setEntity($this);

    }
    
    public function __get($name) {
        if(method_exists($this, $name)){
            $relation = $this->$name();
            
            if(in_array($relation->getRelationType(),[Relation::oneToOne,  Relation::belongsToOne])){
                $this->$name = $relation->first();
            }
            if(in_array($relation->getRelationType(),[Relation::oneToMany, Relation::belongsToMany])){
                $this->$name = $relation->get();
            }
            return $this->$name;
        }
    }
    
    public function count() {
        static::$driver->count(); 
    }

    public function delete() {
        static::$driver->delete();
    }

    public function first() {
        static::$driver->first();
    }

    public function get() {
        static::$driver->get();
        Connection::closeConnection();
    }

    public function save() {
        static::$driver->save();
    }
    
    public function getTableName()
    {
        $reflectionObject = new \ReflectionClass($this);
        $property = $reflectionObject->getProperty("table");
        $property->setAccessible(true);
        return $property->getValue($this);
    }
    
    public static function where($sqlCommand)
    {
        $self = new static;
        self::$select = null;
        self::$where = new Clause(static::$driver,$sqlCommand,  Clause::WHERE);
        static::$driver->setClause(static::$where,  Clause::WHERE);
        return self::$where;
    }
    
    public static function find($id)
    {
        static::getDriver();
        static::$driver->setEntity(new static);
        $self = static::$driver->find($id);
        return $self;
    }
    
    public static function select($sqlCommand=null)
    {
        
        if(empty($sqlCommand)){
            $sqlCommand = "*";
        }
        static::getDriver();
        static::$driver->setEntity(new static);
        self::$where  = null;
        self::$select = new Clause(static::$driver, $sqlCommand, Clause::SELECT);
        static::$driver->setClause(static::$select,Clause::SELECT);
        return self::$select;
    }
    
    private static function getDriver()
    {
        if(empty(static::$driver)){
            static::$driver = Connection::getDriver();
        }
    }

    public function saveMany($collection,$optionalData=NULL) {
        if(empty($collection)){
            throw new Exception("saveMany method 1st argument cannot be empty", 400);
        }
        return static::$driver->saveMany($collection);
    }

    public function belongsTo($modelClassName, $foreignKey, $primaryKey) {
        return static::$driver->belongsTo($modelClassName,$foreignKey,$primaryKey);
    }

    public function belongsToMany($modelClassName, $foreignKey, $primaryKey) {
        return static::$driver->belongsToMany($modelClassName,$foreignKey,$primaryKey);
    }

    public function hasMany($modelClassName, $primaryKey, $foreignKey) {
        return static::$driver->hasMany($modelClassName,$primaryKey,$foreignKey);
    }

    public function hasOne($modelClassName, $primaryKey, $foreignKey) {
        return static::$driver->hasOne($modelClassName,$primaryKey,$foreignKey);
    }
    
    public function getSelectClause()
    {
        self::$select = null;
        self::$select = new Clause(static::$driver,null,Clause::SELECT);
        return self::$select;
    }
    
    public static function lastQuery()
    {
        return static::$driver->lastQuery();
    }

}
