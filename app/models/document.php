<?php
namespace App\Models;

use App\Core\Auth;
use App\Core\Database;

class Document {
    private $db;
    private $auth;
    public function __construct() {
        $this->db = new Database();
        $this->auth = new Auth();
    }

    // Methode pour une requete pour recuperer tous les documents
    public function getAllDocuments() {
        ($this->auth->isAdmin()) ?
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        ORDER BY d.created_at DESC")
            :
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.user_id = ?
                                                        ORDER BY d.created_at DESC", [$_SESSION["user_id"]]);
        
        return $results;
    }

    // Methode pour une requete afin de recuperer un document
    public function getDocumentById($id) {
        ($this->auth->isAdmin()) ?
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.user_id = ?
                                                        ORDER BY d.created_at DESC", [$id])
            :
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.user_id = ?
                                                        ORDER BY d.created_at DESC", [$id, $_SESSION["user_id"]]);
        
        return $results;
    }

    // Methode pour une requete qui renvoyer le nombre total de documents
     public function getDocumentCounts() {
        ($this->auth->isAdmin()) ?
            $results = $this->db->queryFetchAll("SELECT COUNT(*) as Total FROM documents")
            :
            $results = $this->db->queryFetchAll("SELECT COUNT(*) as Total FROM documents WHERE user_id = ?", [$_SESSION["user_id"]]);
        
        return $results;
    }

    // Methode pour une requete de update d'un document
    public function updateDocment($id, $data) {
        return $this->db->query("UPDATE documents 
                                      SET title = ?, description = ?,
                                      category_id = ?, is_public = ?, updated_at = NOW()
                                      WHERE id = ?",
                                      [
                                        $data["title"], 
                                        $data["description"], 
                                        $data["category_id"], 
                                        $data["is_public"],
                                        $id
                                      ]
        
        );
    }

    // Methode pour une requete de supprission d'un document
    public function deleteDocment($id) {
        return $this->db->query("DELETE FROM documents WHERE id = ?", [$id]);
    }

    // TODO : Apres faut mettre dans category
    public function getAllCategories() {
        $results = $this->db->queryFetchAll("SELECT * FROM categories");
        return $results;
    }
}
?>