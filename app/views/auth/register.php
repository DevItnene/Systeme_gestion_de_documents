<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Document Management</title>
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
                        <!-- Left Side - Register Form -->
                        <div class="col-lg-6 col-md-12">
                            <div class="login-left">
                                <div class="logo">
                                    <i class="fas fa-book-open"></i>
                                    <h1>DocManager</h1>
                                </div>
                                
                                <div class="welcome-text">
                                    <h2>Créer un compte</h2>
                                    <p>Rejoignez notre plateforme de gestion de documents</p>
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

                                <!-- Formulaire d'inscription -->
                                <form method="POST" action="/register">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="name" name="name" 
                                               placeholder="Votre nom complet" required
                                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                        <label for="name">
                                            <i class="fas fa-user me-2"></i>Nom complet
                                        </label>
                                    </div>
                                    
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
                                               placeholder="Votre mot de passe" required minlength="6">
                                        <label for="password">
                                            <i class="fas fa-lock me-2"></i>Mot de passe
                                        </label>
                                        <div class="form-text">Minimum 6 caractères</div>
                                    </div>
                                    
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="password_confirm" 
                                               name="password_confirm" placeholder="Confirmer le mot de passe" required>
                                        <label for="password_confirm">
                                            <i class="fas fa-lock me-2"></i>Confirmer le mot de passe
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            J'accepte les <a href="#" class="text-decoration-none">conditions d'utilisation</a>
                                        </label>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-login w-100">
                                        <i class="fas fa-user-plus me-2"></i>Créer mon compte
                                    </button>
                                </form>
                                
                                <div class="login-links">
                                    <p class="mb-0">Déjà un compte ? 
                                        <a href="/login" class="fw-bold">Se connecter</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side - Image -->
                        <div class="col-lg-6 col-md-12 d-none d-lg-block">
                            <div class="login-right">
                                <div class="image-overlay">
                                    <div class="mb-4">
                                        <i class="fas fa-users fa-5x mb-3" style="color: #4cc9f0;"></i>
                                        <h2 class="mb-3">Rejoignez-nous</h2>
                                        <p class="lead">Des milliers de professionnels nous font déjà confiance</p>
                                    </div>
                                    
                                    <ul class="feature-list">
                                        <li><i class="fas fa-check"></i> Interface intuitive et moderne</li>
                                        <li><i class="fas fa-check"></i> Support technique 24/7</li>
                                        <li><i class="fas fa-check"></i> Sauvegarde automatique</li>
                                        <li><i class="fas fa-check"></i> Collaboration en temps réel</li>
                                        <li><i class="fas fa-check"></i> Sécurité de niveau enterprise</li>
                                    </ul>
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
</body>
</html>
