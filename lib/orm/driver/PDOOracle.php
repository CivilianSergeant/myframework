<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lib\ORM\Driver;
use Lib\Interfaces\DatabaseInterface;
use Lib\Interfaces\RelationInterface;
/**
 * Description of PDOOracle
 *
 * @author Himel
 */
class PDOOracle implements DatabaseInterface, RelationInterface{
    
    public function belongsTo($modelClassName, $foreignKey, $primaryKey) {
        
    }

    public function belongsToMany($modelClassName, $foreignKey, $primaryKey) {
        
    }

    public function count() {
        
    }

    public function delete() {
        
    }

    public function first() {
        
    }

    public function get() {
        
    }

    public function hasMany($modelClassName, $primaryKey, $foreignKey) {
        
    }

    public function hasOne($modelClassName, $primaryKey, $foreignKey) {
        
    }

    public function save() {
        
    }

    public function saveMany($collection, $optionalData = NULL) {
        
    }

//put your code here
}
