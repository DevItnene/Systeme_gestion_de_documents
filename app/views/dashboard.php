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
                <!-- Section Bienvenue -->
                <div class='welcome-section'>
                    <h1>Bonjour, {$this->user['name']} ! 👋</h1>
                    <p>Bienvenue sur votre tableau de bord DocManager</p>
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
