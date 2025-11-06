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

   
}