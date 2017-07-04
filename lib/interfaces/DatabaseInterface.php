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
    
    /**
     * To Save One To Many Records
     * @param array $collection 
     * @param array $optionalData, assoc array as optional data
     */
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
