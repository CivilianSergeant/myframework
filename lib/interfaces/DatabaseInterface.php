<?php
namespace Lib\Interfaces;
/**
 * Description of DatabaseInterface
 *
 * @author Himel
 */
interface DatabaseInterface {
    
    /**
     * To Save or Update Entity
     */
    public function save();
    
    public function saveMany($collection,$optionalData=NULL);
    
    /**
     * Get the first item of fetched items
     */
    public function first();
    
    /**
     * Get all fetched items
     */
    public function get();
    
    /**
     * Count Entities
     */
    public function count();
    
    /**
     * Delete Entity
     */
    public function delete();
    
    

}
