<?php

require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../models/Restaurant.php';

class ReservationController {
    



public function list() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Вы должны войти, чтобы просмотреть свои бронирования.";
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $reservationModel = new Reservation();
        
        
        $reservations = $reservationModel->getReservationsByUserId($userId);
        
        
        require_once __DIR__ . '/../views/reservation/list.php';
    }

    public function create() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Вы должны войти, чтобы забронировать столик.";
            header('Location: ?route=login');
            exit;
        }

        $error = null;
        $restaurant = null;

     
        $restaurantId = $_POST['restaurant_id'] ?? $_GET['restaurant_id'] ?? null;
        
        
        if (!$restaurantId) {
            $error = "Идентификатор ресторана не предоставлен.";
        }
        
        
        if ($restaurantId) {
            $restaurantModel = new Restaurant();
            $restaurant = $restaurantModel->getRestaurantById($restaurantId);
        }

        if (!$restaurant) {
             
            $error = "Ресторан не найден."; 
        }
        
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $restaurant) { 
            
            $userId = $_SESSION['user_id'];
            
            $date = $_POST['reservation_date'] ?? null;
            $time = $_POST['reservation_time'] ?? null;
            $guests = $_POST['number_of_guests'] ?? null;
            $remarques = $_POST['remarques'] ?? null;

            $reservationModel = new Reservation();

            
            $tableId = $reservationModel->findAvailableTable($restaurantId, $date, $time, $guests);

            if ($tableId) {
                
                $isCreated = $reservationModel->createReservation($userId, $restaurantId, $tableId, $date, $time, $guests, $remarques);

                if ($isCreated) {
                    $_SESSION['success_message'] = "Ваш столик на {$guests} мест в ресторане {$restaurant['nom']} успешно забронирован на {$date} в {$time}!";
                    header('Location: ?route=home');
                    exit;
                } else {
                    $error = "Произошла ошибка при сохранении бронирования.";
                }
            } else {
                $error = "К сожалению, мы не нашли свободного столика на указанное время и количество гостей. Попробуйте изменить параметры.";
            }
        }
        
        
        require_once __DIR__ . '/../views/reservation/create.php';
    }
}