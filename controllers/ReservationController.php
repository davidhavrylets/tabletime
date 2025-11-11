<?php
// controllers/ReservationController.php

require_once __DIR__ . '/../models/Reservation.php'; 
require_once __DIR__ . '/../models/Restaurant.php'; 


require_once __DIR__ . '/../models/ReservationManager.php'; 


class ReservationController {
    
    /**
     * 1. Список бронирований (Для КЛИЕНТА)
     */
    public function list() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Вы должны войти, чтобы просмотреть свои бронирования.";
            header('Location: ?route=login');
            exit;
        }

        
        $reservationModel = new ReservationManager(); 
        $userId = $_SESSION['user_id'];
        $reservations = $reservationModel->getReservationsByUserId($userId);
        
        require_once __DIR__ . '/../views/reservation/list.php';
    }

    
    public function confirm() {
        
        if ($_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=home');
            exit;
        }

        $reservationId = $_GET['id'] ?? null;
        

        $userId = $_SESSION['user_id'];
        
        
        $reservationModel = new ReservationManager(); 
        $restaurantModel = new Restaurant();

        $reservationDetails = $reservationModel->getReservationById($reservationId); 
        if (!$reservationDetails || !$restaurantModel->isOwnerOfRestaurant($userId, $reservationDetails['restaurant_id'])) {
            $_SESSION['error_message'] = "У вас нет прав для управления этим бронированием.";
            header('Location: ?route=reservation/manage');
            exit;
        }
        
        $isConfirmed = $reservationModel->confirmReservation($reservationId);
        // ...
        header('Location: ?route=reservation/manage');
        exit;
    }

    /**
     * 3. Отмена брони (Только ВЛАДЕЛЕЦ)
     */
    public function cancel() {
        
        if ($_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=home');
            exit;
        }

        $reservationId = $_GET['id'] ?? null;
       
        
        $userId = $_SESSION['user_id'];
        
        $reservationModel = new ReservationManager(); 
        $restaurantModel = new Restaurant();
        
        $reservationDetails = $reservationModel->getReservationById($reservationId); 
        if (!$reservationDetails || !$restaurantModel->isOwnerOfRestaurant($userId, $reservationDetails['restaurant_id'])) {
             
             header('Location: ?route=reservation/manage');
             exit;
        }
        
        $isCancelled = $reservationModel->cancelReservation($reservationId);
        
        header('Location: ?route=reservation/manage');
        exit;
    }

    
    public function manage() {
        
        if ($_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=home');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $restaurantModel = new Restaurant();
        
        $restaurant = $restaurantModel->getRestaurantByUserId($userId); 

        if (!$restaurant) {
            // ...
            header('Location: ?route=restaurant/create'); 
            exit;
        }
        
        $restaurantId = $restaurant['id'];
        
        $reservationModel = new ReservationManager(); 
        $reservations = $reservationModel->getReservationsByRestaurantId($restaurantId);
        $userRestaurant = $restaurant; 

        require_once __DIR__ . '/../views/reservation/manage.php';
    }


    
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $error = null;
        $restaurant = null;
        $restaurantModel = new Restaurant();

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $reservationModel = new ReservationManager(); 
            
            $restaurantId = filter_input(INPUT_POST, 'restaurant_id', FILTER_VALIDATE_INT);
            $guests = filter_input(INPUT_POST, 'number_of_guests', FILTER_VALIDATE_INT);
            $date = filter_input(INPUT_POST, 'reservation_date'); // 'Y-m-d'
            $time = filter_input(INPUT_POST, 'reservation_time'); // 'H:i'
            
            $remarques = trim($_POST['remarques'] ?? ''); 

            // --- БЛОК ВАЛИДАЦИИ ---
            
            if (!$restaurantId || !$guests || !$date || !$time) {
                $error = "Все обязательные поля должны быть заполнены.";
            }

            if ($guests <= 0) {
                $error = "Количество гостей должно быть положительным.";
            }
            
            if (!$error) { 
                $today = new DateTime('today');
                $reservationDate = new DateTime($date);
                if ($reservationDate < $today) {
                    $error = "Дата бронирования не может быть в прошлом.";
                }
            }

            if (!$error) {
                list($hours, $minutes) = explode(':', $time);
                $totalMinutes = (int)$hours * 60 + (int)$minutes;
                
                
                if ($totalMinutes % 30 !== 0) { 
                    $error = "Время бронирования должно быть кратно 30 минутам (например, 12:00 или 12:30).";
                }
            }
            

            if (!$error) {
                
                $tableId = $reservationModel->findAvailableTable($restaurantId, $date, $time, $guests);

                if ($tableId) {
                    if ($reservationModel->createReservation($userId, $restaurantId, $tableId, $date, $time, $guests, $remarques)) {
                        $_SESSION['success_message'] = "Ваше бронирование успешно создано! Ожидайте подтверждения.";
                        header('Location: ?route=reservation/list'); 
                        exit;
                    } else {
                        $error = "Ошибка при записи бронирования в базу данных.";
                    }
                } else {
                    $error = "К сожалению, на выбранное время и количество гостей нет свободных столиков.";
                }
            }
            
            
            if ($error) {
                $_SESSION['error_message'] = $error;
                $_SESSION['form_data'] = $_POST; 
                header('Location: ' . $_SERVER['HTTP_REFERER']); 
                exit;
            }
        } 
        
        // --- Логика GET-запроса (она у вас правильная) ---
       if (isset($_GET['restaurant_id'])) {
    
    // ВАЖНОЕ ИЗМЕНЕНИЕ ЗДЕСЬ: ищем 'restaurant_id' вместо 'id'
    $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);
    
    // Получаем данные ресторана
    $restaurant = $restaurantModel->getRestaurantById($restaurantId);
    
    if (!$restaurant) {
        $_SESSION['error_message'] = "Ресторан не найден.";
        header('Location: ?route=home'); 
        exit;
    }
    
// Если GET-параметр вообще не был передан, выводим ошибку
} else {
    $_SESSION['error_message'] = "Ресторан не выбран.";
    header('Location: ?route=home'); 
    exit;
}

        require_once __DIR__ . '/../views/reservation/create.php';
    }
}