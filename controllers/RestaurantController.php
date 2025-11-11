<?php

require_once __DIR__ . '/../models/Restaurant.php';

class RestaurantController {
    
   
    public function indexPublic() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        $restaurantModel = new Restaurant();
        
        $restaurants = $restaurantModel->getRestaurants($search, $sort, $order); 
        
        require_once __DIR__ . '/../views/restaurant/index_public.php'; 
    }

    /**
     * 2. АДМИН-ПАНЕЛЬ 
     */
    public function list() {
        
        
        if (!isset($_SESSION['user_id'])) { 
            header('Location: ?route=login'); 
            exit; 
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа к этой странице.";
            header('Location: ?route=home'); 
            exit;
        }
        

        $restaurantModel = new Restaurant();
        
        $restaurants = $restaurantModel->getRestaurantsByUserId($_SESSION['user_id']);
        
        require_once __DIR__ . '/../views/restaurant/list.php';
    }

    
   
    public function create() {
        
        // 1. ПРОВЕРКА АВТОРИЗАЦИИ
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        
        $error = null;
        $success = null;
        
        $restaurantModel = new Restaurant();
        $userId = $_SESSION['user_id'];
        $photoFilename = null; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $nom = $_POST['nom'] ?? '';
            $adresse = $_POST['adresse'] ?? '';
            $description = $_POST['description'] ?? '';
            
            
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                
                $file = $_FILES['photo'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $maxFileSize = 5 * 1024 * 1024; 
                $uploadDir = __DIR__ . '/../assets/images/restaurants/';

                
                if (!in_array($file['type'], $allowedTypes)) {
                    $error = "Недопустимый тип файла. Разрешены только JPG, PNG и WebP.";
                } elseif ($file['size'] > $maxFileSize) {
                    $error = "Файл слишком большой. Максимальный размер 5MB.";
                } else {
                   
                    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $photoFilename = uniqid('resto_', true) . '.' . $fileExtension;
                    $targetPath = $uploadDir . $photoFilename;

                    
                    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $error = "Не удалось переместить загруженный файл.";
                        $photoFilename = null; 
                    }
                }
            } 
           
            if ($error === null) {
                
                
                $isCreated = $restaurantModel->createRestaurant($nom, $adresse, $description, $userId, $photoFilename);
                
                if ($isCreated) {
                    
                    $_SESSION['user_role'] = 'owner'; 
                    $_SESSION['success_message'] = "Ресторан '{$nom}' успешно создан! Теперь вы можете добавить столики.";
                    
                    header('Location: ?route=restaurant/list'); 
                    exit;
                    
                } else {
                    $error = "Ошибка при создании ресторана в базе данных.";
                    
                    if ($photoFilename && file_exists($targetPath)) {
                        unlink($targetPath);
                    }
                }
            }
        }
        
        require_once __DIR__ . '/../views/restaurant/create.php';
    }
    
    
    /**
     * 4. УДАЛЕНИЕ РЕСТОРАНА
     */
    public function delete() {
        
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа.";
            header('Location: ?route=home');
            exit;
        }
        

        $restaurantId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if ($restaurantId) {
            $restaurantModel = new Restaurant();
            
           
            if (!$restaurantModel->isOwnerOfRestaurant($userId, $restaurantId)) {
                 $_SESSION['error_message'] = "Вы не можете удалить этот ресторан.";
                 header('Location: ?route=restaurant/list');
                 exit;
            }
           
            
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

    
    public function edit() {
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=login');
            exit;
        }
        if ($_SESSION['user_role'] !== 'owner') {
            $_SESSION['error_message'] = "У вас нет прав доступа.";
            header('Location: ?route=home');
            exit;
        }
        

        $id = $_GET['id'] ?? null; 
        $userId = $_SESSION['user_id'];
        $restaurantModel = new Restaurant();
        
        
        if (!$id || !$restaurantModel->isOwnerOfRestaurant($userId, $id)) {
             $_SESSION['error_message'] = "Вы не можете редактировать этот ресторан.";
             
             header('Location: ?route=restaurant/list'); 
             exit;
        }
        

        $restaurant = $restaurantModel->getRestaurantById($id);

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
        
        require_once __DIR__ . '/../views/restaurant/edit.php';
    }
    
}