<?php

require_once __DIR__ . '/../config/Database.php';

class Reservation {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

    /**
     * Поиск подходящего столика для бронирования.
     * Возвращает ID свободного столика, соответствующего вместимости.
     */
    public function findAvailableTable($restaurantId, $date, $time, $guests) {
        
        
        $sql = "SELECT id FROM resto_table 
                WHERE restaurant_id = :restaurantId AND capacite >= :guests";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurantId', $restaurantId);
        $stmt->bindParam(':guests', $guests);
        $stmt->execute();
        $suitableTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($suitableTables)) {
            return null; 
        }

        $tableIds = implode(',', $suitableTables);

       
        $sql = "SELECT table_id FROM reservation 
                WHERE restaurant_id = :restaurantId 
                AND reservation_date = :date 
                AND reservation_time = :time
                AND table_id IN ({$tableIds})
                AND statut IN ('confirmée', 'en attente')"; 

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurantId', $restaurantId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->execute();
        $bookedTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

       
        $availableTables = array_diff($suitableTables, $bookedTables);

        return empty($availableTables) ? null : reset($availableTables); 
    }

   
    public function createReservation($userId, $restaurantId, $tableId, $date, $time, $guests, $remarques) {
        $sql = "INSERT INTO reservation (user_id, restaurant_id, table_id, reservation_date, reservation_time, number_of_guests, remarques, statut) 
                VALUES (:userId, :restaurantId, :tableId, :date, :time, :guests, :remarques, 'en attente')";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':restaurantId', $restaurantId);
        $stmt->bindParam(':tableId', $tableId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':guests', $guests);
        $stmt->bindParam(':remarques', $remarques);
        
        return $stmt->execute();
    }
}