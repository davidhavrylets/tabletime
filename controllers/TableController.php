<?php

require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Restaurant.php';

class TableController {
    
    
    public function index() {
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        

        $restaurantModel = new Restaurant();
        $userRestaurant = $restaurantModel->getRestaurantByUserId($_SESSION['user_id']); 
        
        if (!$userRestaurant) {
            $_SESSION['error_message'] = "У вас нет активного ресторана для управления столиками.";
            header('Location: ?route=restaurant/list');
            exit;
        }

        $restaurantId = $userRestaurant['id'];
        $tableModel = new Table();
        $tables = $tableModel->getTablesByRestaurantId($restaurantId);

        $error = null;
        $success = null;
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $capacity = $_POST['capacite'] ?? null;
            
            if ($capacity && is_numeric($capacity) && $capacity > 0) {
                $isCreated = $tableModel->createTable((int)$capacity, $restaurantId);
                
                if ($isCreated) {
                    $_SESSION['success_message'] = "Столик вместимостью {$capacity} успешно добавлен.";
                    
                    header('Location: ?route=table/manage');
                    exit;
                } else {
                    $error = "Ошибка при добавлении столика в базу данных.";
                }
            } else {
                $error = "Пожалуйста, введите корректную вместимость столика.";
            }
        }

        
        require_once __DIR__ . '/../views/table/manage.php';
    }
}