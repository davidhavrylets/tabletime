<?php

require_once __DIR__ . '/../config/Database.php';

class Restaurant {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

  
    public function createRestaurant($nom, $adresse, $description, $utilisateurId) {
        
        
        $sql = "INSERT INTO restaurant (nom, adresse, description, UTILISATEUR_id) 
                VALUES (:nom, :adresse, :description, :utilisateurId)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':description', $description);
        
        $stmt->bindParam(':utilisateurId', $utilisateurId); 

        return $stmt->execute();
    }

 
    public function getAllRestaurants() {
        $sql = "SELECT * FROM restaurant";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getRestaurantById($id) {
        $sql = "SELECT * FROM restaurant WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRestaurant($id, $nom, $adresse, $description) {
        $sql = "UPDATE restaurant 
                SET nom = :nom, adresse = :adresse, description = :description 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
    
public function deleteRestaurant($id) {
        
        if ($id == 1) {
            return false;
        }
        $sql = "DELETE FROM restaurant WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}