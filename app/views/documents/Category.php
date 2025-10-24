<?php
namespace App\Views\Documents;

use App\Models\Category as ModelsCategory;

class Category {
    private $categories;
    public function __construct() {
        $this->categories = new ModelsCategory();
    }

    public function displayCategories() {
        $search = isset($_GET['q']) ? htmlentities(trim($_GET['q'])) : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        
        // Pagination
        $limit = 10;
        $search ? $totalPages = $this->categories->getCategoryCounts($search)['Total']
        : $totalPages = $this->categories->getCategoryCounts()['Total'];
        $pages = ceil($totalPages / $limit);
        $offset = ($page - 1) * $limit;
        
        $search ? $categories = $this->categories->searchCategories($search, $limit, $offset)
                : $categories = $this->categories->getAllCategories($limit, $offset);
        
        $this->categoriesTable($totalPages, $categories, $page, $pages, $search);
    }

    public function categoriesTable ($totalPages, $categories, $page, $pages, $search) {
        $table = "
            <div class='d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom'>
                <h1 class='h2'>{$totalPages} Catégories</h1>
                <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#addModal'>
                    <i class='bi bi-plus-circle'></i> Ajouter une catégorie
                </button>
            </div>
        ";
        if (empty($categories)) {
            $table .= "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>#</th>
                        <th scope='col'>Nom</th>
                        <th scope='col'>Description</th>
                        <th scope='col'>Créer Par</th>
                        <th scope='col'>mise à jour</th>
                        <th scope='col'>Date de création</th>
                        <th scope='col'>Actions</th>
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
                            <th scope='col'>#</th>
                            <th scope='col'>Nom</th>
                            <th scope='col'>Description</th>
                            <th scope='col'>Créer Par</th>
                            <th scope='col'>mise à jour</th>
                            <th scope='col'>Date de création</th>
                            <th scope='col'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    "; foreach ($categories as $category) {
                            // $user_name = $this->users->findById($category['user_id'])['name'];

                            $table .= "
                                <tr>
                                    <th scope='row'>{$category['id']}</th>
                                    <td>{$category['name']}</td>
                                    <td>{$category['description']}</td>
                                    <td>{$category['user_name']}</td>
                                    <td>{$category['updated_at']}</td>
                                    <td>{$category['created_at']}</td>
                                    <td class='links-action'>
                                        <a href='#' class='edit-btn' data-bs-toggle='modal' data-bs-target='#editModal' 
                                            data-id='{$category['id']}'
                                            data-name='{$category['name']}'
                                            data-description='{$category['description']}'>
                                            <i class='fa-solid fa-pen-to-square'></i>
                                        </a>
                                        <a href='#' class='delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' 
                                            data-id='{$category['id']}'
                                            data-name='{$category['name']}'>
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
                        <form id='editDocumentForm' method='POST' action='/category/update' novalidate>
                            <div class='modal-body modal-action p-5 pt-4'>
                                <input type='hidden' class='form-control' id='category_id' name='category_id'>
                                <div class='mb-3 row'>
                                    <label for='category_name' class='col-form-label'>Nom de Catégorie</label>
                                    <input type='text' class='form-control' id='category_name' name='category_name' required>
                                    <div class='invalid-feedback'>Veuillez saisir le nom catégorie.</div>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='description' class='col-form-label'>Description</label>
                                    <input type='text' class='form-control' id='description' name='description'>
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

        $addModal = "
            <div class='modal fade' id='addModal' tabindex='-1' aria-labelledby='addModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='addModalLabel'>Modal title</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='alert alert-danger alert-dismissible fade show m-3 mb-0' id='dangerCatMsg' role='alert'>
                            <i class='fas fa-exclamation-circle me-2'></i>
                            <span class='dangerMsg'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <div class='alert alert-success alert-dismissible fade show m-3 mb-0' id='SuccessCatMsg' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>
                            <span class='successMsg'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <form id='addCategoryForm' method='POST' action='/category/add' novalidate>
                            <div class='modal-body modal-action p-5 pt-4'>
                                <input type='hidden' class='form-control' id='category_id' name='category_id'>
                                <div class='mb-3 row'>
                                    <label for='category_name' class='col-form-label'>Nom de Catégorie</label>
                                    <input type='text' class='form-control' id='category_name' name='category_name' required>
                                    <div class='invalid-feedback'>Veuillez saisir le nom de la catégorie.</div>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='description' class='col-form-label'>Description</label>
                                    <input type='text' class='form-control' id='description' name='description'>
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

        echo $addModal;

        $deleteModal = "
                <div class='modal fade' id='deleteModal' tabindex='-1' aria-labelledby='deleteModalLabel' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='deleteModalLabel'>Confirmer la suppression</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <p>Êtes-vous sûr de vouloir supprimer la catégorie :</p>
                                <p><strong id='delete_category_title'></strong></p>
                                <p class='text-danger'><small>Cette action est irréversible.</small></p>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Annuler</button>
                                <form id='deleteForm' method='POST' action='/category/delete' style='display: inline;'>
                                    <input type='hidden' id='delete_category_id' name='delete_category_id'>
                                    <button type='submit' class='btn btn-danger'>Supprimer définitivement</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            ";

        echo $deleteModal;
    }
}
?>