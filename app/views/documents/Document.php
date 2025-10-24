<?php
namespace App\Views\Documents;

use App\Models\User;
use App\Models\Category;
use App\Models\Document as DocumentModel;

class Document {

    private $docs;
    private $users;
    private $categories;
    public function __construct() {
        $this->docs = new DocumentModel();
        $this->users = new User();
        $this->categories = new Category();
    }

    public function displayDocument($id) {
        return $this->docs->getDocumentById(intval($id));
    }

    public function displayDocuments() {
        $search = isset($_GET['q']) ? htmlentities(trim($_GET['q'])) : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Pagination
        $limit = 10;
        $search ? $totalPages = $this->docs->getDocumentCounts($search)['Total']
        : $totalPages = $this->docs->getDocumentCounts()['Total'];
        $pages = ceil($totalPages / $limit);
        $offset = ($page - 1) * $limit;
        
        $search ? $documents = $this->docs->searchDocument($search, $limit, $offset)
                : $documents = $this->docs->getAllDocuments($limit, $offset);
        
        $this->documentTable($documents, $page, $pages, $search);
    }

    public function displayShareDocuments() {

        $search = isset($_GET['q']) ? htmlentities(trim($_GET['q'])) : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Pagination
        $limit = 10;
        $search ? $totalPages = $this->docs->getShareDocumentCounts($search)['Total']
        : $totalPages = $this->docs->getShareDocumentCounts()['Total'];
        $pages = ceil($totalPages / $limit);
        $offset = ($page - 1) * $limit;
        
        $search ? $documents = $this->docs->searchShareDocument($search, $limit, $offset)
                : $documents = $this->docs->getAllShareDocuments($limit, $offset);
        
        $table = "
            <div class='d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom'>
                <h1 class='h2'>{$totalPages} Documents Partagés</h1>
            </div>
            <!-- Message d'erreur -->
            <div id='dangerMsgShare' class='alert alert-danger alert-dismissible fade d-none' role='alert'>
                <i class='bi bi-exclamation-triangle-fill me-2'></i>
                <span class='dangerMessageShare'>Erreur ici</span>
                <button type='button' class='btn-close custom-close' aria-label='Fermer'></button>
            </div>
        ";
        if (empty($documents)) {
            $table .= "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>Titre</th>
                        <th scope='col'>Description</th>
                        <th scope='col'>Type</th>
                        <th scope='col'>Catégorie</th>
                        <th scope='col'>Partagé par</th>
                        <th scope='col'>Téléchargements</th>
                        <th scope='col'>Date de partage</th>
                    </tr>
                </thead>
                <tbody>
                    <td colspan='99' class='text-center'>Aucun document disponible !</td>
                </tbody>
            </table>";
        } else {
            
            $table .= " 
                <table class='table table-hover'>
                    <thead>
                        <tr>
                            <th scope='col'>Titre</th>
                            <th scope='col'>Description</th>
                            <th scope='col'>Type</th>
                            <th scope='col'>Catégorie</th>
                            <th scope='col'>Partagé par</th>
                            <th scope='col'>Téléchargements</th>
                            <th scope='col'>Date de partage</th>
                            <th scope='col'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    "; foreach ($documents as $document) {
                        
                            if (isset($document['file_type'])) {
                                $file_type = $document['file_type'] == 'application/pdf' ? 'pdf' : 
                                    (strpos($document['file_type'], 'docx') !== false ? 'word' :
                                    (strpos($document['file_type'], 'xlsx') !== false ? 'excel' :
                                    (strpos($document['file_type'], 'pptx') !== false ? 'powerpoint' : 'text')));
                            } else {
                                $file_type = 'N/A';
                            }

                            $table .= "
                                <tr>
                                    <td>{$document['title']}</td>
                                    <td>{$document['description']}</td>
                                    <td><i class='fas fa-file-{$file_type} text-danger'></i></td>
                                    <td>{$document['category_name']}</td>
                                    <td>{$document['username']}</td>
                                    <td>{$document['download_count']}</td>
                                    <td>{$document['created_at']}</td>
                                    <td class='links-action'>
                                ";
                                        if ($document['permission'] == "download") {
                                            $table .= "
                                                <a href='#' class='download-btn' id='download_link' 
                                                    data-id='{$document['document_id']}'>
                                                    <i class='fas fa-download'></i>
                                                </a>
                                            ";
                                        } else if ($document["permission"] == "view") {
                                            $table .= "
                                                <a href='#' class='view-btn' data-bs-toggle='modal' data-bs-target='#viewModal' 
                                                    data-id='{$document['document_id']}'
                                                    data-title='{$document['title']}'
                                                    data-description='{$document['description']}'
                                                    data-category='{$document['category_name']}'
                                                    data-file-name='{$document['file_name']}'
                                                    data-file-type='{$document['file_type']}'
                                                    data-file-size='{$document['file_size']}'
                                                    data-shared-by='{$document['username']}'
                                                    data-download-count='{$document['download_count']}'
                                                    data-created-at='{$document['created_at']}'>
                                                    <i class='fa-solid fa-eye'></i>
                                                </a>
                                            ";
                                        } else if ($document["permission"] == "edit") {
                                            $table .= "
                                                <a href='#' class='edit-btn' data-bs-toggle='modal' data-bs-target='#editModal' 
                                                    data-id='{$document['document_id']}'
                                                    data-title='{$document['title']}'
                                                    data-description='{$document['description']}'
                                                    data-category-id='{$document['category_id']}'>
                                                    <i class='fa-solid fa-pen-to-square'></i>
                                                </a>
                                            ";
                                        }
                                        $table .= "
                                        <a href='#' class='delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' 
                                            data-id='{$document['id']}'
                                            data-title='{$document['title']}'>
                                            <i class='fa-solid fa-trash' style='color: lightcoral;'></i>
                                        </a>
                                    </td>
                                </tr>
                            ";
                        }
                $table .= "
                    </tbody>
                </table>
                <div class='d-flex justify-content-center align-items-center gap-3 mt-4'>";

                // Le lien Precedent
                $p_prev = ($page > 1) ? '?page=' . ($page - 1) : '#';
                $p_prev .= $search ? '&q=' . urlencode($search) : '';
                $style_prev = ($page <= 1) ? 'disabled opacity-70' : '';
                $bool_prev = ($page <= 1) ? 'true' : 'false';
                
                $table .= "
                    <a href='{$p_prev}' id='prevPage'
                        class='btn btn-primary shadow-sm px-3 py-1 rounded-pill fw-semibold {$style_prev}'
                        aria-disabled='{$bool_prev}'>
                            ⬅️ Précédent
                    </a>
                    <span class='fw-semibold text-muted'>
                        Page {$page} / {$pages}
                    </span>
                ";

                // Le lien Suivant
                $p_next = ($page < $pages) ? '?page=' . ($page + 1) : '#';
                $p_next .= $search ? '&q=' . urlencode($search) : '';
                $style_next = ($page >= $pages) ? 'disabled opacity-70' : '';
                $bool_next = ($page >= $pages) ? 'true' : 'false';
                
                $table .= "
                    <a href='{$p_next}' id='nextPage'
                    class='btn btn-primary shadow-sm px-3 py-1 rounded-pill fw-semibold {$style_next}'
                    aria-disabled='{$bool_next}'>
                        Suivant ➡️
                    </a>
                </div>";
        }

