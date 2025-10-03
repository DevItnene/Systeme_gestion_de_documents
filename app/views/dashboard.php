<?php
namespace App\Views;

use App\Core\Auth;

class Dashboard
{
    private $auth;
    private $user;
    public function __construct()
    {
        $this->auth = new Auth();
        $this->user = $this->auth->user();
    }

    public function Dashboard()
    {
        echo "
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
                        <i class='fas fa-folder'></i>
                        <span>Mes Documents</span>
                    </a>

                    <a href='/documents/upload' class='sidebar-item'>
                        <i class='fas fa-upload'></i>
                        <span>Uploader</span>
                    </a>

                    <div class='sidebar-divider'></div>

                    " . ($this->auth->isAdmin() ? "
                    <a href='/admin/users' class='sidebar-item'>
                        <i class='fas fa-users-cog'></i>
                        <span>Gestion Utilisateurs</span>
                    </a>

                    <a href='/admin/settings' class='sidebar-item'>
                        <i class='fas fa-cogs'></i>
                        <span>Paramètres</span>
                    </a>

                    <div class='sidebar-divider'></div>
                    " : "") . "

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
                <div class='search-bar'>
                    <input type='text' placeholder='🔍 Rechercher un document, un utilisateur...'>
                </div>

                <div class='header-actions'>
                    <button class='btn-icon' title='Notifications'>
                        <i class='fas fa-bell'></i>
                    </button>

                    <button class='btn-icon' title='Paramètres'>
                        <i class='fas fa-cog'></i>
                    </button>

                    <div class='user-profile'>
                        <div class='user-avatar'>
                            " . strtoupper(substr($this->user['name'], 0, 1)) . "
                        </div>
                        <div class='user-info'>
                            <h5>{$this->user['name']}</h5>
                            <p>" . ($this->auth->isAdmin() ? 'Administrateur' : 'Utilisateur') . "</p>
                        </div>
                        <i class='fas fa-chevron-down'></i>
                    </div>
                </div>
            </div>

            <!-- ===== MAIN CONTENT ===== -->
            <div class='main-content'>
                <!-- Section Bienvenue -->
                <div class='welcome-section'>
                    <h1>Bonjour, {$this->user['name']} ! 👋</h1>
                    <p class='mb-0'>Bienvenue sur votre tableau de bord DocManager</p>
                    <span class='" . ($this->auth->isAdmin() ? 'admin-badge' : 'user-badge') . "'>
                        " . ($this->auth->isAdmin() ? '🏆 ADMINISTRATEUR' : '👤 UTILISATEUR') . "
                    </span>
                </div>

                <!-- Statistiques -->
                <div class='stats-grid'>
                    <div class='stat-card'>
                        <div class='stat-icon'>
                            <i class='fas fa-file'></i>
                        </div>
                        <div class='stat-number'>24</div>
                        <div class='stat-title'>Documents Totaux</div>
                    </div>

                    <div class='stat-card'>
                        <div class='stat-icon'>
                            <i class='fas fa-folder'></i>
                        </div>
                        <div class='stat-number'>8</div>
                        <div class='stat-title'>Dossiers</div>
                    </div>

                    <div class='stat-card'>
                        <div class='stat-icon'>
                            <i class='fas fa-share'></i>
                        </div>
                        <div class='stat-number'>12</div>
                        <div class='stat-title'>Documents Partagés</div>
                    </div>

                    <div class='stat-card'>
                        <div class='stat-icon'>
                            <i class='fas fa-download'></i>
                        </div>
                        <div class='stat-number'>156</div>
                        <div class='stat-title'>Téléchargements</div>
                    </div>
                </div>

                <!-- Activité Récente -->
                <div class='recent-activity'>
                    <h4 class='mb-4'><i class='fas fa-history me-2'></i>Activité Récente</h4>

                    <div class='activity-item'>
                        <div class='activity-icon'>
                            <i class='fas fa-upload'></i>
                        </div>
                        <div>
                            <strong>Rapport_2024.pdf</strong> uploadé
                            <div class='text-muted small'>Il y a 2 heures</div>
                        </div>
                    </div>

                    <div class='activity-item'>
                        <div class='activity-icon'>
                            <i class='fas fa-share'></i>
                        </div>
                        <div>
                            <strong>Présentation.pptx</strong> partagé avec l'équipe
                            <div class='text-muted small'>Il y a 5 heures</div>
                        </div>
                    </div>

                    <div class='activity-item'>
                        <div class='activity-icon'>
                            <i class='fas fa-download'></i>
                        </div>
                        <div>
                            <strong>Contrat.docx</strong> téléchargé 3 fois
                            <div class='text-muted small'>Il y a 1 jour</div>
                        </div>
                    </div>

                    <div class='activity-item'>
                        <div class='activity-icon'>
                            <i class='fas fa-user-plus'></i>
                        </div>
                        <div>
                            Nouvel utilisateur ajouté
                            <div class='text-muted small'>Il y a 2 jours</div>
                        </div>
                    </div>
                </div>
            </div>";
    }
}
