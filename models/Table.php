<?php

require_once __DIR__ . '/../config/Database.php';

class Table {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }
    
    // Получение столика по ID (для edit/delete)
    public function getTableById(int $tableId): array|bool {
        // 💥 ИСПРАВЛЕНО: Имя таблицы на 'resto_table' и столбец на 'numero'
        $stmt = $this->db->prepare("SELECT id, capacite, numero, restaurant_id FROM resto_table WHERE id = :id");
        $stmt->execute([':id' => $tableId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Создание столика (используется в manage)
    public function createTable(int $capacite, int $restaurantId, string $numero): bool {
        // 💥 ИСПРАВЛЕНО: Имя таблицы и столбец
        $sql = "INSERT INTO resto_table (capacite, restaurant_id, numero) VALUES (:capacite, :restaurantId, :numero)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':capacite', $capacite);
        $stmt->bindParam(':restaurantId', $restaurantId);
        $stmt->bindParam(':numero', $numero); // Используем 'numero'

        return $stmt->execute();
    }

    // Получение списка столиков (используется в manage)
    public function getTablesByRestaurantId(int $restaurantId): array {
        // 💥 ИСПРАВЛЕНО: Имя таблицы и столбец
        $sql = "SELECT id, capacite, numero, restaurant_id 
                FROM resto_table 
                WHERE restaurant_id = :restaurantId 
                ORDER BY numero ASC"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurantId', $restaurantId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Обновление столика (используется в edit)
    public function updateTable(int $tableId, int $capacite, string $numero): bool {
        // 💥 ИСПРАВЛЕНО: Имя таблицы и столбец
        $sql = "UPDATE resto_table SET capacite = :capacite, numero = :numero WHERE id = :tableId";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':capacite', $capacite);
        $stmt->bindParam(':numero', $numero); // Используем 'numero'
        $stmt->bindParam(':tableId', $tableId, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    // Удаление столика (используется в delete)
    public function deleteTable(int $tableId): bool {
        // 💥 ИСПРАВЛЕНО: Имя таблицы
        $sql = "DELETE FROM resto_table WHERE id = :tableId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tableId', $tableId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}