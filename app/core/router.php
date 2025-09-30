<?php
namespace App\Core;

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
        echo "<div class='container'>";
        echo "<h1>404 - Page non trouvée</h1>";
        echo "<p><a href='/'>Retour à l'accueil</a></p>";
        echo "</div>";

        exit;
    }
}
?>