<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Models;
use Lib\ORM\Model;
/**
 * Description of Product
 *
 * @author Himel
 */
class Product extends Model{
    private $table = "products";
    
    /**
     * @var string 
     */
    public $id;
    
    public function Order()
    {
        return $this->belongsTo("\Models\Order", "order_id", "id");
    }
}
