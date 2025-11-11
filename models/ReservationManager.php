<?php
// models/ReservationManager.php
require_once __DIR__ . '/AbstractManager.php';

require_once __DIR__ . '/Reservation.php'; 

class ReservationManager extends AbstractManager {
    

    public function createReservation($userId, $restaurantId, $tableId, $date, $time, $guests, $remarques) {
        
        
        $sql = "INSERT INTO reservation (user_id, restaurant_id, table_id, reservation_date, reservation_time, number_of_guests, remarques, statut) 
                VALUES (:user_id, :restaurant_id, :table_id, :reservation_date, :reservation_time, :number_of_guests, :remarques, 'en attente')";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':restaurant_id', $restaurantId);
        $stmt->bindParam(':table_id', $tableId);
        $stmt->bindParam(':reservation_date', $date);
        $stmt->bindParam(':reservation_time', $time);
        $stmt->bindParam(':number_of_guests', $guests);
        $stmt->bindParam(':remarques', $remarques);

        return $stmt->execute();
    }

    public function getReservationsByUserId($userId) {
        $sql = "SELECT r.*, rest.nom AS restaurant_nom 
                FROM reservation r
                JOIN restaurant rest ON r.restaurant_id = rest.id
                WHERE r.user_id = :user_id
                ORDER BY r.reservation_date DESC, r.reservation_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservationsByRestaurantId($restaurantId) {
        $sql = "SELECT r.*, u.nom AS user_nom, u.prenom AS user_prenom, t.numero AS table_numero
                FROM reservation r
                JOIN utilisateur u ON r.user_id = u.id
                JOIN resto_table t ON r.table_id = t.id
                WHERE r.restaurant_id = :restaurant_id
                ORDER BY r.reservation_date ASC, r.reservation_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurant_id', $restaurantId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservationById($reservationId) {
        $sql = "SELECT * FROM reservation WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $reservationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function confirmReservation($reservationId) {
        
        $sql = "UPDATE reservation SET statut = 'confirmée' WHERE id = :id AND statut = 'en attente'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $reservationId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cancelReservation($reservationId) {
        
        $sql = "UPDATE reservation SET statut = 'annulée' WHERE id = :id AND statut != 'annulée'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $reservationId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
   
    public function findAvailableTable($restaurantId, $date, $time, $guests) {
        $sql = "
            SELECT t.id
            FROM resto_table t
            LEFT JOIN reservation r ON t.id = r.table_id
                AND r.reservation_date = :reservation_date
                AND r.reservation_time = :reservation_time
                AND r.statut != 'annulée' -- Игнорируем отмененные
            WHERE 
                t.restaurant_id = :restaurant_id
                AND t.capacite >= :guests
                AND r.id IS NULL -- Ключевое условие: ищем, где НЕТ совпадения (столик свободен)
            LIMIT 1
        ";

       
        $stmt = $this->db->prepare($sql); 
        
        $stmt->bindParam(':restaurant_id', $restaurantId, PDO::PARAM_INT);
        $stmt->bindParam(':reservation_date', $date);
        $stmt->bindParam(':reservation_time', $time);
        $stmt->bindParam(':guests', $guests, PDO::PARAM_INT);
        
        $stmt->execute(); 
        
        $table = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($table) {
            return $table['id']; 
        } else {
            return false; 
        }
    }
    
   
    public function getCommissionRate($restaurantId) {
        return 0.10; // Пример
    }

   
}