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
    public function get($id) {
        $document = $this->documents_model->getDocumentById(intval($id));
        echo json_encode($document);
    }

    // Methode pour mettre a jour un document
    public function update() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false,"message" => "Méthode non autorisée"]);
            return;
        }

        $id = $_POST["doc_id"] ?? null;
        $title = $_POST["title"] ?? '';
        $description = $_POST["description"] ?? '';
        $category_id = $_POST["category_id"] ?? null;
        $is_public = isset($_POST["is_public"]) ? 1 : 0;

        $document = $this->documents_model->getDocumentById(intval($id));
        $user = $this->auth->user();

        // if (!$document || (!$this->auth->isAdmin() && $document['user_id'] != $user['id'])) {
        //     http_response_code(403);
        //     echo json_encode(['success'=> false,'message'=> 'Accès non autorisé']);
        //     return;
        // }

        $success = $this->documents_model->updateDocment($id, [
            'title'=> $title,
            'description'=> $description,
            'category_id'=> $category_id,
            'is_public'=> $is_public
        ]);
        if ($success) {
            echo json_encode(['success'=> true,'message'=> 'Document modifié avec succès']);
        } else  {
            echo json_encode(['success'=> false,'message'=> 'Erreur lors de la modification']);
        }

    }

}
?>