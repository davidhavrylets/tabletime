<?php

require_once __DIR__ . '/../models/Restaurant.php';

class RestaurantController {
    
    /**
     * 1. ПУБЛИЧНЫЙ МЕТОД (для ?route=home)
     * (Здесь все в порядке, он не требует входа)
     */
    public function indexPublic() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        $restaurantModel = new Restaurant();
        
        // ВАЖНО: Убедитесь, что `getRestaurants` в модели не возвращает user_id
        $restaurants = $restaurantModel->getRestaurants($search, $sort, $order); 
        
        // Используем БЕЗОПАСНЫЙ файл
        require_once __DIR__ . '/../views/restaurant/index_public.php'; 
    }

    /**
     * 2. АДМИН-ПАНЕЛЬ (для ?route=restaurant/list)
     * (Показывает владельцу ЕГО рестораны)
     */
    public function list() {
        
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

        $restaurantModel = new Restaurant();
        
        // ИСПРАВЛЕНИЕ: Владелец должен видеть ТОЛЬКО СВОИ рестораны
        $restaurants = $restaurantModel->getRestaurantsByUserId($_SESSION['user_id']);
        
        // Загружаем админ-панель
        require_once __DIR__ . '/../views/restaurant/list.php';
    }

    
    /**
     * 3. СОЗДАНИЕ РЕСТОРАНА
     */
    public function create() {
        // --- ПРОВЕРКА АВТОРИЗАЦИИ И РОЛИ ---
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        // (Мы не проверяем роль 'owner', т.к. 'client' тоже может "Стать Владельцем")
        // --- КОНЕЦ ПРОВЕРКИ ---

        $error = null;
        $success = null;
        
        $restaurantModel = new Restaurant();
        $userId = $_SESSION['user_id'];

        // --- ДОП. ПРОВЕРКА: Не даем создать ВТОРОЙ ресторан ---
        if ($restaurantModel->getRestaurantByUserId($userId)) {
            $_SESSION['error_message'] = "Вы уже являетесь владельцем ресторана. Вы не можете создать второй.";
            header('Location: ?route=table/manage'); // Отправляем его в админку
            exit;
        }
        // --- КОНЕЦ ПРОВЕРКИ ---

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $description = $_POST['description'] ?? '';
            
            $isCreated = $restaurantModel->createRestaurant($nom, $adresse, $description, $userId);
            
            if ($isCreated) {
                // ВАЖНО: После создания ресторана, обновляем роль в сессии!
                $_SESSION['user_role'] = 'owner'; 
                
                $_SESSION['success_message'] = "Ресторан '{$nom}' успешно создан! Теперь вы можете добавить столики.";
                header('Location: ?route=table/manage'); // Сразу отправляем на создание столиков
                exit;
            } else {
                $error = "Ошибка при создании ресторана.";
            }
        }
        
        require_once __DIR__ . '/../views/restaurant/create.php';
    } 
    
    
    /**
     * 4. УДАЛЕНИЕ РЕСТОРАНА
     */
    public function delete() {
        
        // --- ПРОВЕРКА АВТОРИЗАЦИИ И РОЛИ ---
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа.";
            header('Location: ?route=home');
            exit;
        }
        // --- КОНЕЦ ПРОВЕРКИ ---

        $restaurantId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if ($restaurantId) {
            $restaurantModel = new Restaurant();
            
            // --- ПРОВЕРКА ВЛАДЕНИЯ ---
            if (!$restaurantModel->isOwnerOfRestaurant($userId, $restaurantId)) {
                 $_SESSION['error_message'] = "Вы не можете удалить этот ресторан.";
                 header('Location: ?route=restaurant/list');
                 exit;
            }
            // --- КОНЕЦ ПРОВЕРКИ ---
            
            $isDeleted = $restaurantModel->deleteRestaurant($restaurantId);

            if ($isDeleted) {
                $_SESSION['success_message'] = "Ресторан с ID {$restaurantId} успешно удален.";
            } else {
                $_SESSION['error_message'] = "Ошибка удаления ресторана.";
            }
        }
        
        header('Location: ?route=restaurant/list');
        exit;
    } 

    /**
     * 5. РЕДАКТИРОВАНИЕ РЕСТОРАНА
     */
    public function edit() {
        // --- ПРОВЕРКА АВТОРИЗАЦИИ И РОЛИ ---
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа.";
            header('Location: ?route=home');
            exit;
        }
        // --- КОНЕЦ ПРОВЕРКИ ---

        $id = $_GET['id'] ?? null; // ID ресторана для редактирования
        $userId = $_SESSION['user_id'];
        $restaurantModel = new Restaurant();
        
        // --- ПРОВЕРКА ВЛАДЕНИЯ ---
        if (!$id || !$restaurantModel->isOwnerOfRestaurant($userId, $id)) {
             $_SESSION['error_message'] = "Вы не можете редактировать этот ресторан.";
             header('Location: ?route=table/manage'); // На главную админки
             exit;
        }
        // --- КОНЕЦ ПРОВЕРКИ ---

        $restaurant = $restaurantModel->getRestaurantById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? $restaurant['nom'];
            $adresse = $_POST['adresse'] ?? $restaurant['adresse'];
            $description = $_POST['description'] ?? $restaurant['description'];

            $isUpdated = $restaurantModel->updateRestaurant($id, $nom, $adresse, $description);

            if ($isUpdated) {
                $_SESSION['success_message'] = "Ресторан '{$nom}' успешно обновлен.";
                header('Location: ?route=table/manage'); // На главную админки
                exit;
            } else {
                $_SESSION['error_message'] = "Ошибка при обновлении ресторана.";
            }
        }
        
        require_once __DIR__ . '/../views/restaurant/edit.php';
    }
    
}