        echo $table;

        $categories = $this->categories->getAllCategories();

        $editModal = "
            <div class='modal fade' id='editModal' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='editModalLabel'>Modal title</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='alert alert-danger alert-dismissible fade show m-3 mb-0' id='dangerMsgBox' role='alert'>
                            <i class='fas fa-exclamation-circle me-2'></i>
                            <span class='dangerMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <div class='alert alert-success alert-dismissible fade show m-3 mb-0' id='SuccessMsgBox' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>
                            <span class='successMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <form id='editDocumentForm' novalidate>
                            <div class='modal-body modal-action p-5 pt-4'>
                                <input type='hidden' class='form-control' id='doc_id' name='doc_id'>
                                <div class='mb-3 row'>
                                    <label for='title' class='col-form-label'>Titre du document</label>
                                    <input type='text' class='form-control' id='title' name='title' required>
                                    <div class='invalid-feedback'>Veuillez saisir le titre du document.</div>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='description' class='col-form-label'>Description</label>
                                    <input type='text' class='form-control' id='description' name='description'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='category_id' class='col-form-label'>Catégorie</label>
                                    <select class='form-select' id='category_id' name='category_id' required>
                                    ";
                                        foreach ($categories as $category) {
                                            $editModal .=" <option value='{$category['id']}'> {$category['name']} </option>";
                                        }
                                $editModal .="
                                    </select>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fermer</button>
                                    <button type='submit' class='btn btn-primary' id='save-btn'>Enregistrer</button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        ";

