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
    
   public function getReservationsByUserId(int $user_id): array {
        $stmt = $this->db->prepare("
            SELECT * FROM RESERVATION WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user_id]);
        
        $reservations = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reservation = new Reservation();
            $reservation->setId($data['id'])
                        ->setUserId($data['user_id'])
                        ->setRestaurantId($data['restaurant_id'])
                        ->setDate($data['reservation_date'])
                        ->setTime($data['reservation_time'])
                        ->setGuests($data['number_of_guests']);
            $reservations[] = $reservation;
        }

        return $reservations;
    }
}