<?php

require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Restaurant.php';

class TableController {
    
    /**
     * 1. РЕДАКТИРОВАНИЕ СТОЛИКА
     */
    public function edit() {
        // --- ПРОВЕРКА АВТОРИЗАЦИИ И РОЛИ ---
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа к этой странице.";
            header('Location: ?route=home'); // Клиентов отправляем на главную
            exit;
        }
        // --- КОНЕЦ ПРОВЕРКИ ---

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
        // Получаем ресторан, чтобы проверить, что столик принадлежит владельцу
        $restaurant = $restaurantModel->getRestaurantByUserId($userId);
        $error = null;

        // Проверка: найден ли столик и принадлежит ли он текущему владельцу
        if (!$table || !$restaurant || $table['restaurant_id'] !== $restaurant['id']) {
            $_SESSION['error_message'] = "Столик не найден или у вас нет прав на его редактирование.";
            header('Location: ?route=table/manage');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $capacity = filter_input(INPUT_POST, 'capacite', FILTER_VALIDATE_INT);
            $name = trim($_POST['name'] ?? ''); // (Или 'numero', если вы используете его)
            
            if (!$capacity || $capacity <= 0) {
                $error = "Вместимость должна быть положительным числом.";
            }
            if (empty($name)) {
                $error = "Название столика не может быть пустым.";
            }

            if (!$error) {
                if ($tableModel->updateTable($tableId, $capacity, $name)) { 
                    $_SESSION['success_message'] = "Столик '{$name}' успешно обновлен.";
                    header('Location: ?route=table/manage');
                    exit;
                } else {
                    $error = "Ошибка при обновлении столика в базе данных.";
                }
            }
            
            $table['capacite'] = $capacity; 
            $table['name'] = $name;
        }

        $userRestaurant = $restaurant;
        
        require_once __DIR__ . '/../views/table/edit.php';
    }


    /**
     * 2. УДАЛЕНИЕ СТОЛИКА
     */
    public function delete() {
        // --- ПРОВЕРКА АВТОРИЗАЦИИ И РОЛИ ---
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа к этой странице.";
            header('Location: ?route=home');
            exit;
        }
        // --- КОНЕЦ ПРОВЕРКИ ---

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

        // Проверка: найден ли столик и принадлежит ли он текущему владельцу
        if (!$table || !$restaurant || $table['restaurant_id'] !== $restaurant['id']) {
            $_SESSION['error_message'] = "Столик не найден или у вас нет прав на его удаление.";
            header('Location: ?route=table/manage');
            exit;
        }
        
        if ($tableModel->deleteTable($tableId)) {
            $tableName = $table['name'] ?? 'ID: ' . $table['id']; 
            $_SESSION['success_message'] = "Столик '{$tableName}' успешно удален.";
        } else {
            $_SESSION['error_message'] = "Не удалось удалить столик.";
        }

        header('Location: ?route=table/manage');
        exit;
    }


    /**
     * 3. УПРАВЛЕНИЕ (ГЛАВНАЯ СТРАНИЦА АДМИНКИ СТОЛИКОВ)
     */
   public function manage() {
        // --- ПРОВЕРКА АВТОРИЗАЦИИ И РОЛИ ---
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа к этой странице.";
            header('Location: ?route=home');
            exit;
        }
        // --- КОНЕЦ ПРОВЕРКИ ---
        
        $restaurantModel = new Restaurant();
        $userRestaurant = $restaurantModel->getRestaurantByUserId($_SESSION['user_id']); 
        
        if (!$userRestaurant) {
            $_SESSION['error_message'] = "У вас нет активного ресторана. Пожалуйста, сначала создайте его.";
            header('Location: ?route=restaurant/create'); 
            exit;
        }

        $restaurantId = $userRestaurant['id'];
        
        // ИСПРАВЛЕНИЕ: Используем 'new Table()', а не 'TableManager()'
        $tableModel = new Table(); 
        $error = null;
        
        // Обработка POST-запроса (Создание нового столика)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // ИСПРАВЛЕНИЕ: Мы читаем 'name' и 'capacite' из формы
            $capacity = $_POST['capacite'] ?? null;
            $name = trim($_POST['name'] ?? ''); 

            if ($capacity && is_numeric($capacity) && $capacity > 0 && !empty($name)) {
                
                // ИСПРАВЛЕНИЕ: Передаем ВСЕ ТРИ аргумента в Модель
                $isCreated = $tableModel->createTable((int)$capacity, $restaurantId, $name); 
                
                if ($isCreated) {
                    $_SESSION['success_message'] = "Столик '{$name}' (вместимость {$capacity}) успешно добавлен.";
                    header('Location: ?route=table/manage');
                    exit;
                } else {
                    $error = "Ошибка при добавлении столика в базу данных.";
                }
            } else {
                $error = "Пожалуйста, введите корректное имя и вместимость столика.";
            }
        }
        
        // Загружаем список столиков (уже с новым)
        $tables = $tableModel->getTablesByRestaurantId($restaurantId);
        
        require_once __DIR__ . '/../views/table/manage.php';
    }
}