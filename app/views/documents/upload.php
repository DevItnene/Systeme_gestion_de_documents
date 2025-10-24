<?php
namespace App\Views\Documents;

use App\Models\Category;
use App\Models\Document;

class Upload
{
    private $categories;
    public function __construct() {
        $this->categories = new Category();
    }

    public function uploadDocument() {
        $categories = $this->categories->getAllCategories();
        $addForm = "
            <div class='container form-card'>
                <div class='card shadow-md'>
                    <div class='card-header text-white'>
                        <h4 class='mb-0'><i class='bi bi-pencil-square me-2'></i>Ajouter un document</h4>
                    </div>
                    <div class='card-body'>
                        <div class='alert alert-danger alert-dismissible fade show mb-4' id='dangerMsgBox' role='alert'>
                            <i class='fas fa-exclamation-circle me-2'></i>
                            <span class='dangerMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <div class='alert alert-success alert-dismissible fade show mb-4' id='SuccessMsgBox' role='alert'>
                            <i class='fas fa-check-circle me-2'></i>
                            <span class='successMessage'></span>
                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                        </div>
                        <form id='addform' method='post' enctype='multipart/form-data' novalidate>
                            <!-- Titre -->
                            <div class='mb-3'>
                                <label for='title' class='form-label'>Titre <span class='text-danger'>*</span></label>
                                <input type='text' class='form-control' id='title' name='title' placeholder='Entrez un titre' required>
                                <div class='invalid-feedback'>Le titre est requis.</div>
                            </div>

                            <!-- Description -->
                            <div class='mb-3'>
                                <label for='description' class='form-label'>Description</label>
                                <textarea class='form-control' id='description' name='description' rows='4' placeholder='Entrez une description'></textarea>
                            </div>

                            <!-- Document -->
                            <div class='mb-3'>
                                <label for='document_file' class='form-label'>Document <span class='text-danger'>*</span></label>
                                <input class='form-control' type='file' id='document_file' name='document_file' required>
                                <small style='opacity: 0.7'>Les formats acceptés : pdf, text, pptx, xlsx, docx, csv, json</small>
                                <div class='invalid-feedback'>Veuillez téléverser le document.</div>
                            </div>

                            <!-- Catégorie -->
                            <div class='mb-3'>
                                <label for='category_id' class='form-label'>Catégorie <span class='text-danger'>*</span></label>
                                <select class='form-select' id='category_id' name='category_id' required>
                                    <option value='' disabled selected>-- Sélectionnez une catégorie --</option>
                                    ";
                                        foreach ($categories as $category) {
                                            $addForm .=" <option value='{$category['id']}'> {$category['name']} </option>";
                                        }
                                    $addForm .="
                                </select>
                                <div class='invalid-feedback'>Veuillez choisir une catégorie.</div>
                            </div>

                            <!-- Public -->
                            <div class='mb-3'>
                                <label for='is_public' class='form-label'>Public <span class='text-danger'>*</span></label>
                                <select class='form-select' id='is_public' name='is_public' required>
                                    <option value='' disabled selected>-- Choisir --</option>
                                    <option value='1'>Oui</option>
                                    <option value='0'>Non</option>
                                </select>
                                <div class='invalid-feedback'>Veuillez indiquer si le document est public.</div>
                            </div>

                            <!-- Bouton -->
                            <div class='col-12 d-flex justify-content-end mt-4'>
                                <button type='submit' class='col-md-4 btn btn-primary add-btn'>
                                    <i class='bi bi-send me-1'></i> Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        ";

        echo $addForm;
    }
}
