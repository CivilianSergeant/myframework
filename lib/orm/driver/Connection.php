<?php
namespace Lib\ORM\Driver;

use Lib\Config;
/**
 * Description of Connection
 *
 * @author Himel
 */
class Connection {
    
    protected static $conn;
    
    public static function getInstance() {
        
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
        
        return static::$conn;
    }
    
}
