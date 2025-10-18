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
    public function getAllDocuments($limit = null, $offset = null) {
        if ($limit === null || $offset === null) {
            return $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                    FROM  documents d
                                                    LEFT JOIN categories c ON d.category_id = c.id
                                                    ORDER BY d.created_at DESC");
        } 
        
        ($this->auth->isAdmin()) ?
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        ORDER BY d.created_at DESC LIMIT $limit OFFSET $offset")
            :
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.user_id = ?
                                                        ORDER BY d.created_at DESC LIMIT $limit OFFSET $offset", [$_SESSION["user_id"]]);
        
        return $results;
    }

    // Methode pour une requete afin de recuperer un document
    public function getDocumentById($id) {
        ($this->auth->isAdmin()) ?
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.id = ?", [$id])
            :
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM  documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.id = ? AND d.user_id = ?", [$id, $_SESSION["user_id"]]);
        
        return $results;
    }

    // Methode pour rechercher un ou plusieurs documents
    public function searchDocument($search, $limit = null, $offset = null) {
        ($this->auth->isAdmin()) ?
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name, u.name as user_name
                                                        FROM documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        LEFT JOIN users u ON d.user_id = u.id
                                                        WHERE d.title LIKE ? OR u.name LIKE ? OR c.name LIKE ?
                                                        ORDER BY d.created_at DESC LIMIT $limit OFFSET $offset
                                                    ", ["%$search%", "%$search%", "%$search%"])
            :
            $results = $this->db->queryFetchAll("  SELECT d.*, c.name as category_name
                                                        FROM documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.user_id = ? AND (d.title LIKE ? OR c.name LIKE ?)
                                                        ORDER BY d.created_at DESC LIMIT $limit OFFSET $offset
                                                    ", [$_SESSION["user_id"], "%$search%", "%$search%"]);
        
        return $results;
    }

    // Methode pour une requete qui renvoyer le nombre total de documents
    public function getDocumentCounts($search = null) {
        if ($search != null) {
            ($this->auth->isAdmin()) ?
                $results = $this->db->queryFetch(" SELECT COUNT(*) as Total 
                                                        FROM documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE (d.title LIKE ? OR c.name LIKE ?)
                                                    ", ["%$search%", "%$search%"])
                :
                $results = $this->db->queryFetch("SELECT COUNT(*) as Total 
                                                        FROM documents d
                                                        LEFT JOIN categories c ON d.category_id = c.id
                                                        WHERE d.user_id = ? AND (d.title LIKE ? OR c.name LIKE ?)
                                                    ", [$_SESSION["user_id"], "%$search%", "%$search%"]);
            return $results;
        }
        
        ($this->auth->isAdmin()) ?
            $results = $this->db->queryFetch("SELECT COUNT(*) as Total FROM documents")
            :
            $results = $this->db->queryFetch("SELECT COUNT(*) as Total FROM documents WHERE user_id = ?", [$_SESSION["user_id"]]);
        
        return $results;
    }

    // Methode pour recuperer un document partagé
    public function getSharedDocument($user_id) {
        return $this->db->query("SELECT * documents WHERE shared_by = ?", [$user_id]);
    }

    // Methode pour une requete pour recuperer tous les documents partagés
    public function getAllShareDocuments($limit = null, $offset = null) {

        return $this->db->queryFetchAll("  SELECT d_s.id, d_s.created_at, 
                                                d.id as document_id, d.title, d.description, d.file_name, d.file_size, d.file_type, d.download_count,
                                                u.name as username, c.name as category_name, c.id as category_id
                                                FROM  document_shares d_s
                                                LEFT JOIN documents d ON d.id = d_s.document_id
                                                LEFT JOIN users u ON u.id = d_s.shared_by 
                                                LEFT JOIN categories c ON c.id = d.category_id
                                                WHERE d_s.shared_with_user_id = ?
                                                ORDER BY d_s.created_at DESC
                                                LIMIT  $limit OFFSET $offset", [$_SESSION["user_id"]]);
    }

    // Methode pour rechercher un ou plusieurs documents partagés
    public function searchShareDocument($search, $limit = null, $offset = null) {
           return $this->db->queryFetchAll("   SELECT d_s.id, d_s.created_at, 
                                                    d.id as document_id, d.title, d.description, d.file_name, d.file_size, d.file_type, d.download_count,
                                                    u.name as username, c.name as category_name, c.id as category_id
                                                    FROM  document_shares d_s
                                                    LEFT JOIN documents d ON d.id = d_s.document_id
                                                    LEFT JOIN users u ON u.id = d_s.shared_by 
                                                    LEFT JOIN categories c ON c.id = d.category_id
                                                    WHERE d_s.shared_with_user_id = ? AND (d.title LIKE ? OR u.name LIKE ? OR c.name LIKE ?)
                                                    ORDER BY d_s.created_at DESC 
                                                    LIMIT  $limit OFFSET $offset
                                                ", [$_SESSION["user_id"], "%$search%", "%$search%", "%$search%"]);
    }
    
    // Methode pour une requete qui renvoyer le nombre total de documents partagés pour un utilisateur
    public function getShareDocumentCounts($search = null) {
        if ($search != null) {
            return $this->db->queryFetch(" SELECT COUNT(*) as Total
                                                FROM  document_shares d_s
                                                LEFT JOIN documents d ON d.id = d_s.document_id
                                                LEFT JOIN users u ON u.id = d_s.shared_by 
                                                LEFT JOIN categories c ON c.id = d.category_id
                                                WHERE d_s.shared_with_user_id = ? AND (d.title LIKE ? OR u.name LIKE ? OR c.name LIKE ?)
                                                ", [$_SESSION['user_id'], "%$search%", "%$search%", "%$search%"]);
        }

        return $this->db->queryFetch("SELECT COUNT(*) as Total FROM document_shares WHERE shared_with_user_id = ?", [$_SESSION["user_id"]]);
    }

    // Methode pour une requete de update d'un document
    public function updateDocument($id, $data, $shareDocument = null) {
        if ($shareDocument != null) {
            return $this->db->query("UPDATE documents 
                                          SET title = ?, description = ?, category_id = ?,
                                          updated_at = NOW() WHERE id = ?",
                                          [
                                            $data["title"], 
                                            $data["description"],
                                            $data["category_id"],
                                            $id
                                          ]
            
            );
        }

        if (array_Key_exists('file_name', $data)) {
            return $this->db->query("UPDATE documents 
                                          SET title = ?, description = ?, file_name = ?, file_path = ?,
                                          file_size = ?, file_type = ?, category_id = ?, is_public = ?,
                                          updated_at = NOW() WHERE id = ?",
                                          [
                                            $data["title"], 
                                            $data["description"],
                                            $data["file_name"],
                                            $data["file_path"],
                                            $data["file_size"],
                                            $data["file_type"],
                                            $data["category_id"], 
                                            $data["is_public"],
                                            $id
                                          ]
            
            );
        } else {
            return $this->db->query("UPDATE documents 
                                          SET title = ?, description = ?, category_id = ?,
                                          is_public = ?, updated_at = NOW() WHERE id = ?",
                                          [
                                            $data["title"], 
                                            $data["description"],
                                            $data["category_id"], 
                                            $data["is_public"],
                                            $id
                                          ]
            
            );
        }
    }

    // Methode pour une requete de supprission d'un document
    public function deleteDocument($id, $shareDocument = null) {
        $shareDocument ?
            $delete = $this->db->query("DELETE FROM document_shares WHERE id = ?", [$id]) :
            $delete = $this->db->query("DELETE FROM documents WHERE id = ?", [$id]);
        return $delete;
    }

    // Methode pour ajouter un document
    public function insertDocument($data) {
        return $this->db->query("INSERT INTO documents (id, title, description, file_name, file_path, file_size, file_type, category_id, user_id, is_public, download_count, created_at, updated_at) VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())", [
            $data["title"],
            $data["description"],
            $data["file_name"],
            $data["file_path"],
            $data["file_size"],
            $data["file_type"],
            $data["category_id"],
            $data["user_id"],
            $data["is_public"],
        ]);
    }

    // Methode pour partager un document
    public function sharingDocument($data) {
        return $this->db->query("INSERT INTO document_shares (id, document_id, shared_with_user_id, permission, shared_by, created_at) VALUES(NULL, ?, ?, ?, ?, CURRENT_TIMESTAMP())", [
            $data["document_id"],
            $data["shared_with_user_id"],
            $data["permission"],
            $data["shared_by"]
        ]);
    }

    // Methode pour une requete pour recuperer tous les documents publics
    public function getAllPublicsDocuments($limit = null, $offset = null) {
        if ($limit === null || $offset === null) {
            return $this->db->queryFetchAll("  SELECT d.*, c.name as category_name, u.name as username
                                                    FROM  documents d
                                                    LEFT JOIN categories c ON d.category_id = c.id
                                                    LEFT JOIN users u ON u.id = d.user_id
                                                    WHERE d.is_public = 1
                                                    ORDER BY d.created_at DESC");
        } 
    
        return $this->db->queryFetchAll("  SELECT d.*, c.name as category_name, u.name as username
                                                FROM  documents d
                                                LEFT JOIN categories c ON d.category_id = c.id
                                                LEFT JOIN users u ON u.id = d.user_id
                                                WHERE d.is_public = 1
                                                ORDER BY d.created_at DESC LIMIT $limit OFFSET $offset");
    }

    // Methode pour une requete qui renvoyer le nombre total de documents public
    public function getPublicDocumentCounts($search = null) {
        if ($search != null) {
            return $this->db->queryFetch(" SELECT COUNT(*) as Total 
                                                FROM documents d
                                                LEFT JOIN categories c ON d.category_id = c.id
                                                WHERE is_public = 1 AND (d.title LIKE ? OR c.name LIKE ?)
                                            ", ["%$search%", "%$search%"]);
        }
        
        return $this->db->queryFetch("SELECT COUNT(*) as Total FROM documents WHERE is_public = 1");
    }

    // Methode pour incrementer le nombre de telechargement
    public function incrementDownloadCount($id) {
        return $this->db->query("UPDATE documents SET download_count = download_count + 1 WHERE id = ?", [$id]);
    }

    // TODO : Apres faut mettre dans category
    public function getAllCategories() {
        $results = $this->db->queryFetchAll("SELECT * FROM categories");
        return $results;
    }
}
?>