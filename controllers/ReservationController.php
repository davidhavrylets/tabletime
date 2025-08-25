<?php

require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../models/ReservationManager.php';

class ReservationController {

    public function create() {
        // Проверяем, авторизован ли пользователь)
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            die();
        }
        
        $reservationManager = new ReservationManager();

        // Обработка данных из формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $reservation = new Reservation();
                $reservation->setUserId($_SESSION['user_id'])
                            ->setRestaurantId($_POST['restaurant_id'] ?? 1) // Заглушка
                            ->setDate($_POST['date'])
                            ->setTime($_POST['time'])
                            ->setGuests($_POST['guests']);
    
                if ($reservationManager->createReservation($reservation)) {
                    echo "Ваш столик успешно забронирован!";
                } else {
                    echo "Произошла ошибка при бронировании.";
                }
            } catch (InvalidArgumentException $e) {
                echo "Ошибка: " . $e->getMessage();
            }

        } else {
            // Если GET-запрос, показываем форму бронирования
            require_once __DIR__ . '/../views/reservation/create.php';
        }
    }
}