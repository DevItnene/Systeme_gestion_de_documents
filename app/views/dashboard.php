<?php
namespace App\Views;

use App\Core\Auth;
use App\Models\Category;
use App\Models\Document;

class Dashboard
{
    private $auth;
    private $user;
    private $document;
    private $category;
    public function __construct()
    {
        $this->auth = new Auth();
        $this->user = $this->auth->user();
        $this->document = new Document();
        $this->category = new Category();
    }

    public function Dashboard()
    {
        $totalDocument = $this->document->getDocumentCounts();
        $totalDocumentShared = $this->document->getShareDocumentCounts(null, true);
        $totalCategories = $this->category->getCategoryCounts(null, true);
        $totalDownloadDocs = $this->document->getDocumentDownloadCounts();

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
                        <div class='stat-number'>{$totalDocument['Total']}</div>
                        <div class='stat-title'>" . (($totalDocument['Total'] <= 1) ? 'Document Total' : 'Documents Totaux') . "</div>
                    </div>

                    <div class='stat-card'>
                        <div class='stat-icon'>
                            <i class='bi bi-bookmarks-fill'></i>
                        </div>
                        <div class='stat-number'>{$totalCategories['Total']}</div>
                        <div class='stat-title'>" . (($totalCategories['Total'] <= 1) ? 'Catégorie' : 'Catégories') . "</div>
                    </div>

                    <div class='stat-card'>
                        <div class='stat-icon'>
                            <i class='fas fa-share'></i>
                        </div>
                        <div class='stat-number'>{$totalDocumentShared['Total']}</div>
                        <div class='stat-title'>" . (($totalDocumentShared['Total'] <= 1) ? 'Document Partagé' : 'Documents Partagés') . "</div>
                    </div>

                    <div class='stat-card'>
                        <div class='stat-icon'>
                            <i class='fas fa-download'></i>
                        </div>
                        <div class='stat-number'>". (($totalDownloadDocs['Total'] == \NULL) ? 0 : $totalDownloadDocs['Total']) ."</div>
                        <div class='stat-title'>" . (($totalDownloadDocs['Total'] <= 1) ? 'Téléchargement' : 'Téléchargements') . "</div>
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
