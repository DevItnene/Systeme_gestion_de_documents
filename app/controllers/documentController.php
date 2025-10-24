<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Views\Documents\Upload;
use App\Views\Documents\Category;
use App\Views\Documents\Document;
use App\Models\Category as CategoryModel;
use App\Models\Document as DocumentModel;

class DocumentController {

    private $auth;
    private $documents;
    private $categories;
    private $documents_model;
    private $categories_models;
    private $upload;
    public function __construct() {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->documents = new Document();
        $this->documents_model = new DocumentModel();
        $this->categories = new Category();
        $this->categories_models = new CategoryModel();
        $this->upload = new Upload();
    }

    // Fonction pour lister tous les documents
    public function documentList() {
        
        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->documents->displayDocuments();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";

    }

    // Fonction pour lister tous les documents partagés
    public function shareDocumentList() {
        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->documents->displayShareDocuments();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";
    }

    // Fonction pour lister tous les documents publics
    public function publicDocumentList() {
        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->documents->displayPublicDocuments();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";
    }

    // Methode pour récupérer les données d'un document
    public function getDocument($id) {
        return $this->documents_model->getDocumentById(intval($id));
    }

    // Methode pour mettre a jour un document
    public function update() {
        header('Content-Type: application/json');
        ini_set('display_errors', 1);
        error_reporting(\E_ALL);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false,"message" => "Méthode non autorisée"]);
            return;
        }
                
        $id = htmlentities(trim($_POST["doc_id"])) ?? null;
        $title = htmlentities(trim($_POST["title"])) ?? '';
        $description = htmlentities(trim($_POST["description"])) ?? '';
        $category_id = intval($_POST["category_id"]) ?? null;
        $is_public = intval($_POST["is_public"]);

        if ($title == '') {
            echo json_encode(['success' => false, 'message' => 'Le titre du document ne doit pas être vide']);
            return;
        }

        $documents = $this->documents_model->getAllDocuments();
        $results = [];
        
        if (!empty($_FILES["document_file"]["name"])) {

            switch ($_FILES['document_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    echo json_encode(['success' => false, 'message' => 'Le fichier dépasse la taille maximale autorisée par le serveur.']);
                    return;
                case UPLOAD_ERR_FORM_SIZE:
                    echo json_encode(['success' => false, 'message' => 'Le fichier dépasse la taille maximale autorisée par le formulaire HTML.']);
                    return;
                case UPLOAD_ERR_PARTIAL:
                    echo json_encode(['success' => false, 'message' => 'Le fichier n\'a été que partiellement uploadé.']);
                    return;
                case UPLOAD_ERR_NO_FILE:
                    echo json_encode(['success' => false, 'message' => 'Aucun fichier n\'a été envoyé.']);
                    return;
            }
            $file_name = $_FILES['document_file']['name'];
            $file_type = "application/" . pathinfo($file_name, \PATHINFO_EXTENSION);
            $file_size = $_FILES["document_file"]["size"];
            $file_path = "/uploads/documents/$file_name";
            $target_path = __DIR__ . "/../Public/assets/uploads/documents/" . $file_name;

            foreach ($documents as $doc) {
                if (stripos($doc['file_name'], $file_name) !== false) {
                    $results[] = $doc;
                }
            }

            if (empty($results)) {
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
                    echo json_encode(['success'=> false, 'message'=> 'Échec lors du déplacement du fichier']);
                    return;
                }
            } else {
                echo json_encode(['success'=> false, 'message'=> 'Échec, le nom du fichier est utilisé par un utilisateur.']);
                return;
            }
        } else {
            $data = [
                'title'=> $title,
                'description'=> $description,
                'category_id'=> $category_id,
                'is_public'=> $is_public
            ];
            
            // foreach ($documents as $doc) {
            //     if (stripos($doc['title'], $title) !== false) {
            //         $results[] = $doc;
            //     }
            // }

            // if (empty($results)) {
            //     $data = [
            //         'title'=> $title,
            //         'description'=> $description,
            //         'category_id'=> $category_id,
            //         'is_public'=> $is_public
            //     ];
            // } else {
            //     echo json_encode(['success'=> false, 'message'=> 'Le titre est utilisé par un autre utilisateur']);
            //     return;
            // }
        }
        
        $document = $this->getDocument(intval($id));

        if (!isset($document[0])) {
            // http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Document introuvable']);
            return;
        }

        $current = $document[0];

        if (
               $current['title'] === $title && 
               $current['description'] === $description && 
               $current['category_id'] === $category_id &&
               $current['is_public'] === $is_public &&
                $_FILES['document_file']['name'] === ''
            ) {
                echo json_encode(['success'=> true,'message'=> 'Aucune information modifiée.']);
                return;
            } else {
                $success = $this->documents_model->updateDocument($id, $data);
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Document mis à jour avec succès.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour du document.']);
                } 
            }

    }

    // Methode pour mettre a jour un document partagé
    public function updateShareDocument() {
        header('Content-Type: application/json');
        ini_set('display_errors', 1);
        error_reporting(\E_ALL);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
            return;
        }

        $id = isset($_POST["doc_id"]) ? intval(trim($_POST["doc_id"])) : null;
        $title = isset($_POST["title"]) ? trim($_POST["title"]) : '';
        $description = isset($_POST["description"]) ? trim($_POST["description"]) : '';
        $category_id = isset($_POST["category_id"]) ? intval(trim($_POST["category_id"])) : null;

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Le titre du document ne doit pas être vide']);
            return;
        }

        $data = [
            'title' => htmlentities($title),
            'description' => htmlentities($description),
            'category_id' => $category_id
        ];

        $document = $this->getDocument($id);

        if (!isset($document[0])) {
            // http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Document introuvable']);
            return;
        }

        $current = $document[0];

        if (
            $current['title'] === $title &&
            $current['description'] === $description &&
            $current['category_id'] === $category_id
        ) {
            echo json_encode(['success' => true, 'message' => 'Aucune information modifiée.']);
            return;
        }

        $success = $this->documents_model->updateDocument($id, $data, true);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Document mis à jour avec succès.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour du document.']);
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
        $document_share_id = $_POST["delete_document_share_id"] ?? null;
        $delete_category_id = $_POST["delete_category_id"] ?? null;

        if ($delete_category_id) {
            $success = $this->categories_models->deleteCategory($delete_category_id);
        } else {
            $id ? $success = $this->documents_model->deleteDocument($id)
                : $success = $this->documents_model->deleteDocument($document_share_id, true);
        }


        if ($success) {
            echo json_encode(['success'=> true,'message'=> $id]);
        } else  {
            echo json_encode(['success'=> false,'message'=> 'Erreur lors de la suppression du document']);
        }
    }

    public function canDownload($id) {
        header('Content-Type: application/json');

        if (!isset($id) || !is_numeric($id)) {
            echo json_encode(["success" => false, "message" => "ID invalide."]);
            return;
        }

        $id = intval($id);
        $document_share = $this->documents_model->getSharedDocument($id);

        // Si le document n’est pas partagé, on vérifie s’il est public
        $document = $this->documents_model->getDocumentById($id);

        if (!$document) {
            echo json_encode(["success" => false, "message" => "Document introuvable."]);
            return;
        }

        if ($document[0]['is_public'] == 1) {
            echo json_encode(["success" => true]);
            return;
        }

        // Vérifier la permission de partage
        if (!$document_share || $document_share[0]["permission"] !== "download") {
            echo json_encode(["success" => false, "message" => "Vous n'avez pas la permission de télécharger ce document."]);
            return;
        }

        echo json_encode(["success" => true]);
    }

    // Methode pour telecharger un document
    public function download($id) {
        if (!isset($id) || !is_numeric($id)) {
            http_response_code(400);
            exit ("ID invalide.");
        }

        $id = intval($id);
        $document = $this->getDocument($id);

        if (!$document) {
            http_response_code(404);
            exit ("Fichier non trouvé.");
        }

        $file_name = $document[0]['file_name'];
        $file_path = __DIR__ . "/../Public/assets" . $document[0]['file_path'];

        if (!file_exists($file_path)) {
            http_response_code(404);
            exit ("Fichier introuvable sur le serveur");
        }

        $this->documents_model->incrementDownloadCount($id);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Transfert-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        ob_clean();
        flush();
        
        readfile($file_path);
        exit;
    }

    // Methode pour uploader un document
    public function insert() {
        header('Content-Type: application/json');
        ini_set('display_errors', 1);
        error_reporting(\E_ALL);
        
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false,"message" => "Méthode non autorisée"]);
            return;
        }

        $documents = $this->documents_model->getAllDocuments();

        $title = htmlentities(trim($_POST["title"])) ?? '';
        $description = htmlentities(trim($_POST["description"])) ?? '';
        $category_id = intval(htmlentities(trim($_POST["category_id"]))) ?? null;
        $is_public = intval(htmlentities(trim($_POST["is_public"])));

        // if (empty($title) || empty($category_id) || empty($is_public) || empty($_FILES["document_file"]["name"])) {
        //     echo json_encode(["success"=> false,"message"=> ""]);
        // }

        $user = $this->auth->user();
        $results = [];

        if (!empty($_FILES["document_file"]["name"])) {

            switch ($_FILES['document_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    echo json_encode(['success' => false, 'message' => 'Le fichier dépasse la taille maximale autorisée par le serveur.']);
                    return;
                case UPLOAD_ERR_FORM_SIZE:
                    echo json_encode(['success' => false, 'message' => 'Le fichier dépasse la taille maximale autorisée par le formulaire HTML.']);
                    return;
                case UPLOAD_ERR_PARTIAL:
                    echo json_encode(['success' => false, 'message' => 'Le fichier n\'a été que partiellement uploadé.']);
                    return;
                case UPLOAD_ERR_NO_FILE:
                    echo json_encode(['success' => false, 'message' => 'Aucun fichier n\'a été envoyé.']);
                    return;
            }

            $file_name = $_FILES['document_file']['name'];
            $file_type = "application/" . pathinfo($file_name, \PATHINFO_EXTENSION);
            $file_size = $_FILES["document_file"]["size"];
            $file_path = "/uploads/documents/$file_name";
            $target_path = __DIR__ . "/../Public/assets/uploads/documents/" . $file_name;

            foreach ($documents as $doc) {
                if (stripos($doc['file_name'], $file_name) !== false || stripos($doc['title'], $title) !== false) {
                    $results[] = $doc;
                }
            }

            if (empty($results)) {
                if (move_uploaded_file($_FILES['document_file']['tmp_name'], $target_path)) {
                $data = [
                        'title'=> $title,
                        'description'=> $description,
                        'file_name'=> $file_name,
                        'file_path'=> $file_path,
                        'file_size'=> $file_size,
                        'file_type'=> $file_type,
                        'category_id'=> $category_id,
                        'user_id'=> $user['id'],
                        'is_public'=> $is_public
                    ];
                // echo json_encode(['success'=> true, 'message'=> 'Fichier déplacé avec succès']);
                } else {
                    echo json_encode(['success'=> false, 'message'=> 'Échec du déplacement du document']);
                    return;
                }
            } else {
                echo json_encode(['success'=> false, 'message'=> 'Échec, le titre et/ou le nom du fichier est utilisé par un utilisateur.']);
                return;
            }
        }
        $success = $this->documents_model->insertDocument($data);
        if ($success) {
            echo json_encode(['success'=> true,'message'=> 'Document uploadé avec succèss.']);
        } else  {
            echo json_encode(['success'=> false,'message'=> 'Erreur lors de l\'ajout du document']);
        }  

    }

    // Methode pour partager un document
    public function shareDocument() {
        header('Content-Type: application/json');
        ini_set('display_errors', 1);
        error_reporting(\E_ALL);
        
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false,"message" => "Méthode non autorisée"]);
            return;
        }

        if (!array_key_exists('permission', $_POST) || !array_key_exists('shared_with_user_id', $_POST)) {
            echo json_encode(['success' => false, 'message' => 'Veuillez renseigné les champs obligatoires.']);
            return;
        }

        $id = htmlentities(trim($_POST["document_share_id"])) ?? null;
        $shared_with_users_id = $_POST['shared_with_user_id'] ?? null;
        $permission = $_POST['permission'] ?? null;
        $user = $this->auth->user();
        $shared_by = $user['id'];

        if ($shared_with_users_id[0] == 'everyone') {
            $data = [
                'document_id' => $id,
                'is_public'=> '1',
            ];

           $success = $this->documents_model->isPublicDocument($data);
            if ($success) {
                echo json_encode(['success'=> true,'message'=> 'Document rendu public avec succèss.']);
                return;
            } else  {
                echo json_encode(['success'=> false,'message'=> 'Erreur lors du partage du document']);
                return;
            } 
        }

        foreach ($shared_with_users_id as $shared_with_user_id) {
           $data = [
                'document_id' => $id,
                'shared_with_user_id'=> $shared_with_user_id,
                'permission'=> $permission,
                'shared_by'=> $shared_by,
            ];

            $success = $this->documents_model->sharingDocument($data);
        }
        
        if ($success) {
            echo json_encode(['success'=> true,'message'=> 'Document partagé avec succèss.']);
        } else  {
            echo json_encode(['success'=> false,'message'=> 'Erreur lors du partage du document']);
        }
    }

    // Methode pour afficher le formulaire d'ajout
    public function uploadPage() {
        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->upload->uploadDocument();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";
    }

    // Methode pour afficher les Categories
    public function displayCategories() {
        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->categories->displayCategories();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";
    }

    // Methode pour mettre a jour une catégorie
    public function updateCategory() {
        header('Content-Type: application/json');
        ini_set('display_errors', 1);
        error_reporting(\E_ALL);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
            return;
        }

        $id = isset($_POST["category_id"]) ? intval($_POST["category_id"]) : null;
        $name = isset($_POST["category_name"]) ? trim($_POST["category_name"]) : '';
        $description = isset($_POST["description"]) ? trim($_POST["description"]) : '';

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Le nom de la catégorie ne doit pas être vide']);
            return;
        }

        $data = [
            'name' => htmlentities($name),
            'description' => htmlentities($description)
        ];

        $category = $this->categories_models->getCategoryById($id);

        if (!isset($category[0])) {
            echo json_encode(['success' => false, 'message' => 'Catégorie introuvable']);
            return;
        }

        $current = $category[0];

        if (
            $current['name'] === $name &&
            $current['description'] === $description
        ) {
            echo json_encode(['success' => true, 'message' => 'Aucune information modifiée.']);
            return;
        }

        $success = $this->categories_models->updateCategory($id, $data);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Catégorie mis à jour avec succès.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Échec de la mise à jour de la Catégorie.']);
        }
    }

    // Methode pour ajouter une categorie
    public function insertCategory() {
        header('Content-Type: application/json');
        ini_set('display_errors', 1);
        error_reporting(\E_ALL);
        
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);
            echo json_encode(["success" => false,"message" => "Méthode non autorisée"]);
            return;
        }

        $categories = $this->categories_models->getAllCategories();

        $category_name = htmlentities(trim($_POST["category_name"])) ?? '';
        $description = htmlentities(trim($_POST["description"])) ?? '';

        if (empty($category_name)) {
            echo json_encode(["success"=> false,"message"=> "Veuillez saisir le nom de la catégorie"]);
            return;
        }

        $user = $this->auth->user();
        $results = [];

        foreach ($categories as $category) {
            if (stripos($category['name'], $category_name) !== false) {
                $results[] = $category;
            }
        }

        if (empty($results)) {
            $data = [
                'name'=> $category_name,
                'description'=> $description,
                'user_id'=> $user['id']
            ];
        } else {
            echo json_encode(['success'=> false, 'message'=> 'Échec, le nom de la catégorie existe déjà.']);
            return;
        }

        $success = $this->categories_models->insertCategory($data);
        if ($success) {
            echo json_encode(['success'=> true,'message'=> 'Catégorie ajoutée avec succèss.']);
        } else  {
            echo json_encode(['success'=> false,'message'=> 'Erreur lors de l\'ajout de la catégorie']);
        }  

    }

}
?>