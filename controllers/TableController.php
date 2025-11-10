<?php

require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Restaurant.php';

class TableController {
    
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tableId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$tableId) {
            $_SESSION['error_message'] = "ID столика не предоставлен.";
            header('Location: ?route=table/manage');
            exit;
        }

        $tableModel = new Table();
        $restaurantModel = new Restaurant();
        
        $table = $tableModel->getTableById($tableId);
        $restaurant = $restaurantModel->getRestaurantByUserId($userId);
        $error = null;
        $success = null;

        // найден ли столик и принадлежит ли он текущему владельцу
        if (!$table || !$restaurant || $table['restaurant_id'] !== $restaurant['id']) {
            $_SESSION['error_message'] = "Столик не найден или у вас нет прав на его редактирование.";
            header('Location: ?route=table/manage');
            exit;
        }
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $capacity = filter_input(INPUT_POST, 'capacite', FILTER_VALIDATE_INT);
            // ПЕРЕКЛЮЧАЕМСЯ С 'name' на 'numero'
            $numero = trim($_POST['name'] ?? ''); 
            
            if (!$capacity || $capacity <= 0) {
                $error = "Вместимость должна быть положительным числом.";
            }
            
            if (empty($numero)) { // Используем $numero
                $error = "Название/Номер столика не может быть пустым.";
            }

            if (!$error) {
                // ИСПОЛЬЗУЕМ updateTable с $numero
                if ($tableModel->updateTable($tableId, $capacity, $numero)) { 
                    $_SESSION['success_message'] = "Столик '{$numero}' успешно обновлен.";
                    header('Location: ?route=table/manage');
                    exit;
                } else {
                    $error = "Ошибка при обновлении столика в базе данных.";
                }
            }
            
            // Если ошибка, обновляем данные столика для повторного отображения формы
            $table['capacite'] = $capacity; 
            $table['numero'] = $numero; // ИСПОЛЬЗУЕМ $numero
        }
        
        // ВАЖНО: нужно загрузить поле 'numero' из БД для формы
        $table['name'] = $table['numero'] ?? '';

        
        $userRestaurant = $restaurant;
        
        require_once __DIR__ . '/../views/table/edit.php';
    }

    // ----------------------------------------------------------------------
    // УДАЛЕНИЕ (delete)
    // ----------------------------------------------------------------------
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tableId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$tableId) {
            $_SESSION['error_message'] = "ID столика не предоставлен.";
            header('Location: ?route=table/manage');
            exit;
        }

        $tableModel = new Table();
        $restaurantModel = new Restaurant();
        
        $table = $tableModel->getTableById($tableId);
        $restaurant = $restaurantModel->getRestaurantByUserId($userId);

        
        if (!$table || !$restaurant || $table['restaurant_id'] !== $restaurant['id']) {
            $_SESSION['error_message'] = "Столик не найден или у вас нет прав на его удаление.";
            header('Location: ?route=table/manage');
            exit;
        }
        
        
        if ($tableModel->deleteTable($tableId)) {
            
            $tableName = $table['name'] ?? 'ID: ' . $table['id']; 
            $_SESSION['success_message'] = "Столик '{$tableName}' успешно удален. Связанные с ним бронирования также отменены.";
        } else {
            $_SESSION['error_message'] = "Не удалось удалить столик.";
        }

        header('Location: ?route=table/manage');
        exit;
    }


    // ----------------------------------------------------------------------
    // МЕТОД 3: УПРАВЛЕНИЕ (manage) - бывшая функция index()
    // ----------------------------------------------------------------------
    public function manage() {
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        
        
        $restaurantModel = new Restaurant();
        $userRestaurant = $restaurantModel->getRestaurantByUserId($_SESSION['user_id']); 
        
        if (!$userRestaurant) {
            $_SESSION['error_message'] = "У вас нет активного ресторана для управления столиками.";
            header('Location: ?route=restaurant/list'); // Или создать ресторан: ?route=restaurant/create
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
        
        
        $tables = $tableModel->getTablesByRestaurantId($restaurantId);
        
        require_once __DIR__ . '/../views/table/manage.php';
    }
}