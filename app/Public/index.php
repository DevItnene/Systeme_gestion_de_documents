<?php
namespace App\Public;

use App\Core\Router;
session_start();
require_once __DIR__ . "/../../vendor/autoload.php";

$router = new Router();

$router->get("/", "AuthController@showLogin");
$router->get("/login", "AuthController@showLogin");
$router->post("/login", "AuthController@LoginTraitment");
$router->get("/dashboard", "DashboardController@dashboard");
$router->get("/register", "AuthController@showRegister");
$router->get("/logout", "AuthController@LogOut");

$router->get("/documents","DocumentController@documentList");

$router->run();





