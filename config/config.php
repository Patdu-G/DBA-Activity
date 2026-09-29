<?php
// Fill in the details of the computer that hosts PostgreSQL.
return [
    'demo_mode' => true,              // true = sample data, no PostgreSQL needed. Set to false for the real DB.
    'host'     => '192.168.1.10',     // IP/hostname of the remote computer
    'port'     => '5432',
    'dbname'   => 'your_database',
    'user'     => 'your_user',
    'password' => 'your_password',
    'base_url' => '/lab_borrowing',   // folder name inside htdocs
];
