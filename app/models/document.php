<?php
namespace App\Models;

use App\Core\Auth;
use App\Core\Database;

class Document {
    private $documents;
    private $auth;
    public function __construct() {
        $this->documents = new Database();
        $this->auth = new Auth();
    }

    public function getAllDocuments() {
        ($this->auth->isAdmin()) ?
            $results = $this->documents->queryFetchAll("SELECT * FROM documents")
            :
            $results = $this->documents->queryFetchAll("SELECT * FROM documents WHERE user_id = ?", [$_SESSION["user_id"]]);
        
        return $results;
    }

    public function getDocument($id) {
        ($this->auth->isAdmin()) ?
            $results = $this->documents->queryFetchAll("SELECT * FROM documents WHERE id = ?", [$id])
            :
            $results = $this->documents->queryFetchAll("SELECT * FROM documents WHERE id = ? AND user_id = ?", [$id, $_SESSION["user_id"]]);
        
        return $results;
    }
}
?>