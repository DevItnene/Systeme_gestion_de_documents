<?php
namespace App\Core;
use PDO;
use App\Config\Database as BaseInfo;

class Database {
    private $config;
    private $pdo;
    public function __construct() {
        $database = new BaseInfo();
        $this->config = $database->database_information();
        $this->connect();
    }

    // fonction pour se connecter a la base de données
    public function connect() {
        try {
            $dsn = "mysql:host={$this->config['host']}; dbname={$this->config['dbname']}; charset={$this->config['charset']}";
            $this->pdo = new PDO($dsn, $this->config["username"], $this->config["password"]);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo"Connexion Reussie avec success";
        } catch (\PDOException $e) {
            die ("Erreur de connexion à la base de données : ". $e->getMessage());
        }
    }

    // Fonction pour une requete SELECT qui renvoie un seul element
    public function queryFetch($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die ("Erreur de la requête SQL : ". $e->getMessage(). "<br>Requête : " . $sql);
        }
    }

    // Fonction pour une requete SELECT qui renvoie plusieurs elements
    public function queryFetchAll($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die ("Erreur de la requête SQL : ". $e->getMessage(). "<br>Requête : " . $sql);
        }
    }

    // Fonction pour une requete UPDATE/DELETE
    public function query(string $sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            die ("Erreur de la requête SQL : ". $e->getMessage(). "<br>Requête : " . $sql);
        }
    }

    public function LastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
?>