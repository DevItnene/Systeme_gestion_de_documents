<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Views\Documents\Upload;
use App\Views\Documents\Document;
use App\Models\Document as DocumentModel;
class DocumentController {

    private $auth;
    private $documents;
    private $documents_model;
    private $upload;
    public function __construct() {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->documents = new Document();
        $this->documents_model = new DocumentModel();
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


    // Methode pour récupérer les données d'un document
    public function getDocument($id) {
        return $this->documents_model->getDocumentById(intval($id));
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

        $documents = $this->documents_model->getAllDocuments();
        $results = [];

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
                    echo json_encode(['success'=> false, 'message'=> 'Échec du déplacement du fichier']);
                    return;
                }
            } else {
                echo json_encode(['success'=> false, 'message'=> 'Échec, le titre et/ou le nom du fichier est utilisé par un utilisateur.']);
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

        // $document = $this->documents_model->getDocumentById(intval($id));
        // $user = $this->auth->user();

        // if (!isset($document) || ($this->auth->isAdmin() == false && ($document['user_id'] != $user['id']))) { 
        //     // http_response_code(403);
        //     echo json_encode(['success'=> false,'message'=> 'Accès non autorisé']);
        //     return;
        // }

        $document = $this->getDocument(intval($id));

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
        $user = $this->auth->user();

        $results = [];

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

}
?>