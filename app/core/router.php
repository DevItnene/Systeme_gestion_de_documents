<?php
namespace App\Core;

use App\Controllers\DocumentController;

class Router {
    private $routes = [];
    public function get($path, $controllerAction) {
        $this->routes['GET'][$path] = $controllerAction;
    }

    public function post($path, $controllerAction) {
        $this->routes['POST'][$path] = $controllerAction;
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], \PHP_URL_PATH); // /Login
        $path = str_replace('/index.php','',$path);
        $path = $path ?: '/';

        if (preg_match('#^/documents/download/(\d+)$#', $path, $matches ) && $method === 'GET') {
            $id = $matches[1];
            $controller = new DocumentController();
            $controller->download($id);
            return;
        }

        if (isset($this->routes[$method][$path])) {
            $controllerAction = $this->routes[$method][$path];
            list($controllerName, $action) = explode('@', $controllerAction);
            $controllerClass = "App\\Controllers\\{$controllerName}";

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                $controller->$action();
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

    private function notFound() {
        http_response_code(404);
        echo "
                <!DOCTYPE html>
                <html lang='fr'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>404 - Page non trouvée</title>
                    <!-- Bootstrap CSS -->
                    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                </head>
                <body>
                    <div class='container text-center'>
                        <h1 class='mt-5'>404 - Page non trouvée</h1>
                        <p><a href='/'>Retour à l'accueil</a></p>
                    </div>
                </body>
                </html>
            ";
        
        exit;
    }
}
?>

