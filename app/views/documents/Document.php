<?php
namespace App\Views\Documents;

use App\Models\Document as DocumentModel;

class Document {

    private $docs;

    public function __construct() {
        $this->docs = new DocumentModel();
    }

    public function displayDocument($id) {
        $document = $this->docs->getDocumentById(intval($id));
    }

    public function displayDocuments() {
        $documents = $this->docs->getAllDocuments();
        $table = "
            <div class='d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom'>
                <h1 class='h2'>Mes Documents</h1>
                <button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#uploadModal'>
                    <i class='fas fa-upload me-2'></i>Uploader un document
                </button>
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
            ($_SESSION["user_role"] == "admin") ? $th_user_id = "<th scope='col'>Utilisateur id</th>" : $th_user_id = '';
            
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
                            <th scope='col'>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    "; foreach ($documents as $document) {
                            ($_SESSION["user_role"] == "admin") ? $td_user_id = "<td>{$document['user_id']}</td>" : $td_user_id = '';
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
            ";
        }

        echo $table;

        $categories = $this->docs->getAllCategories();

        $editModal = "
            <div class='modal fade' id='editModal' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='editModalLabel'>Modal title</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='alert alert-success alert-dismissible fade show m-3 mb-0' id='SuccessMsgBox' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>
                            <span class='successMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <form id='editDocumentForm' method='POST' action='/documents/update'>
                            <div class='modal-body modal-action p-5 pt-4'>
                                <input type='hidden' class='form-control' id='doc_id' name='doc_id'>
                                <div class='mb-3 row'>
                                    <label for='title' class='col-form-label'>Titre du document</label>
                                    <input type='text' class='form-control' id='title' name='title'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='description' class='col-form-label'>Description</label>
                                    <input type='text' class='form-control' id='description' name='description'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='category_id' class='col-form-label'>Catégorie</label>
                                    <select class='form-select' id='category_id' name='category_id'>
                                    ";
                                        foreach ($categories as $category) {
                                            $editModal .=" <option value='{$category['id']}'> {$category['name']} </option>";
                                        }
                                $editModal .="
                                    </select>
                                </div>                                
                                <div class='mb-3 row'>
                                    <label for='document_file' class='col-form-label'>Changer le document</label>
                                    <input type='file' class='form-control' id='document_file' name='document_file' accept='application/*'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='is_public' class='col-form-label'>Public</label>
                                    <select class='form-select' id='is_public' name='is_public'>
                                        <option value='1'>Oui</option>
                                        <option value='0'>Non</option>
                                    </select>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' class='btn btn-primary' id='save-btn'>Save changes</button>
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
                                <form id='deleteDocumentForm' method='POST' action='/documents/delete' style='display: inline;'>
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
                                        <h6>Informations générales</h6>
                                        <p><strong>Titre :</strong> <span id='view_title'></span></p>
                                        <p><strong>Description :</strong> <span id='view_description'></span></p>
                                        <p><strong>Catégorie :</strong> <span id='view_category'></span></p>
                                        <p><strong>Statut :</strong> <span id='view_status' class='badge'></span></p>
                                    </div>
                                    <div class='col-md-6'>
                                        <h6>Informations techniques</h6>
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
    }
}
?>