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
    
    protected static $conn;
    protected static $self;
    protected static $take;
    protected static $skip;
    protected static $sqlCommand;
    
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
                throw new Exception("Check Database configuration at config.php",500);
            }
            
            self::$conn = new \PDO("mysql:host=localhost;dbname=test","root","");
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

    public static function all($take=null,$skip=null)
    {
        
        $self = new static;
        static::$sqlCommand = "SELECT * FROM ".static::table;
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
        $stmt = self::$conn->query($sqlCommand);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS,  get_class($self));
    }
    
    public function __destruct() {
        self::$conn = null;
    }
    
    
}
