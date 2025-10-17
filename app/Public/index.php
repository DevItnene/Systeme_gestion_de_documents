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
$router->post("/register", "AuthController@register");
$router->get("/logout", "AuthController@LogOut");

// Routes 
$router->get("/documents","DocumentController@documentList");
$router->post("/documents/update","DocumentController@update");
$router->post("/documents/delete","DocumentController@delete");
$router->get("/documents/download/[i:id]","DocumentController@download");
$router->get("/upload","DocumentController@uploadPage");
$router->post("/documents/insert","DocumentController@insert");
$router->post("/documents/shareDocument","DocumentController@shareDocument");
$router->get("/shareDocuments","DocumentController@shareDocumentList");


$router->run();