        echo $editModal;

        $deleteModal = "
                <div class='modal fade' id='deleteModal' tabindex='-1' aria-labelledby='deleteModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='deleteModalLabel'>Confirmer la suppression</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <p>Êtes-vous sûr de vouloir supprimer le document :</p>
                                <p><strong id='delete_document_title'></strong></p>
                                <p class='text-danger'><small>Cette action est irréversible.</small></p>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Annuler</button>
                                <form id='deleteForm' method='POST' action='/documents/delete' style='display: inline;'>
                                    <input type='hidden' id='delete_document_share_id' name='delete_document_share_id'>
                                    <button type='submit' class='btn btn-danger'>Supprimer définitivement</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ";

        echo $deleteModal;

        $viewModal = "
                <!-- ===== MODAL DE VISUALISATION ===== -->
                <div class='modal fade' id='viewModal' tabindex='-1' aria-labelledby='viewModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-lg'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='viewModalLabel'>Détails du Document</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <div class='row'>
                                    <div class='col-md-6'>
                                        <h4 class='text-center'>Informations générales</h4>
                                        <p><strong>Titre :</strong> <span id='view_title'></span></p>
                                        <p><strong>Description :</strong> <span id='view_description'></span></p>
                                        <p><strong>Catégorie :</strong> <span id='view_category'></span></p>
                                        <p><strong>Partager par :</strong> <span id='view_status'></span></p>
                                    </div>
                                    <div class='col-md-6'>
                                        <h4 class='text-center'>Informations techniques</h4>
                                        <p><strong>Nom du fichier :</strong> <span id='view_filename'></span></p>
                                        <p><strong>Type :</strong> <span id='view_type'></span></p>
                                        <p><strong>Taille :</strong> <span id='view_size'></span></p>
                                        <p><strong>Date de partage :</strong> <span id='view_date'></span></p>
                                        <p><strong>Téléchargements :</strong> <span id='view_downloads'></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        
        echo $viewModal;
    }

    public function displayPublicDocuments() {

        $search = isset($_GET['q']) ? htmlentities(trim($_GET['q'])) : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Pagination
        $limit = 10;
        $search ? $totalPages = $this->docs->getPublicDocumentCounts($search)['Total']
        : $totalPages = $this->docs->getPublicDocumentCounts()['Total'];
        $pages = ceil($totalPages / $limit);
        $offset = ($page - 1) * $limit;
        
        $search ? $documents = $this->docs->searchPublicDocument($search, $limit, $offset)
                : $documents = $this->docs->getAllPublicsDocuments($limit, $offset);
        
        $table = "
            <div class='d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom'>
                <h1 class='h2'>{$totalPages} Documents Publics</h1>
            </div>
        ";
        if (empty($documents)) {
            $table .= "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>Titre</th>
                        <th scope='col'>Description</th>
                        <th scope='col'>Type</th>
                        <th scope='col'>Catégorie</th>
                        <th scope='col'>Partagé par</th>
                        <th scope='col'>Téléchargements</th>
                        <th scope='col'>Date de création</th>
                    </tr>
                </thead>
                <tbody>
                    <td colspan='99' class='text-center'>Aucun document disponible !</td>
                </tbody>
            </table>";
        } else {
            
            $table .= " 
                <table class='table table-hover'>
                    <thead>
                        <tr>
                            <th scope='col'>Titre</th>
                            <th scope='col'>Description</th>
                            <th scope='col'>Type</th>
                            <th scope='col'>Catégorie</th>
                            <th scope='col'>Auteur</th>
                            <th scope='col'>Téléchargements</th>
                            <th scope='col'>Date de création</th>
                            <th scope='col'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    "; foreach ($documents as $document) {
                        
                            if (isset($document['file_type'])) {
                                $file_type = $document['file_type'] == 'application/pdf' ? 'pdf' : 
                                    (strpos($document['file_type'], 'docx') !== false ? 'word' :
                                    (strpos($document['file_type'], 'xlsx') !== false ? 'excel' :
                                    (strpos($document['file_type'], 'pptx') !== false ? 'powerpoint' : 'text')));
                            } else {
                                $file_type = 'N/A';
                            }

                            $table .= "
                                <tr>
                                    <td>{$document['title']}</td>
                                    <td>{$document['description']}</td>
                                    <td><i class='fas fa-file-{$file_type} text-danger'></i></td>
                                    <td>{$document['category_name']}</td>
                                    <td>{$document['username']}</td>
                                    <td>{$document['download_count']}</td>
                                    <td>{$document['created_at']}</td>
                                    <td class='links-action'>
                                        <a href='#' class='download-btn' id='download_link' 
                                            data-id='{$document['id']}'>
                                            <i class='fas fa-download'></i>
                                        </a>
                                        <a href='#' class='view-btn' data-bs-toggle='modal' data-bs-target='#viewModal' 
                                            data-id='{$document['id']}'
                                            data-title='{$document['title']}'
                                            data-description='{$document['description']}'
                                            data-category='{$document['category_name']}'
                                            data-file-name='{$document['file_name']}'
                                            data-file-type='{$document['file_type']}'
                                            data-file-size='{$document['file_size']}'
                                            data-shared-by='{$document['username']}'
                                            data-download-count='{$document['download_count']}'
                                            data-created-at='{$document['created_at']}'>
                                            <i class='fa-solid fa-eye'></i>
                                        </a>
                                        <a href='#' class='edit-btn' data-bs-toggle='modal' data-bs-target='#editModal' 
                                            data-id='{$document['id']}'
                                            data-title='{$document['title']}'
                                            data-description='{$document['description']}'
                                            data-category-id='{$document['category_id']}'>
                                            <i class='fa-solid fa-pen-to-square'></i>
                                        </a>
                                        ";
                                        if ($_SESSION["user_role"] == "admin") {
                                            $table .= "
                                                <a href='#' class='delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' 
                                                    data-id='{$document['id']}'
                                                    data-title='{$document['title']}'>
                                                    <i class='fa-solid fa-trash' style='color: lightcoral;'></i>
                                                </a>
                                            ";
                                        }
                                        
                                        "
                                    </td>
                                </tr>
                            ";
                        }
                $table .= "
                    </tbody>
                </table>
                <div class='d-flex justify-content-center align-items-center gap-3 mt-4'>";

                // Le lien Precedent
                $p_prev = ($page > 1) ? '?page=' . ($page - 1) : '#';
                $p_prev .= $search ? '&q=' . urlencode($search) : '';
                $style_prev = ($page <= 1) ? 'disabled opacity-70' : '';
                $bool_prev = ($page <= 1) ? 'true' : 'false';
                
                $table .= "
                    <a href='{$p_prev}' id='prevPage'
                        class='btn btn-primary shadow-sm px-3 py-1 rounded-pill fw-semibold {$style_prev}'
                        aria-disabled='{$bool_prev}'>
                            ⬅️ Précédent
                    </a>
                    <span class='fw-semibold text-muted'>
                        Page {$page} / {$pages}
                    </span>
                ";

                // Le lien Suivant
                $p_next = ($page < $pages) ? '?page=' . ($page + 1) : '#';
                $p_next .= $search ? '&q=' . urlencode($search) : '';
                $style_next = ($page >= $pages) ? 'disabled opacity-70' : '';
                $bool_next = ($page >= $pages) ? 'true' : 'false';
                
                $table .= "
                    <a href='{$p_next}' id='nextPage'
                    class='btn btn-primary shadow-sm px-3 py-1 rounded-pill fw-semibold {$style_next}'
                    aria-disabled='{$bool_next}'>
                        Suivant ➡️
                    </a>
                </div>";
        }

        echo $table;

        $categories = $this->categories->getAllCategories();

        $editModal = "
            <div class='modal fade' id='editModal' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='editModalLabel'>Modal title</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='alert alert-danger alert-dismissible fade show m-3 mb-0' id='dangerMsgBox' role='alert'>
                            <i class='fas fa-exclamation-circle me-2'></i>
                            <span class='dangerMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <div class='alert alert-success alert-dismissible fade show m-3 mb-0' id='SuccessMsgBox' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>
                            <span class='successMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <form id='editDocumentForm' novalidate>
                            <div class='modal-body modal-action p-5 pt-4'>
                                <input type='hidden' class='form-control' id='doc_id' name='doc_id'>
                                <div class='mb-3 row'>
                                    <label for='title' class='col-form-label'>Titre du document</label>
                                    <input type='text' class='form-control' id='title' name='title' required>
                                    <div class='invalid-feedback'>Veuillez saisir le titre du document.</div>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='description' class='col-form-label'>Description</label>
                                    <input type='text' class='form-control' id='description' name='description'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='category_id' class='col-form-label'>Catégorie</label>
                                    <select class='form-select' id='category_id' name='category_id' required>
                                    ";
                                        foreach ($categories as $category) {
                                            $editModal .=" <option value='{$category['id']}'> {$category['name']} </option>";
                                        }
                                $editModal .="
                                    </select>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fermer</button>
                                    <button type='submit' class='btn btn-primary' id='submitBtn'>
                                        Enregistrer
                                        <span id='loader' class='spinner-border spinner-border-sm ms-2' style='display: none;' role='status' aria-hidden='true'></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        ";

        echo $editModal;

        $deleteModal = "
                <div class='modal fade' id='deleteModal' tabindex='-1' aria-labelledby='deleteModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='deleteModalLabel'>Confirmer la suppression</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <p>Êtes-vous sûr de vouloir supprimer le document :</p>
                                <p><strong id='delete_document_title'></strong></p>
                                <p class='text-danger'><small>Cette action est irréversible.</small></p>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Annuler</button>
                                <form id='deleteForm' method='POST' action='/documents/delete' style='display: inline;'>
                                    <input type='hidden' id='delete_document_id' name='delete_document_id'>
                                    <button type='submit' class='btn btn-danger'>Supprimer définitivement</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ";

        echo $deleteModal;

        $viewModal = "
                <!-- ===== MODAL DE VISUALISATION ===== -->
                <div class='modal fade' id='viewModal' tabindex='-1' aria-labelledby='viewModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-lg'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='viewModalLabel'>Détails du Document</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <div class='row'>
                                    <div class='col-md-6'>
                                        <h4 class='text-center'>Informations générales</h4>
                                        <p><strong>Titre :</strong> <span id='view_title'></span></p>
                                        <p><strong>Description :</strong> <span id='view_description'></span></p>
                                        <p><strong>Catégorie :</strong> <span id='view_category'></span></p>
                                        <p><strong>Partager par :</strong> <span id='view_status'></span></p>
                                    </div>
                                    <div class='col-md-6'>
                                        <h4 class='text-center'>Informations techniques</h4>
                                        <p><strong>Nom du fichier :</strong> <span id='view_filename'></span></p>
                                        <p><strong>Type :</strong> <span id='view_type'></span></p>
                                        <p><strong>Taille :</strong> <span id='view_size'></span></p>
                                        <p><strong>Date de partage :</strong> <span id='view_date'></span></p>
                                        <p><strong>Téléchargements :</strong> <span id='view_downloads'></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fermer</button>
                                <a href='#' id='view_download_link' class='btn btn-primary'>
                                    <i class='fas fa-download me-2'></i>Télécharger
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        
        echo $viewModal;
    }

    public function documentTable ($documents, $page, $pages, $search) {
        $table = "
            <div class='d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom'>
                <h1 class='h2'>Mes Documents</h1>
                <a href='/upload'>
                    <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#uploadModal'>
                        <i class='fas fa-upload me-2'></i>Uploader un document
                    </button>
                </a>
            </div>
        ";
        if (empty($documents)) {
            $table .= "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>#</th>
                        <th scope='col'>Titre</th>
                        <th scope='col'>Description</th>
                        <th scope='col'>Type</th>
                        <th scope='col'>Catégorie id</th>
                        <th scope='col'>Public</th>
                        <th scope='col'>Téléchargements</th>
                        <th scope='col'>Date de création</th>
                    </tr>
                </thead>
                <tbody>
                    <td colspan='99' class='text-center'>Aucun document disponible !</td>
                </tbody>
            </table>";
        } else {
            ($_SESSION["user_role"] == "admin") ? $th_user_id = "<th scope='col'>Utilisateur</th>" : $th_user_id = '';
            
            $table .= " 
                <table class='table table-hover'>
                    <thead>
                        <tr>
                            <th scope='col'>#</th>
                            <th scope='col'>Titre</th>
                            <th scope='col'>Description</th>
                            <th scope='col'>Type</th>
                            <th scope='col'>Catégorie</th>
                            {$th_user_id}
                            <th scope='col'>Public</th>
                            <th scope='col'>Téléchargements</th>
                            <th scope='col'>Date de création</th>
                            <th scope='col'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    "; foreach ($documents as $document) {
                            $user_name = $this->users->findById($document['user_id'])['name'];
                            ($_SESSION["user_role"] == "admin") ? $td_user_id = "<td>{$user_name}</td>" : $td_user_id = '';
                            if (isset($document['file_type'])) {
                                $file_type = $document['file_type'] == 'application/pdf' ? 'pdf' : 
                                    (strpos($document['file_type'], 'docx') !== false ? 'word' :
                                    (strpos($document['file_type'], 'xlsx') !== false ? 'excel' :
                                    (strpos($document['file_type'], 'pptx') !== false ? 'powerpoint' : 'text')));
                            } else {
                                $file_type = 'N/A';
                            }
                            
                            $is_public = ($document['is_public']==1) ? 'Oui' : 'Non';

                            $table .= "
                                <tr>
                                    <th scope='row'>{$document['id']}</th>
                                    <td>{$document['title']}</td>
                                    <td>{$document['description']}</td>
                                    <td><i class='fas fa-file-{$file_type} text-danger'></i></td>
                                    <td>{$document['category_name']}</td>
                                    {$td_user_id}
                                    <td>{$is_public}</td>
                                    <td>{$document['download_count']}</td>
                                    <td>{$document['created_at']}</td>
                                    <td class='links-action'>
                                        <a href='#' class='share-btn' data-bs-toggle='modal' data-bs-target='#shareDocumentModal' 
                                            data-id='{$document['id']}'
                                            data-title='{$document['title']}'>
                                            <i class='bi bi-share-fill'></i>
                                        </a>
                                        <a href='#' class='view-btn' data-bs-toggle='modal' data-bs-target='#viewModal' 
                                            data-id='{$document['id']}'
                                            data-title='{$document['title']}'
                                            data-description='{$document['description']}'
                                            data-category='{$document['category_name']}'
                                            data-is-public='{$document['is_public']}'
                                            data-file-name='{$document['file_name']}'
                                            data-file-type='{$document['file_type']}'
                                            data-file-size='{$document['file_size']}'
                                            data-download-count='{$document['download_count']}'
                                            data-created-at='{$document['created_at']}'>
                                            <i class='fa-solid fa-eye'></i>
                                        </a>
                                        <a href='#' class='edit-btn' data-bs-toggle='modal' data-bs-target='#editModal' 
                                            data-id='{$document['id']}'
                                            data-title='{$document['title']}'
                                            data-description='{$document['description']}'
                                            data-category-id='{$document['category_id']}'
                                            data-is-public='{$document['is_public']}'>
                                            <i class='fa-solid fa-pen-to-square'></i>
                                        </a>
                                        <a href='#' class='delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' 
                                            data-id='{$document['id']}'
                                            data-title='{$document['title']}'>
                                            <i class='fa-solid fa-trash' style='color: lightcoral;'></i>
                                        </a>
                                    </td>
                                </tr>
                            ";
                        }
                $table .= "
                    </tbody>
                </table>
                <div class='d-flex justify-content-center align-items-center gap-3 mt-4'>";

                // Le lien Precedent
                $p_prev = ($page > 1) ? '?page=' . ($page - 1) : '#';
                $p_prev .= $search ? '&q=' . urlencode($search) : '';
                $style_prev = ($page <= 1) ? 'disabled opacity-70' : '';
                $bool_prev = ($page <= 1) ? 'true' : 'false';
                
                $table .= "
                    <a href='{$p_prev}' id='prevPage'
                        class='btn btn-primary shadow-sm px-3 py-1 rounded-pill fw-semibold {$style_prev}'
                        aria-disabled='{$bool_prev}'>
                            ⬅️ Précédent
                    </a>
                    <span class='fw-semibold text-muted'>
                        Page {$page} / {$pages}
                    </span>
                ";

                // Le lien Suivant
                $p_next = ($page < $pages) ? '?page=' . ($page + 1) : '#';
                $p_next .= $search ? '&q=' . urlencode($search) : '';
                $style_next = ($page >= $pages) ? 'disabled opacity-70' : '';
                $bool_next = ($page >= $pages) ? 'true' : 'false';
                
                $table .= "
                    <a href='{$p_next}' id='nextPage'
                    class='btn btn-primary shadow-sm px-3 py-1 rounded-pill fw-semibold {$style_next}'
                    aria-disabled='{$bool_next}'>
                        Suivant ➡️
                    </a>
                </div>";
        }

        echo $table;

        $categories = $this->categories->getAllCategories();

        $editModal = "
            <div class='modal fade' id='editModal' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='editModalLabel'>Modal title</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='alert alert-danger alert-dismissible fade show m-3 mb-0' id='dangerMsgBox' role='alert'>
                            <i class='fas fa-exclamation-circle me-2'></i>
                            <span class='dangerMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <div class='alert alert-success alert-dismissible fade show m-3 mb-0' id='SuccessMsgBox' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>
                            <span class='successMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <form id='editDocumentForm' method='POST' action='/documents/update' novalidate>
                            <div class='modal-body modal-action p-5 pt-4'>
                                <input type='hidden' class='form-control' id='doc_id' name='doc_id'>
                                <div class='mb-3 row'>
                                    <label for='title' class='col-form-label'>Titre du document</label>
                                    <input type='text' class='form-control' id='title' name='title' required>
                                    <div class='invalid-feedback'>Veuillez saisir le titre du document.</div>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='description' class='col-form-label'>Description</label>
                                    <input type='text' class='form-control' id='description' name='description'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='category_id' class='col-form-label'>Catégorie</label>
                                    <select class='form-select' id='category_id' name='category_id' required>
                                    ";
                                        foreach ($categories as $category) {
                                            $editModal .=" <option value='{$category['id']}'> {$category['name']} </option>";
                                        }
                                $editModal .="
                                    </select>
                                    <div class='invalid-feedback' id='invalidType'>Veuillez choisir une catégorie</div>
                                </div>                                
                                <div class='mb-3 row'>
                                    <label for='document_file' class='col-form-label'>Changer le document</label>
                                    <input type='file' id='document_file' name='document_file' class='form-control'
                                        accept='.pdf,.txt,.pptx,.xlsx,.docx,.csv,.json'>
                                    <small style='opacity: 0.7'>Les formats acceptés : pdf, text, pptx, xlsx, docx, csv, json</small>
                                    <div class='invalid-feedback' id='invalidType'></div>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='is_public' class='col-form-label'>Public</label>
                                    <select class='form-select' id='is_public' name='is_public'>
                                        <option value='1'>Oui</option>
                                        <option value='0'>Non</option>
                                    </select>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fermer</button>
                                    <button type='submit' class='btn btn-primary' id='save-btn'>Enregistrer</button>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        ";

        echo $editModal;

        $deleteModal = "
                <div class='modal fade' id='deleteModal' tabindex='-1' aria-labelledby='deleteModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='deleteModalLabel'>Confirmer la suppression</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <p>Êtes-vous sûr de vouloir supprimer le document :</p>
                                <p><strong id='delete_document_title'></strong></p>
                                <p class='text-danger'><small>Cette action est irréversible.</small></p>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Annuler</button>
                                <form id='deleteForm' method='POST' action='/documents/delete' style='display: inline;'>
                                    <input type='hidden' id='delete_document_id' name='delete_document_id'>
                                    <button type='submit' class='btn btn-danger'>Supprimer définitivement</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ";

        echo $deleteModal;

        $viewModal = "
                <!-- ===== MODAL DE VISUALISATION ===== -->
                <div class='modal fade' id='viewModal' tabindex='-1' aria-labelledby='viewModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-lg'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='viewModalLabel'>Détails du Document</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <div class='row'>
                                    <div class='col-md-6'>
                                        <h4 class='text-center'>Informations générales</h4>
                                        <p><strong>Titre :</strong> <span id='view_title'></span></p>
                                        <p><strong>Description :</strong> <span id='view_description'></span></p>
                                        <p><strong>Catégorie :</strong> <span id='view_category'></span></p>
                                        <p><strong>Statut :</strong> <span id='view_status' class='badge'></span></p>
                                    </div>
                                    <div class='col-md-6'>
                                        <h4 class='text-center'>Informations techniques</h4>
                                        <p><strong>Nom du fichier :</strong> <span id='view_filename'></span></p>
                                        <p><strong>Type :</strong> <span id='view_type'></span></p>
                                        <p><strong>Taille :</strong> <span id='view_size'></span></p>
                                        <p><strong>Date d'upload :</strong> <span id='view_date'></span></p>
                                        <p><strong>Téléchargements :</strong> <span id='view_downloads'></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fermer</button>
                                <a href='#' id='view_download_link' class='btn btn-primary'>
                                    <i class='fas fa-download me-2'></i>Télécharger
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        
        echo $viewModal;

        $users = $this->users->getAllUsers();

        $shareModal = "
                    <!-- ===== MODAL DE PARTAGE DE DOCUMENT ===== -->
                    <div class='modal fade' id='shareDocumentModal' tabindex='-1' aria-labelledby='shareDocumentModalLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='shareDocumentModalLabel'>Partager le document</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Fermer'></button>
                                </div>

                                <div class='modal-body'>
                                    <div class='alert alert-danger alert-dismissible fade show mb-4' id='dangerMsgShareBox' role='alert'>
                                        <i class='fas fa-exclamation-circle me-2'></i>
                                        <span class='dangerMessageShare'></span>
                                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                    </div>
                                    <div class='alert alert-success alert-dismissible fade show mb-4' id='SuccessMsgBoxShare' role='alert'>
                                        <i class='fas fa-check-circle me-2'></i>
                                        <span class='successMessageShare'></span>
                                        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                    </div>

                                    <form id='shareDocumentForm' novalidate>
                                        <input type='hidden' id='document_share_id' name='document_share_id'>

                                        <!-- Title -->
                                        <div class='mb-3'>
                                            <label for='shareDocumentTitle' class='col-form-label fw-semibold'>Titre du document</label>
                                            <input type='text' class='form-control' id='shareDocumentTitle' name='shareDocumentTitle'>
                                        </div>

                                        <!-- Select utilisateur -->
                                        <div class='mb-3'>
                                            <label for='shared_with_user_id' class='form-label fw-semibold'>Partager avec <span class='text-danger'>*</span></label>
                                            <select class='form-select' id='shared_with_user_id' name='shared_with_user_id[]' multiple required>
                                                <option value='' disabled>-- Sélectionnez un ou plusieurs utilisateurs --</option>
                                                <option value='everyone'>Toute le monde</option>
                                                ";
                                                foreach ($users as $user) {
                                                    if ($user["id"] != $_SESSION['user_id']) {
                                                        $shareModal .=" <option value='{$user['id']}'> {$user['name']} </option>";
                                                    }
                                                }
                                            $shareModal .="
                                            </select>
                                            <small class='text-muted'>Maintenez <strong>Ctrl</strong> (ou <strong>Cmd</strong> sur Mac) pour sélectionner plusieurs utilisateurs.</small>
                                            <div class='invalid-feedback'>Veuillez sélectionner un ou plusieurs utilisateurs.</div>
                                        </div>

                                        <!-- Select permission -->
                                        <div class='mb-3'>
                                            <label for='permission' class='form-label fw-semibold'>Permission <span class='text-danger'>*</span></label>
                                            <select class='form-select' id='permission' name='permission' required>
                                                <option value='' disabled selected>-- Choisissez un type de permission --</option>
                                                <option value='edit'>Éditer</option>
                                                <option value='download'>Télécharger</option>
                                                <option value='view'>Visualiser</option>
                                            </select>
                                            <div class='invalid-feedback'>Veuillez sélectionner une option.</div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Footer -->
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Annuler</button>
                                    <button type='submit' class='btn btn-primary' form='shareDocumentForm'><i class='bi bi-send-fill me-1'></i>Partager</button>
                                </div>
                            </div>
                        </div>
                    </div>
            ";
        echo $shareModal;
    }
}
?>