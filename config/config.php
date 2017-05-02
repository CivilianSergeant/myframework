<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return [
    
    
    'base_url' => 'http://localhost/myframework/',

    'special_chars' => '[^a-zA-Z0-9-_]',
    'default_driver' => 'mysql',
    'database' => [
        'mysql' => [
            "driver"=>"mysql",
            "host" => "localhost",
            'user' => "root",
            'pass' => "",
            'dbname'=> "m2m_website"
        ],
        'oracle'=> [
            "driver"=>"oci",
            'user' => "gpsnexnw",
            'pass' => "gpsnexnw",
            'dbname'=> "(DESCRIPTION =(ADDRESS_LIST=(ADDRESS=(PROTOCOL = TCP)(HOST = 192.168.1.60)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=dbdev)))"
        ]
    ]
];
        
