<?php
namespace App\Views\Auth;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Document Management</title>
    <!-- CSS Link -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="LoginBody">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-10 col-xl-10 col-lg-10">
                <div class="login-container">
                    <div class="row g-0">
                        <!-- Left Side - Image -->
                        <div class="col-lg-6 col-md-12 d-none d-lg-block">
                            <div class="login-right">
                                <div class="image-overlay">
                                    <div class="mb-4">
                                        <i class="fas fa-book fa-5x mb-3" style="color: #f0cc4cff;"></i>
                                        <h2 class="mb-3">Gestion de Documents</h2>
                                        <p class="lead">Organisez, partagez et gérez vos documents en toute simplicité</p>
                                    </div>
                                    
                                    <ul class="feature-list">
                                        <li><i class="fas fa-check"></i> Stockage sécurisé</li>
                                        <li><i class="fas fa-check"></i> Partage collaboratif</li>
                                        <li><i class="fas fa-check"></i> Accès multi-appareils</li>
                                        <li><i class="fas fa-check"></i> Recherche intelligente</li>
                                        <li><i class="fas fa-check"></i> Synchronisation en temps réel</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side - Login Form -->
                        <div class="col-lg-6 col-md-12">
                            <div class="login-left">
                                <div class="logo">
                                    <i class="fas fa-book-open"></i>
                                    <h1>DocManager</h1>
                                </div>
                                
                                <div class="welcome-text">
                                    <h2>Content de vous revoir !</h2>
                                    <p>Connectez-vous à votre compte</p>
                                </div>

                                <!-- Messages d'alerte -->
                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?= $_SESSION['error']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>
                                
                                <?php if (isset($_SESSION['success'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <?= $_SESSION['success']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['success']); ?>
                                <?php endif; ?>

                                <!-- Formulaire de connexion -->
                                <form method="POST" action="/login">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="name@example.com" required
                                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                        <label for="email">
                                            <i class="fas fa-envelope me-2"></i>Adresse email
                                        </label>
                                    </div>
                                    
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Votre mot de passe" required>
                                        <label for="password">
                                            <i class="fas fa-lock me-2"></i>Mot de passe
                                        </label>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">
                                                Se souvenir de moi
                                            </label>
                                        </div>
                                        <a href="#" class="text-decoration-none">Mot de passe oublié ?</a>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-login w-100">
                                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                    </button>
                                </form>
                                
                                <div class="login-links">
                                    <p class="mb-0">Pas encore de compte ? 
                                        <a href="/register" class="fw-bold">Créer un compte</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- main.js Link -->
    <script src="/assets/js/script.js"></script>
</body>
</html>