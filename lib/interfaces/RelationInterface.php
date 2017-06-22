<?php
namespace Lib\Interfaces;

/**
 * Description of RelationInterface
 *
 * @author Himel
 */
interface RelationInterface {
    
    public function hasOne($modelClassName,$primaryKey,$foreignKey);
    public function hasMany($modelClassName,$primaryKey,$foreignKey);
    public function belongsTo($modelClassName,$foreignKey,$primaryKey);
    public function belongsToMany($modelClassName,$foreignKey,$primaryKey);
    
    
}
