<?php

require_once __DIR__ . '/../config/Database.php';

class Table {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

    /**
     * Метод для создания нового столика в ресторане
     * @param int $capacity Вместимость столика
     * @param int $restaurantId ID ресторана, которому принадлежит столик
     * @return bool Успех/неуспех операции
     */
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