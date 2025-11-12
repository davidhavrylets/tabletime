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
            $_SESSION['error_message'] = "Vous devez vous connecter pour consulter vos réservations.";
            header('Location: ?route=login');
            exit;
        }

        
        $reservationModel = new ReservationManager(); 
        $userId = $_SESSION['user_id'];
        $reservations = $reservationModel->getReservationsByUserId($userId);
        
        require_once __DIR__ . '/../views/reservation/list.php';
    }

    
public function edit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour éditer les réservations.";
            header('Location: ?route=home');
            exit;
        }

        $reservationId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $userId = $_SESSION['user_id'];
        $error = null;

        $reservationModel = new ReservationManager();
        $reservation = $reservationModel->getReservationById($reservationId);

       
        if (!$reservation || (int)$reservation['user_id'] !== (int)$userId) {
            $_SESSION['error_message'] = "La réservation n'a pas été trouvée ou appartient à un autre utilisateur.";
            header('Location: ?route=reservation/list');
            exit;
        }

        
        if ($reservation['statut'] !== 'en attente' && $reservation['statut'] !== 'confirmée') {
            $_SESSION['error_message'] = "Cette réservation ne peut pas être éditée, car son statut est : " . htmlspecialchars($reservation['statut']);
            header('Location: ?route=reservation/list');
            exit;
        }
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            
            $guests = filter_input(INPUT_POST, 'number_of_guests', FILTER_VALIDATE_INT);
            $date = filter_input(INPUT_POST, 'reservation_date');
            $time = filter_input(INPUT_POST, 'reservation_time');
            $remarques = trim($_POST['remarques'] ?? '');

           
            if (!$guests || !$date || !$time || $guests <= 0) {
                $error = "Tous les champs obligatoires doivent être remplis correctement.";
            }

            if (!$error) {
                
                $restaurantId = $reservation['restaurant_id'];
                
                
                $tableId = $reservationModel->findAvailableTableForUpdate(
                    $restaurantId, 
                    $date, 
                    $time, 
                    $guests, 
                    $reservationId 
                );

                if ($tableId) {
                    
                    if ($reservationModel->updateReservation($reservationId, $tableId, $date, $time, $guests, $remarques)) {
                        $_SESSION['success_message'] = "Votre réservation a été mise à jour avec succès !";
                        header('Location: ?route=reservation/list');
                        exit;
                    } else {
                        $error = "Erreur lors de la mise à jour de la réservation dans la base de données.";
                    }
                } else {
                    $error = "Malheureusement, il n'y a pas de tables disponibles pour l'heure et le nombre de convives sélectionnés.";
                }
            }

           
            if ($error) {
               
                $reservation['number_of_guests'] = $guests;
                $reservation['reservation_date'] = $date;
                $reservation['reservation_time'] = $time;
                $reservation['remarques'] = $remarques;
                $_SESSION['error_message'] = $error;
            }
        }
        
        
        $restaurantModel = new Restaurant();
        $restaurant = $restaurantModel->getRestaurantById($reservation['restaurant_id']);
        
        require_once __DIR__ . '/../views/reservation/edit_form.php';
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
            $_SESSION['error_message'] = "Vous n'avez pas les droits pour gérer cette réservation.";
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
                $error = "Tous les champs obligatoires doivent être remplis.";
            }

            if ($guests <= 0) {
                $error = "Le nombre de convives doit être positif.";
            }
            
            if (!$error) { 
                $today = new DateTime('today');
                $reservationDate = new DateTime($date);
                if ($reservationDate < $today) {
                    $error = "La date de réservation ne peut pas être passée.";
                }
            }

            if (!$error) {
                list($hours, $minutes) = explode(':', $time);
                $totalMinutes = (int)$hours * 60 + (int)$minutes;
                

                if ($totalMinutes % 30 !== 0) {
                    $error = "L'heure de réservation doit être un multiple de 30 minutes (par exemple, 12:00 ou 12:30).";
                }
            }
            

            if (!$error) {
                
                $tableId = $reservationModel->findAvailableTable($restaurantId, $date, $time, $guests);

                if ($tableId) {
                    if ($reservationModel->createReservation($userId, $restaurantId, $tableId, $date, $time, $guests, $remarques)) {
                        $_SESSION['success_message'] = "Votre réservation a été créée avec succès ! Veuillez attendre la confirmation.";
                        header('Location: ?route=reservation/list'); 
                        exit;
                    } else {
                        $error = "Erreur lors de l'enregistrement de la réservation dans la base de données.";
                    }
                } else {
                    $error = "Malheureusement, il n'y a pas de tables disponibles pour l'heure et le nombre de convives sélectionnés.";
                }
            }
            
            
            if ($error) {
                $_SESSION['error_message'] = $error;
                $_SESSION['form_data'] = $_POST; 
                header('Location: ' . $_SERVER['HTTP_REFERER']); 
                exit;
            }
        } 
        
        
       if (isset($_GET['restaurant_id'])) {
    
    
    $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);
    
    
    $restaurant = $restaurantModel->getRestaurantById($restaurantId);
    
    if (!$restaurant) {
        $_SESSION['error_message'] = "Le restaurant n'a pas été sélectionné.";
        header('Location: ?route=home'); 
        exit;
    }
    
// Если GET-параметр вообще не был передан, выводим ошибку
} else {
    $_SESSION['error_message'] = "Le restaurant n'a pas été sélectionné.";
    header('Location: ?route=home'); 
    exit;
}

        require_once __DIR__ . '/../views/reservation/create.php';
    }
}

