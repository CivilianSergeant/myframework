<?php

namespace Lib\ORM\Driver;
use Lib\Interfaces\DatabaseInterface;
/**
 * Description of DatabaseAdapter
 *
 * @author Himel
 */
class DatabaseAdapter implements DatabaseInterface{
    
    public static $driver;
    public static $connection;
    
    public function __construct() {
        static::$connection = Connection::getInstance();
        if(Connection::isMysql()){
            static::$driver = new AbstractMysqlDriver(static::$connection);
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
    }

    public function save() {
        static::$driver->save();
    }

}
