<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Router;

class AuthController {

    private $auth;
    public function __construct() {
        $this->auth = new Auth();
    }

    // Afficher la page de Login
    public function showLogin() {
        // $this->auth->login('mahamat@gmail.com', 'admin123');
        if ($this->auth->isLoggedIn()) {
            header("Location: /dashboard");
            exit;
        }
        require_once __DIR__ ."/../Views/Auth/Login.php";
    }

    // Traiter le formulaire de login
    public function LoginTraitment() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /login");
            exit;
        }
        $email = $_POST["email"] ?? '';
        $password = $_POST['password'] ?? '';

        if ($this->auth->login($email, $password)) {
            header('Location: /dashboard');
            exit;
        } else {
            header('Location: /login');
            exit;
        }
    }

    // Afficher la page d'inscription
    public function showRegister() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }

        require_once __DIR__ .'/../Views/Auth/Register.php';
    }

    // Traiter l'inscription
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Register');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if ($this->auth->userModel->createUser($name, $email, $password)) {
            $_SESSION['success'] = "Compte créé avec succès ! Vous pouvez vous connecter.";
            header("Location: /login");
            exit;
        } else {
            $_SESSION["error"] = "Cette adresse email est déjà utilisée.";
            header("Location: /Register");
            exit;
        }
    }

    // Deconnexion
    public function LogOut() {
        $this->auth->logout();
        header("Location: /login");
        exit;
    }
}
?>