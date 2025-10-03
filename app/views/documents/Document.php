<?php
namespace App\Views\Documents;

use App\Models\Document as DocumentModel;

class Document {

    private $docs;

    public function __construct() {
        $this->docs = new DocumentModel();
    }

    public function showDocument($id) {
        $document = $this->docs->getDocument(intval($id));
    }

    public function showDocuments() {
        $documents = $this->docs->getAllDocuments();
        if (empty($documents)) {
            $table = "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>#</th>
                        <th scope='col'>Titre</th>
                        <th scope='col'>Description</th>
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
            
            $table = " 
                <table class='table table-hover'>
                    <thead>
                        <tr>
                            <th scope='col'>#</th>
                            <th scope='col'>Titre</th>
                            <th scope='col'>Description</th>
                            <th scope='col'>Catégorie id</th>
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
                      
                            $is_public = ($document['is_public']==1) ? 'Oui' : 'Non';
                            $table .= "
                                <tr>
                                    <th scope='row'>{$document['id']}</th>
                                    <td>{$document['title']}</td>
                                    <td>{$document['description']}</td>
                                    <td>{$document['category_id']}</td>
                                    {$td_user_id}
                                    <td>{$is_public}</td>
                                    <td>{$document['download_count']}</td>
                                    <td>{$document['created_at']}</td>
                                    <td class='links-action'>
                                        <a href='#' data-bs-toggle='modal' data-bs-target='#actionModal' data-docs-id='{$document['id']}' data-user-id='{$_SESSION['user_id']}'><i class='fa-solid fa-eye'></i></a>
                                        <a href='#' data-bs-toggle='modal' data-bs-target='#actionModal' data-docs-id='{$document['id']}' data-user-id='{$_SESSION['user_id']}'><i class='fa-solid fa-pen-to-square'></i></a>
                                        <a href='#' data-bs-toggle='modal' data-bs-target='#actionModal' data-docs-id='{$document['id']}' data-user-id='{$_SESSION['user_id']}'><i class='fa-solid fa-trash' style='color: lightcoral;'></i></a>
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

        echo "
            <div class='modal fade' id='actionModal' tabindex='-1' aria-labelledby='actionModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='actionModalLabel'>Modal title</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body p-5'>
                            <form method='GET'>
                                <div class='mb-3 row'>
                                    <label for='name' class='col-sm-2 col-form-label'>Titre</label>
                                    <input type='text' class='form-control' id='name' value='Titre'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='description' class='col-sm-2 col-form-label'>Description</label>
                                    <input type='text' class='form-control' id='description' value='Titre'>
                                </div>
                                <div class='mb-3 row'>
                                    <label for='category_id' class='col-sm-2 col-form-label'>Catégorie Id</label>
                                    <input type='text' class='form-control' id='category_id' value='Titre'>
                                </div>
                                <div class='modal-footer'>
                                    <button type='cancel' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' class='btn btn-primary'>Save changes</button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        ";

        var_dump($_GET);
    }
}
?>