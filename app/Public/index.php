<?php
namespace App\Public;

use App\Core\Router;

require_once __DIR__ . "/../../vendor/autoload.php";

// $router = new \AltoRouter();

$router = new Router(dirname(__DIR__) ."/Views");

$router
    ->get('/', 'Auth/Login', 'LoginPage')
    ->run();

// use App\Core\Database;
// $dataInfo = new Database();
// $sql = "SELECT * FROM users";

// $result = $dataInfo->query($sql);
// var_dump($result);




