<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Models\User;
use App\Views\Documents\Document;
class DocumentController {

    private $auth;
    private $documents;
    public function __construct() {
        $this->auth = new Auth();
        $this->auth->requireAuth();
        $this->documents = new Document();
    }

    public function documentList() {
        
        require_once __DIR__ ."/../Views/Layouts/Header.php";

        $this->documents->showDocuments();

        require_once __DIR__ ."/../Views/Layouts/Footer.php";

    }

}
?>