<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Reservation.php';

class ReservationManager {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

    public function createReservation(Reservation $reservation): bool {
        
        
        $stmt = $this->db->prepare("
            INSERT INTO RESERVATION (user_id, restaurant_id, reservation_date, reservation_time, number_of_guests)
            VALUES (:user_id, :restaurant_id, :date, :time, :guests)
        ");
        
        $params = [
            ':user_id' => $reservation->getUserId(),
            ':restaurant_id' => $reservation->getRestaurantId(),
            ':date' => $reservation->getDate(),
            ':time' => $reservation->getTime(),
            ':guests' => $reservation->getGuests()
        ];

        return $stmt->execute($params);
    }
}