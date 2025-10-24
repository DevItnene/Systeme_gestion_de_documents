<?php
namespace App\Public;

use App\Core\Router;

// require_once __DIR__ . "/../../App/Controllers/sessions.php";

require_once __DIR__ . "/../../vendor/autoload.php";
session_start();
// require_once __DIR__ . "/../../App/Controllers/sessions.php";
$router = new Router();

$router->get("/", "AuthController@showLogin");
$router->get("/login", "AuthController@showLogin");
$router->post("/login", "AuthController@LoginTraitment");
$router->get("/register", "AuthController@showRegister");
$router->post("/register", "AuthController@register");
$router->get("/logout", "AuthController@LogOut");

$router->get("/dashboard", "DashboardController@dashboard");

// Routes 
$router->get("/documents","DocumentController@documentList");
$router->post("/documents/update","DocumentController@update");
$router->post("/documents/delete","DocumentController@delete");
$router->get("/documents/download/[i:id]","DocumentController@download");
$router->get("/documents/canDownload/[i:id]","DocumentController@canDownload");


$router->get("/upload","DocumentController@uploadPage");
$router->post("/upload/insert","DocumentController@insert");

$router->post("/documents/shareDocument","DocumentController@shareDocument");
$router->get("/shareDocuments","DocumentController@shareDocumentList");
$router->post("/shareDocuments/delete","DocumentController@delete");
$router->post("/shareDocuments/update","DocumentController@updateShareDocument");

$router->post("/publicDocuments/delete","DocumentController@delete");
$router->get("/publicDocuments","DocumentController@publicDocumentList");

$router->get("/Categories","DocumentController@displayCategories");
$router->post("/Categories/delete","DocumentController@delete");
$router->post("/Categories/update","DocumentController@updateCategory");
$router->post("/Categories/insert","DocumentController@insertCategory");


$router->run();





