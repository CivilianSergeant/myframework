<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Models;

/**
 * Description of User
 *
 * @author Himel
 */
class User extends \Lib\ORM\Database{
    
    const table = "menus";
    
    protected $id;
    protected $name;
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }
    
    function Pages()
    {
        return $this->hasMany("\Models\Page", "id", "menu_id")->where("is_published = 1");
    }


    
}
