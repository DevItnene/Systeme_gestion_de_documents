<?php
namespace App\Core;

use App\Models\User;

class Auth {

    public $userModel;
    public function __construct() {
        $this->userModel = new User();
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user["password"])) {
            if ($user["is_active"]) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_role"] = $user["role"];
                $_SESSION["logged_in"] = true;

                $this->userModel->updateLastLogin($user["id"]);

                return true;
            } else {
                $_SESSION["error"] = "Votre compte est désactivé.";
                return false;
            }
        } else { 
            $_SESSION["error"] = "Email ou mot de passe incorrect.";
            return false;
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;
    }

    public function user() {
        if ($this->isLoggedIn()) {
            return [
                "id" => $_SESSION["user_id"],
                "email"=> $_SESSION["user_email"],
                "name" => $_SESSION["user_name"],
                "role"=> $_SESSION["user_role"]
            ];
        }
    }

    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION["user_role"] === 'admin';
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function requireAdmin() {
        $this->requireAuth();

        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Accès non autorisé.';
            header('Location: /dashboard');
            exit;
        }
    }
}
?>