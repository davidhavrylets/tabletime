<?php

require_once __DIR__ . '/../models/Restaurant.php';

class RestaurantController {
    
    /**
     * 1. ПУБЛИЧНЫЙ МЕТОД (для ?route=home)
     */
    public function indexPublic() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        $restaurantModel = new Restaurant();
        
        $restaurants = $restaurantModel->getRestaurants($search, $sort, $order); 
        
        require_once __DIR__ . '/../views/restaurant/index_public.php'; 
    }

    /**
     * 2. АДМИН-ПАНЕЛЬ (для ?route=restaurant/list)
     */
    public function list() {
        
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
        
        $restaurants = $restaurantModel->getRestaurantsByUserId($_SESSION['user_id']);
        
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
        // --- КОНЕЦ ПРОВЕРКИ ---

        $error = null;
        $success = null;
        
        $restaurantModel = new Restaurant();
        $userId = $_SESSION['user_id'];

        // 💥 УДАЛЕНА ПРОВЕРКА, КОТОРАЯ ЗАПРЕЩАЛА ВТОРОЙ РЕСТОРАН. 
        // Теперь владелец может иметь несколько ресторанов.

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $description = $_POST['description'] ?? '';
            
            $isCreated = $restaurantModel->createRestaurant($nom, $adresse, $description, $userId);
            
            if ($isCreated) {
                // Если пользователь был клиентом, обновляем роль!
                $_SESSION['user_role'] = 'owner'; 
                
                $_SESSION['success_message'] = "Ресторан '{$nom}' успешно создан! Теперь вы можете добавить столики.";
                
                // 💥 ИСПРАВЛЕНО: Перенаправляем на СПИСОК РЕСТОРАНОВ
                header('Location: ?route=restaurant/list'); 
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

        $id = $_GET['id'] ?? null; 
        $userId = $_SESSION['user_id'];
        $restaurantModel = new Restaurant();
        
        // --- ПРОВЕРКА ВЛАДЕНИЯ ---
        if (!$id || !$restaurantModel->isOwnerOfRestaurant($userId, $id)) {
             $_SESSION['error_message'] = "Вы не можете редактировать этот ресторан.";
             // 💥 ИСПРАВЛЕНО: Перенаправляем на СПИСОК РЕСТОРАНОВ
             header('Location: ?route=restaurant/list'); 
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
                // 💥 ИСПРАВЛЕНО: Перенаправляем на СПИСОК РЕСТОРАНОВ
                header('Location: ?route=restaurant/list'); 
                exit;
            } else {
                $_SESSION['error_message'] = "Ошибка при обновлении ресторана.";
            }
        }
        
        require_once __DIR__ . '/../views/restaurant/edit.php';
    }
    
}