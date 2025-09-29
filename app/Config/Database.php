<?php
namespace App\Config;

class Database {
    function database_information() {
        return [
            'host' => 'localhost',
            'dbname' => 'document_management',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4'
        ];
    }
}



?>