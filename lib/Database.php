<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib;

/**
 * Description of Database
 *
 * @author Himel
 */
class Database {
    
    const table = null;
    protected static $conn;
    protected static $self;
    protected static $take;
    protected static $skip;
    protected static $sqlCommand;
    protected static $where;
    protected static $select;
    protected static $sortBy;
    protected static $sortOrder;


    private function __construct() 
    {
        if(empty(self::$conn)){
            
            $databaseConfig = Config::get('database');
            $host   = $databaseConfig['host'];
            $driver = $databaseConfig['driver'];
            $user   = $databaseConfig['user'];
            $pass   = $databaseConfig['pass'];
            $dbName = $databaseConfig['dbname']; 
            if(empty($databaseConfig) || empty($driver) || empty($host) || empty($user) || empty($dbName)){
                throw new \Exception("Check Database configuration at config.php",500);
            }
            
            self::$conn = new \PDO("$driver:host=$host;dbname=$dbName",$user,$pass);
        }
    }
    
    public static function find($id)
    {
        $self = new static;
        static::$sqlCommand = "SELECT * FROM ".static::table." WHERE id=".$id;
        $stmt = self::$conn->query(static::$sqlCommand);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS,  get_class($self));
        return $stmt->fetch();
    }

    public static function all($take=null,$skip=null,$sortBy=null,$sortOrder=null)
    {
        
        $self = new static;
        static::$sqlCommand = "SELECT * FROM ".static::table;
        
        if(!empty($sortBy) && !empty($sortOrder)){
            static::$sqlCommand .= " ORDER BY `$sortBy` $sortOrder";
        }
        
        if(!empty($take)){
            static::$sqlCommand .= " LIMIT ".$take;
        }
        if(isset($skip)){
            static::$sqlCommand .= " OFFSET ".$skip;
        }
        
        $stmt = self::$conn->query(static::$sqlCommand);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS,  get_class($self));
    }
    
    public static function query($sqlCommand)
    {
        
        $self = new static;
        static::$sqlCommand = $sqlCommand;
        $stmt = self::$conn->query(static::$sqlCommand);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS,  get_class($self));
    }
    
//    public static function Where($sqlCommand)
//    {
//        $self = new static;
//        self::$where = $sqlCommand;
//        return $self;
//    }
    
    public function where($sqlCommand)
    {
        self::$where = $sqlCommand;
        return $this;
    }
    
    public static function select($select=null)
    {
        $self = new static;
        self::$select = $select;
        return $self;
    }
    
    public function count()
    {
        static::$sqlCommand = "SELECT COUNT(*) as total FROM ".static::table;
        if(self::$where){
            static::$sqlCommand .= " WHERE ".self::$where;
        }
     
        $stmt = self::$conn->query(static::$sqlCommand);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        return $stmt->fetch();
    }
    
    public function get()
    {
        if(empty(self::$select)){
            self::$select = "*";
        }
        static::$sqlCommand = "SELECT ".self::$select." FROM ".static::table;
        
        if(self::$where){
            static::$sqlCommand .= " WHERE ".self::$where;
        }
        
        if(!empty(self::$sortBy) && !empty(self::$sortOrder)){
            static::$sqlCommand .= " ORDER BY `".self::$sortBy."` ".self::$sortOrder;
        }
        
        $stmt = self::$conn->query(static::$sqlCommand);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS,  get_class($this));
        return $stmt->fetchAll();
    }
    
    public function sort($sortBy,$sortOrder)
    {
        self::$sortBy = $sortBy;
        self::$sortOrder = $sortOrder;
        return $this;
    }
    
    public function __destruct() {
        self::$conn = null;
    }
    
    
}
