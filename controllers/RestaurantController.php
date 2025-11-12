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
                    $error = "Type de fichier non autorisé. Seuls les formats JPG, PNG et WebP sont autorisés.";
                } elseif ($file['size'] > $maxFileSize) {
                    $error = "Le fichier est trop volumineux. La taille maximale est de 5 Mo.";
                } else {
                   
                    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $photoFilename = uniqid('resto_', true) . '.' . $fileExtension;
                    $targetPath = $uploadDir . $photoFilename;

                    
                    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $error = "Impossible de déplacer le fichier téléchargé.";
                        $photoFilename = null; 
                    }
                }
            } 
           
            if ($error === null) {
                
                
                $isCreated = $restaurantModel->createRestaurant($nom, $adresse, $description, $userId, $photoFilename);
                
                if ($isCreated) {
                    
                    $_SESSION['user_role'] = 'owner'; 
                    $_SESSION['success_message'] = "Le restaurant « {$nom} » a été créé avec succès ! Vous pouvez maintenant ajouter des tables.";
                    header('Location: ?route=restaurant/list'); 
                    exit;
                    
                } else {
                    $error = "Erreur lors de la création du restaurant dans la base de données.";
                    
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
            $_SESSION['error_message'] = "Vous n'avez pas les droits d'accès.";
            header('Location: ?route=home');
            exit;
        }
        

        $restaurantId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if ($restaurantId) {
            $restaurantModel = new Restaurant();
            
           
            if (!$restaurantModel->isOwnerOfRestaurant($userId, $restaurantId)) {
                 $_SESSION['error_message'] = "Vous ne pouvez pas supprimer ce restaurant.";
                 header('Location: ?route=restaurant/list');
                 exit;
            }
           
            
            $isDeleted = $restaurantModel->deleteRestaurant($restaurantId);

            if ($isDeleted) {
                $_SESSION['success_message'] = "Le restaurant avec l'ID {$restaurantId} a été supprimé avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la suppression du restaurant.";
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
            $_SESSION['error_message'] = "Vous n'avez pas les droits d'accès.";
            header('Location: ?route=home');
            exit;
        }
        

        $id = $_GET['id'] ?? null; 
        $userId = $_SESSION['user_id'];
        $restaurantModel = new Restaurant();
        
        
        if (!$id || !$restaurantModel->isOwnerOfRestaurant($userId, $id)) {
             $_SESSION['error_message'] = "Vous ne pouvez pas éditer ce restaurant.";
             
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
                $_SESSION['success_message'] = "Le restaurant « {$nom} » a été mis à jour avec succès.";
                
                header('Location: ?route=restaurant/list'); 
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour du restaurant.";
            }
        }
        
        require_once __DIR__ . '/../views/restaurant/edit.php';
    }
    
}