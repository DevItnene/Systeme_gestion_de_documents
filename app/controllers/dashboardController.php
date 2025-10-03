<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Views\Dashboard;

class DashboardController {
    private $auth;
    private $dashboard;
    
    public function __construct() {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->dashboard = new Dashboard();
    }

    // Afficher la vue du dashboard selon le role
    public function dashboard() {

        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->dashboard->Dashboard();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";

    }
}

?>