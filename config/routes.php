<?php
return [
  'default_route' => 'DefaultController',
  'default_method' => 'index',
    
   'routes' => [
        
       '/services' => [
           'default_route'  => 'ServiceController',
           'default_method' => 'index'
       ],
       '/services/about/:any' => [
           'default_route'  => 'ServiceController',
           'default_method' => 'about'
       ],
       
       
   ], 
    
];