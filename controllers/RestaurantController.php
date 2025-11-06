<?php

require_once __DIR__ . '/../models/Restaurant.php';

class RestaurantController {
    
    public function list() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) { 
            header('Location: ?route=login'); 
            exit; 
        } 

        $restaurantModel = new Restaurant();
        $restaurants = $restaurantModel->getAllRestaurants();
        
        require_once __DIR__ . '/../views/restaurant/list.php';
    }

    
    public function create() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $nom = $_POST['nom'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $description = $_POST['description'] ?? '';
            $user_id_restaurateur = $_SESSION['user_id']; 

            $restaurantModel = new Restaurant();
            $isCreated = $restaurantModel->createRestaurant($nom, $adresse, $description, $user_id_restaurateur);
            
            if ($isCreated) {
                $success = "Le restaurant '{$nom}' a été créé avec succès!";
                
            } else {
                $error = "Erreur lors de la création du restaurant.";
            }
        }
        
        
        require_once __DIR__ . '/../views/restaurant/create.php';
    } 
    
    
    public function delete() {
       
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }

        $restaurantId = $_GET['id'] ?? null;
        
        if ($restaurantId) {
            $restaurantModel = new Restaurant();
            $isDeleted = $restaurantModel->deleteRestaurant($restaurantId);

            if ($isDeleted) {
                
                $_SESSION['success_message'] = "Ресторан с ID {$restaurantId} успешно удален.";
            } else {
                
                $_SESSION['error_message'] = "Ошибка удаления ресторана. Невозможно удалить системные записи.";
            }
        }
        
        
        header('Location: ?route=restaurant/list');
        exit;
    } 
    
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }

        $id = $_GET['id'] ?? null;
        $restaurantModel = new Restaurant();
        $restaurant = $restaurantModel->getRestaurantById($id); // Получаем текущие данные

        if (!$restaurant) {
            header('Location: ?route=restaurant/list');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? $restaurant['nom'];
            $adresse = $_POST['adresse'] ?? $restaurant['adresse'];
            $description = $_POST['description'] ?? $restaurant['description'];

            $isUpdated = $restaurantModel->updateRestaurant($id, $nom, $adresse, $description);

            if ($isUpdated) {
                $_SESSION['success_message'] = "Ресторан '{$nom}' успешно обновлен.";
                header('Location: ?route=restaurant/list');
                exit;
            } else {
                $_SESSION['error_message'] = "Ошибка при обновлении ресторана.";
            }
        }
        
        // Передаем данные в форму для редактирования
        require_once __DIR__ . '/../views/restaurant/edit.php';
    }
    
} 