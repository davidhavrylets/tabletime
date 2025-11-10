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

    public function confirm() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Вы должны войти для управления бронированиями.";
            header('Location: ?route=login');
            exit;
        }

        $reservationId = $_GET['id'] ?? null;
        if (!$reservationId) {
            $_SESSION['error_message'] = "ID бронирования не предоставлен.";
            header('Location: ?route=reservation/manage');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $reservationModel = new Reservation();
        $restaurantModel = new Restaurant();

        
        $reservationDetails = $reservationModel->getReservationById($reservationId); 
        
        if (!$reservationDetails) {
            $_SESSION['error_message'] = "Бронирование не найдено.";
            header('Location: ?route=reservation/manage');
            exit;
        }
        
        $restaurantId = $reservationDetails['restaurant_id'];

        // 2. !!! ПРОВЕРКА ПРАВ: Владеет ли пользователь этим рестораном?
        if (!$restaurantModel->isOwnerOfRestaurant($userId, $restaurantId)) {
            $_SESSION['error_message'] = "У вас нет прав для управления этим бронированием.";
            header('Location: ?route=reservation/manage');
            exit;
        }
        
        
        $isConfirmed = $reservationModel->confirmReservation($reservationId);

        if ($isConfirmed) {
            $_SESSION['success_message'] = "Бронирование #{$reservationId} успешно подтверждено!";
        } else {
            $_SESSION['error_message'] = "Не удалось подтвердить бронирование #{$reservationId}. Возможно, оно уже подтверждено или отменено.";
        }

        header('Location: ?route=reservation/manage');
        exit;
    }

    public function cancel() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Вы должны войти для управления бронированиями.";
            header('Location: ?route=login');
            exit;
        }

        $reservationId = $_GET['id'] ?? null;

        if (!$reservationId) {
            $_SESSION['error_message'] = "ID бронирования не предоставлен.";
            header('Location: ?route=reservation/manage');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $reservationModel = new Reservation();
        $restaurantModel = new Restaurant();
        
        
        $reservationDetails = $reservationModel->getReservationById($reservationId); 
        
        if (!$reservationDetails) {
            $_SESSION['error_message'] = "Бронирование не найдено.";
            header('Location: ?route=reservation/manage');
            exit;
        }

        $restaurantId = $reservationDetails['restaurant_id'];

        
        if (!$restaurantModel->isOwnerOfRestaurant($userId, $restaurantId)) {
            $_SESSION['error_message'] = "У вас нет прав для управления этим бронированием.";
            header('Location: ?route=reservation/manage');
            exit;
        }

        
        $isCancelled = $reservationModel->cancelReservation($reservationId);

        if ($isCancelled) {
            $_SESSION['success_message'] = "Бронирование #{$reservationId} успешно отменено!";
        } else {
            $_SESSION['error_message'] = "Не удалось отменить бронирование #{$reservationId}. Возможно, оно уже отменено.";
        }

        header('Location: ?route=reservation/manage');
        exit;
    }

    public function manage() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Вы должны войти для управления бронированиями.";
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $restaurantModel = new Restaurant();
        
        
        $restaurant = $restaurantModel->getRestaurantByUserId($userId);

        if (!$restaurant) {
            $error = "Для управления бронированиями сначала необходимо создать свой ресторан.";
            
        } else {
            
            $restaurantId = $restaurant['id'];
            $reservationModel = new Reservation();
            $reservations = $reservationModel->getReservationsByRestaurantId($restaurantId);
        }
        
        
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
            $reservationModel = new Reservation();
            $restaurantId = filter_input(INPUT_POST, 'restaurant_id', FILTER_VALIDATE_INT);
            $guests = filter_input(INPUT_POST, 'number_of_guests', FILTER_VALIDATE_INT);
            $date = filter_input(INPUT_POST, 'reservation_date');
            $time = filter_input(INPUT_POST, 'reservation_time');
            $remarques = filter_input(INPUT_POST, 'remarques', FILTER_SANITIZE_STRING);

            // --- БЛОК ВАЛИДАЦИИ ДАННЫХ НА СЕРВЕРЕ ---
            
           
            if (!$restaurantId || !$guests || !$date || !$time) {
                $error = "Все обязательные поля должны быть заполнены.";
            } 
            
            
            $today = new DateTime('today');
            $reservationDate = new DateTime($date);
            if ($reservationDate < $today) {
                $error = "Дата бронирования не может быть в прошлом.";
            }

            
            if (!$error) {
                list($hours, $minutes) = explode(':', $time);
                $totalMinutes = (int)$hours * 60 + (int)$minutes;
                
                
                if ($totalMinutes % 30 !== 0) {
                    $error = "Время бронирования должно быть кратно 30 минутам (например, 12:00 или 12:30).";
                }
            }
            
            // --- КОНЕЦ БЛОКА ВАЛИДАЦИИ ---

            if (!$error) {
                
                $reservationModel = new Reservation();
                $tableId = $reservationModel->findAvailableTable($restaurantId, $date, $time, $guests);

                if ($tableId) {
                    
                    if ($reservationModel->createReservation($userId, $restaurantId, $tableId, $date, $time, $guests, $remarques)) {
                        $_SESSION['success_message'] = "Ваше бронирование успешно создано! Ожидайте подтверждения от ресторана.";
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
        
        // 2. Отображение формы (GET-запрос)
        // Если ID ресторана передан в URL
        if (isset($_GET['id'])) {
            $restaurantId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $restaurant = $restaurantModel->getRestaurantById($restaurantId);
        } else {
            
            $restaurant = $restaurantModel->getRestaurantByUserId($userId);
            
            if (!$restaurant) {
              
                $error = "Для бронирования выберите ресторан из списка.";
            }
        }


        
        include 'views/reservation/create.php';
    }
}