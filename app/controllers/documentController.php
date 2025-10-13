<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Views\Documents\Document;
use App\Models\Document as DocumentModel;
class DocumentController {

    private $auth;
    private $documents;
    private $documents_model;
    public function __construct() {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->documents = new Document();
        $this->documents_model = new DocumentModel();
    }

    // Fonction pour lister tous les documents
    public function documentList() {
        
        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->documents->displayDocuments();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";

    }

    // Methode pour récupérer les données d'un document
    public function getDocument($id) {
        $document = $this->documents_model->getDocumentById(intval($id));
        return $document;
    }

    // Methode pour mettre a jour un document
    public function update() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false,"message" => "Méthode non autorisée"]);
            return;
        }

        $id = htmlentities(trim($_POST["doc_id"])) ?? null;
        $title = htmlentities(trim($_POST["title"])) ?? '';
        $description = htmlentities(trim($_POST["description"])) ?? '';
        $category_id = intval(htmlentities(trim($_POST["category_id"]))) ?? null;
        $is_public = intval(htmlentities(trim($_POST["is_public"])));

        if (!empty($_FILES["document_file"]["name"])) {
            if ($_FILES['document_file']['error'] !== \UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement du fichier.']);
                return;
            }

            $file_name = $_FILES['document_file']['name'];
            $file_type = "application/" . pathinfo($file_name, \PATHINFO_EXTENSION);
            $file_size = $_FILES["document_file"]["size"];
            $file_path = "/uploads/documents/$file_name";
            $target_path = __DIR__ . "/../Public/assets/uploads/documents/" . $file_name;

            if (move_uploaded_file($_FILES['document_file']['tmp_name'], $target_path)) {
                $data = [
                        'title'=> $title,
                        'description'=> $description,
                        'file_name'=> $file_name,
                        'file_path'=> $file_path,
                        'file_size'=> $file_size,
                        'file_type'=> $file_type,
                        'category_id'=> $category_id,
                        'is_public'=> $is_public
                    ];
                // echo json_encode(['success'=> true, 'message'=> 'Fichier déplacé avec succès']);
            } else {
                echo json_encode(['success'=> false, 'message'=> 'Échec du déplacement du fichier']);
                return;
            }
        } else {
            $data = [
                    'title'=> $title,
                    'description'=> $description,
                    'category_id'=> $category_id,
                    'is_public'=> $is_public
                ];
        }

        // $document = $this->documents_model->getDocumentById(intval($id));
        // $user = $this->auth->user();

        // if (!isset($document) || ($this->auth->isAdmin() == false && ($document['user_id'] != $user['id']))) { 
        //     // http_response_code(403);
        //     echo json_encode(['success'=> false,'message'=> 'Accès non autorisé']);
        //     return;
        // }

        $document = $this->documents_model->getDocumentById(intval($id));

        if (
                $document[0]['title'] === $title && 
                $document[0]['description'] === $description && 
                $document[0]['category_id'] === $category_id &&
                $document[0]['is_public'] === $is_public &&
                $_FILES['document_file']['name'] === ''
            ) {
                echo json_encode(['success'=> true,'message'=> 'Aucune information modifiée.']);
                return;
            } else {
                $success = $this->documents_model->updateDocument($id, $data);
                if ($success) {
                    echo json_encode(['success'=> true,'message'=> 'Document modifié avec succèss.']);
                } else  {
                    echo json_encode(['success'=> false,'message'=> 'Erreur lors de la modification']);
                }  
            }

    }

    // Methode pour supprimer un document
    public function delete() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false,"message" => "Méthode non autorisée"]);
            return;
        }

        $id = $_POST["delete_document_id"] ?? null;

        $success = $this->documents_model->deleteDocument($id);

        if ($success) {
            echo json_encode(['success'=> true,'message'=> $id]);
        } else  {
            echo json_encode(['success'=> false,'message'=> 'Erreur lors de la suppression du document']);
        }
    }

}
?>