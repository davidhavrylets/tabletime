<?php

require_once __DIR__ . '/../config/Database.php';

class Table {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

    public function getTableById($tableId) {
        $sql = "SELECT * FROM resto_table WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $tableId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function updateTable($tableId, $capacite, $numero) { 
        $sql = "UPDATE resto_table SET capacite = :capacite, numero = :numero WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $tableId, PDO::PARAM_INT);
        $stmt->bindParam(':capacite', $capacite, PDO::PARAM_INT);
        $stmt->bindParam(':numero', $numero, PDO::PARAM_STR); 
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            
            return false;
        }
    }

   
    public function deleteTable($tableId) {
        $sql = "DELETE FROM resto_table WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $tableId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function createTable($capacity, $restaurantId) {
        $sql = "INSERT INTO resto_table (capacite, restaurant_id) 
                VALUES (:capacity, :restaurantId)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':restaurantId', $restaurantId);
        
        return $stmt->execute();
    }

    
    public function getTablesByRestaurantId($restaurantId) {
        $sql = "SELECT * FROM resto_table WHERE restaurant_id = :restaurantId ORDER BY capacite ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurantId', $restaurantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}