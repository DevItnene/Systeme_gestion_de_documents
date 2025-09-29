<?php
namespace App\Core;

use AltoRouter;
class Router {
    private $ViewPath;
    private $router;
    public function __construct($ViewPath) {
        $this->ViewPath = $ViewPath;
        $this->router = new AltoRouter();
    }

    public function get($url, $view, ?string $name = null) {
        $this->router->map('GET', $url, $view, $name);
        return $this;
    }

    public function run() {
        $match = $this->router->match();
        $view = $match['target'];
        require $this->ViewPath . \DIRECTORY_SEPARATOR . $view . '.php'; 
        return $this;
    }
}
?>