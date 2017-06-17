<?php

namespace Lib\ORM;
use Lib\Interfaces\DatabaseInterface;
use Lib\ORM\Driver\Connection;
use Lib\ORM\Driver\Mysql;
/**
 * Description of DatabaseAdapter
 *
 * @author Himel
 */
class Model implements DatabaseInterface{
    
    protected static $driver;
    protected static $select;
    protected static $where;
    protected $table;
    
    const CLAUSE_SELECT = 'select';
    
    public function __construct() {
        
        static::$driver = Connection::getDriver();
        static::$driver->setEntity($this);

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
        return $this->table;
    }
    
    public static function where($sqlCommand)
    {
        $self = new static;
        self::$select = null;
        self::$where = new Clause($self,$sqlCommand,self::CLAUSE_WHERE);
        return self::$where;
    }
    
    public static function select($sqlCommand=null)
    {
        $self = new static;
        
//        static::$driver = Connection::getDriver();
//        static::$driver->setEntity($self);
        
        if(empty($sqlCommand)){
            $sqlCommand = "*";
        }
        
        self::$where  = null;
        self::$select = new Clause(static::$driver, $sqlCommand, self::CLAUSE_SELECT);
        static::$driver->setClause(static::$select,self::CLAUSE_SELECT);
        return self::$select;
    }
    
    
    
    

}
