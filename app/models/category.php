<?php
namespace App\Models;

use App\Core\Database;
class Category {

    private $db;
    public function __construct() {
        $this->db = new Database();
    }

    // Methode pour recuperer toutes les catégories
    public function getAllCategories($limit = null, $offset = null) {
        if ($limit === null || $offset === null) {
            return $this->db->queryFetchAll("  SELECT c.*, u.name as user_name
                                                    FROM categories c
                                                    LEFT JOIN users u ON u.id = c.created_by
                                                    ORDER BY c.created_at
                                                ");
        }

        return $this->db->queryFetchAll("  SELECT c.*, u.name as user_name
                                                FROM categories c
                                                LEFT JOIN users u ON u.id = c.created_by
                                                ORDER BY c.created_at DESC LIMIT $limit OFFSET $offset
                                            ");
    }

    // Methode pour recuperer une catégorie
    public function getCategoryById($id) {
        return $this->db->queryFetchAll("  SELECT c.*, u.name as user_name
                                                FROM categories c
                                                LEFT JOIN users u ON u.id = c.created_by
                                                WHERE c.id = ?
                                            ", [$id]);
    }

    // Methode pour rechercher un ou plusieurs catégories
    public function searchCategories($search, $limit = null, $offset = null) {
        if ($limit === null || $offset === null) {
            return $this->db->queryFetchAll("  SELECT c.*, u.name as user_name
                                                    FROM categories c
                                                    LEFT JOIN users u ON u.id = c.created_by
                                                    ORDER BY c.created_at
                                                ");
        }
        
        return $this->db->queryFetchAll("  SELECT c.*, u.name as user_name
                                                FROM categories c
                                                LEFT JOIN users u ON u.id = c.created_by
                                                WHERE (u.name LIKE ? OR c.name LIKE ?)
                                                ORDER BY c.created_at DESC LIMIT $limit OFFSET $offset
                                            ", ["%$search%", "%$search%"]);

    }

    // Methode pour une requete qui renvoyer le nombre total des catégories
    public function getCategoryCounts($search = null, $user = null) {
        if ($search != null) {
            return $this->db->queryFetch(" SELECT COUNT(*) as Total 
                                                FROM categories c
                                                LEFT JOIN users u ON u.id = c.created_by
                                                WHERE (u.name LIKE ? OR c.name LIKE ?)
                                            ", ["%$search%", "%$search%"]);

        } else if ($user != null) {
            return $this->db->queryFetch(" SELECT COUNT(*) as Total 
                                                FROM categories c
                                                LEFT JOIN users u ON u.id = c.created_by
                                                WHERE c.created_by = ? AND (u.name LIKE ? OR c.name LIKE ?)
                                            ", [$_SESSION["user_id"], "%$search%", "%$search%"]);
        }

        return $this->db->queryFetch("SELECT COUNT(*) as Total FROM categories");

    }

    // Methode pour une requete de supprission d'une catégorie
    public function deleteCategory($id) {
        return $this->db->query("DELETE FROM categories WHERE id = ?", [$id]);
    }

    // Methode pour une requete de update d'une catégorie
    public function updateCategory($id, $data) {
        return $this->db->query("  UPDATE categories 
                                        SET name = ?, description = ?, updated_at = NOW()
                                        WHERE id = ?",
                                        [
                                            $data["name"], 
                                            $data["description"],
                                            $id
                                        ]
        
                                );
    }

    public function insertCategory($data) {
        return $this->db->query("  INSERT INTO categories (id, name, description, created_by, created_at, updated_at)
                                        VALUES (NULL, ?, ?, ?, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())",
                                        [
                                            $data["name"], 
                                            $data["description"],
                                            $data["user_id"]
                                        ]
        
                                );
    }
}
?>