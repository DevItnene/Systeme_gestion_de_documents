<?php
namespace App\Controllers;

use App\Core\Auth;

class DashboardController {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
        $this->auth->requireAuth(); // Protection de la page
    }

    public function index() {
        $user = $this->auth->user();
        
        // Afficher la vue du dashboard
        echo "<h1>Bienvenue sur le Dashboard</h1>";
        echo "<p>Bonjour <strong>{$user['name']}</strong>!</p>";
        echo "<p>Email: {$user['email']}</p>";
        echo "<p>Rôle: <strong>{$user['role']}</strong></p>";
        
        if ($this->auth->isAdmin()) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🏆 Panneau Administrateur</h3>";
            echo "<p>Vous avez accès aux fonctionnalités administrateur.</p>";
            echo "<ul>";
            echo "<li><a href='/admin/users'>Gérer les utilisateurs</a></li>";
            echo "<li><a href='/admin/settings'>Paramètres système</a></li>";
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>👤 Espace Utilisateur</h3>";
            echo "<p>Vous avez accès aux fonctionnalités utilisateur standard.</p>";
            echo "</div>";
        }
        
        echo "<div style='margin-top: 30px;'>";
        echo "<a href='/documents' style='margin-right: 10px;' class='btn btn-primary'>📁 Mes Documents</a>";
        echo "<a href='/profile' style='margin-right: 10px;' class='btn btn-secondary'>👤 Mon Profil</a>";
        echo "<a href='/logout' class='btn btn-danger'>🚪 Déconnexion</a>";
        echo "</div>";
    }
}

?>