<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dashboard - Document Management</title>
    <!-- CSS Link -->
    <link rel='stylesheet' href='/assets/css/style.css'>
    <!-- Bootstrap CSS -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <!-- Google Fonts -->
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
</head>
<body>
    <!-- ===== SIDEBAR ===== -->
    <div class='sidebar'>
        <div class='sidebar-brand'>
            <h2><i class='fas fa-book-open'></i>DocManager</h2>
        </div>

        <div class='sidebar-menu'>
            <a href='/dashboard' class='sidebar-item active'>
                <i class='fas fa-home'></i>
                <span>Tableau de Bord</span>
            </a>

            <a href='/documents' class='sidebar-item'>
                <i class="bi bi-folder2-open me-2"></i>
                <span>Mes Documents</span>
            </a>

            <a href='/upload' class='sidebar-item'>
                <i class='fas fa-upload'></i>
                <span>Uploader</span>
            </a>
            <a href='/shareDocuments' class='sidebar-item'>
                <i class="bi bi-folder-symlink"></i>
                <span>Documents Partagés</span>
            </a>
            <a href='/publicDocuments' class='sidebar-item'>
                <i class="bi bi-file-earmark-richtext me-2"></i>
                <span>Documents Publics</span>
            </a>

            <div class='sidebar-divider'></div>

            <?php if ($this->auth->isAdmin()): ?>
                <a href='/admin/users' class='sidebar-item'>
                    <i class='fas fa-users-cog'></i>
                    <span>Gestion Utilisateurs</span>
                </a>

                <a href='/admin/settings' class='sidebar-item'>
                    <i class='fas fa-cogs'></i>
                    <span>Paramètres</span>
                </a>

                <div class='sidebar-divider'></div>
            <?php endif ?>

            <a href='/profile' class='sidebar-item'>
                <i class='fas fa-user'></i>
                <span>Mon Profil</span>
            </a>

            <a href='/logout' class='sidebar-item'>
                <i class='fas fa-sign-out-alt'></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <!-- ===== HEADER ===== -->
    <div class='header'>
        <form action="" class="w-50 ">
            <div class='search-bar'>
                <input type='text' placeholder='🔍 Rechercher un document par title, catégorie...' name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            </div>
        </form>

        <div class='header-actions '>
            <button class='btn-icon' title='Notifications'>
                <i class='fas fa-bell'></i>
            </button>

            <button class='btn-icon d-none d-sm-block' title='Paramètres'>
                <i class='fas fa-cog'></i>
            </button>

            <div class='user-profile'>
                <div class='user-avatar'>
                    <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                </div>
                <div class='user-info d-none d-sm-block'>
                    <h5><?= $_SESSION['user_name'] ?></h5>
                    <p><?= ($this->auth->isAdmin() ? 'Administrateur' : 'Utilisateur') ?></p>
                </div>
                <i class='fas fa-chevron-down'></i>
            </div>
        </div>
    </div>
    <!-- ===== MAIN CONTENT ===== -->
    <div class='main-content'>
