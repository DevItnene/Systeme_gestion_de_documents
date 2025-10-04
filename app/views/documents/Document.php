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
                            
                            $file_type = $document['file_type'] == 'application/pdf' ? 'pdf' : 
                                (strpos($document['file_type'], 'word') !== false ? 'word' :
                                (strpos($document['file_type'], 'excel') !== false ? 'excel' :
                                (strpos($document['file_type'], 'presentation') !== false ? 'powerpoint' : 'text')));
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
                                        <a href='#' class='see-btn' data-bs-toggle='modal' data-bs-target='#viewModal' 
                                            data-docs-id='{$document['id']}'>
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
                                        <a href='#' data-bs-toggle='modal' data-bs-target='#deleteModal' 
                                            data-docs-id='{$document['id']}'>
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
                        <form id='editDocumentForm' method='POST' action='/documents/update'>
                            <div class='modal-body modal-action p-5'>
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
                                    <label for='category_id' class='col-form-label'>Catégorie Id</label>
                                    <select class='form-select' id='category_id' name='category_id'>
                                    ";
                                        foreach ($categories as $category) {
                                            $editModal .=" <option value='{$category['id']}'> {$category['name']} </option>";
                                        }
                                $editModal .="
                                    </select>
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
    }
}
?>