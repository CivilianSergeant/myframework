<?php
return [
    'default_route' => 'DefaultController',
    'default_method' => 'index',
    
    'default_login_route' =>'/login',  
   
    'routes' => [
        
       '/login' => [
           'default_route'  => 'ServiceController',
           'default_method' => 'index'
       ],
       '/dashboard' => [
           'default_route'  => 'ServiceController',
           'default_method' => 'about'
       ],
       
       
   ], 
    
];