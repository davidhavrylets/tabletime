<?php

require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Restaurant.php';

class TableController {
    
    /**
     * 1. РЕДАКТИРОВАНИЕ СТОЛИКА
     */
    public function edit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tableId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);
        $redirectUrl = $restaurantId ? '?route=table/manage&restaurant_id=' . $restaurantId : '?route=restaurant/list';

        if (!$tableId) {
            $_SESSION['error_message'] = "ID столика не предоставлен.";
            header('Location: ' . $redirectUrl);
            exit;
        }

        $tableModel = new Table();
        $restaurantModel = new Restaurant();
        
        $table = $tableModel->getTableById($tableId);
        $restaurant = $restaurantModel->getRestaurantById($table['restaurant_id']);

        if (!$table || !$restaurant || $restaurant['utilisateur_id'] != $userId) {
            $_SESSION['error_message'] = "Столик не найден или у вас нет прав на его редактирование.";
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $capacity = filter_input(INPUT_POST, 'capacite', FILTER_VALIDATE_INT);
            // 💥 ИСПРАВЛЕНО: Используем 'numero'
            $numero = trim($_POST['numero'] ?? ''); 
            
            if (!$capacity || $capacity <= 0 || empty($numero)) {
                $error = "Пожалуйста, введите корректный номер/имя и вместимость.";
            } else {
                // 💥 ИСПРАВЛЕНО: Передаем $numero
                if ($tableModel->updateTable($tableId, $capacity, $numero)) { 
                    $_SESSION['success_message'] = "Столик '{$numero}' успешно обновлен.";
                    header('Location: ' . $redirectUrl);
                    exit;
                } else {
                    $error = "Ошибка при обновлении столика в базе данных.";
                }
            }
            
            $table['capacite'] = $capacity; 
            // 💥 ИСПРАВЛЕНО: Используем 'numero'
            $table['numero'] = $numero;
        }

        $userRestaurant = $restaurant; 
        require_once __DIR__ . '/../views/table/edit.php';
    }


    /**
     * 2. УДАЛЕНИЕ СТОЛИКА
     */
    public function delete() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tableId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);
        $redirectUrl = $restaurantId ? '?route=table/manage&restaurant_id=' . $restaurantId : '?route=restaurant/list';

        if (!$tableId) {
            $_SESSION['error_message'] = "ID столика не предоставлен.";
            header('Location: ' . $redirectUrl);
            exit;
        }

        $tableModel = new Table();
        $restaurantModel = new Restaurant();
        
        $table = $tableModel->getTableById($tableId);
        $restaurant = $restaurantModel->getRestaurantById($table['restaurant_id']);

        if (!$table || !$restaurant || $restaurant['utilisateur_id'] != $userId) {
            $_SESSION['error_message'] = "Столик не найден или у вас нет прав на его удаление.";
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        if ($tableModel->deleteTable($tableId)) {
            // 💥 ИСПРАВЛЕНО: Используем 'numero'
            $tableName = $table['numero'] ?? 'ID: ' . $table['id']; 
            $_SESSION['success_message'] = "Столик '{$tableName}' успешно удален.";
        } else {
            $_SESSION['error_message'] = "Не удалось удалить столик.";
        }

        header('Location: ' . $redirectUrl);
        exit;
    }


    /**
     * 3. УПРАВЛЕНИЕ (ГЛАВНАЯ СТРАНИЦА АДМИНКИ СТОЛИКОВ)
     */
    public function manage() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=login');
            exit;
        }
        
        $ownerId = $_SESSION['user_id'];
        $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);

        if (!$restaurantId) {
             $_SESSION['error_message'] = "Не указан ID ресторана.";
             header('Location: ?route=restaurant/list'); 
             exit;
        }

        $restaurantModel = new Restaurant();
        $userRestaurant = $restaurantModel->getRestaurantById($restaurantId); 

        if (!$userRestaurant || $userRestaurant['utilisateur_id'] != $ownerId) {
            $_SESSION['error_message'] = "Ресторан не найден или у вас нет прав доступа. (ID ресторана: {$restaurantId}, ID владельца: {$ownerId})";
            header('Location: ?route=restaurant/list'); 
            exit;
        }

        $tableModel = new Table(); 
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $capacity = $_POST['capacite'] ?? null;
            // 💥 ИСПРАВЛЕНО: Используем 'numero'
            $numero = trim($_POST['numero'] ?? ''); 

            if ($capacity && is_numeric($capacity) && $capacity > 0 && !empty($numero)) {
                
                // 💥 ИСПРАВЛЕНО: Передаем $numero
                $isCreated = $tableModel->createTable((int)$capacity, $restaurantId, $numero); 
                
                if ($isCreated) {
                    $_SESSION['success_message'] = "Столик '{$numero}' (вместимость {$capacity}) успешно добавлен.";
                    header('Location: ?route=table/manage&restaurant_id=' . $restaurantId);
                    exit;
                } else {
                    $error = "Ошибка при добавлении столика в базу данных.";
                }
            } else {
                $error = "Пожалуйста, введите корректный номер/имя и вместимость столика.";
            }
        }
        
        $tables = $tableModel->getTablesByRestaurantId($restaurantId);
        
        require_once __DIR__ . '/../views/table/manage.php';
    }
}