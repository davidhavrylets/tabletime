<?php

require_once __DIR__ . '/AbstractManager.php';

class Table extends AbstractManager {
    
    
    public function getTableById(int $tableId): array|bool {
        
        $stmt = $this->db->prepare("SELECT id, capacite, numero, restaurant_id FROM resto_table WHERE id = :id");
        $stmt->execute([':id' => $tableId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function createTable(int $capacite, int $restaurantId, string $numero): bool {
        
        $sql = "INSERT INTO resto_table (capacite, restaurant_id, numero) VALUES (:capacite, :restaurantId, :numero)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':capacite', $capacite);
        $stmt->bindParam(':restaurantId', $restaurantId);
        $stmt->bindParam(':numero', $numero); 

        return $stmt->execute();
    }

    
    public function getTablesByRestaurantId(int $restaurantId): array {
        $sql = "SELECT id, capacite, numero, restaurant_id 
                FROM resto_table 
                WHERE restaurant_id = :restaurantId 
                ORDER BY numero ASC"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurantId', $restaurantId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function updateTable(int $tableId, int $capacite, string $numero): bool {
        
        $sql = "UPDATE resto_table SET capacite = :capacite, numero = :numero WHERE id = :tableId";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':capacite', $capacite);
        $stmt->bindParam(':numero', $numero); // Используем 'numero'
        $stmt->bindParam(':tableId', $tableId, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    
    public function deleteTable(int $tableId): bool {
        
        $sql = "DELETE FROM resto_table WHERE id = :tableId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tableId', $tableId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}