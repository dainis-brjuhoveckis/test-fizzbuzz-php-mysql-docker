<?php 

// it would be nice to use settings.ini or similar file but let's not invent another framework
const CONFIG = 
    [
        'db' => [
            'dsn' => 'mysql:host=mysql;dbname=foo', 
            'user'=> 'foo',
            'pass' => 'foo'
        ]
    ];

?>