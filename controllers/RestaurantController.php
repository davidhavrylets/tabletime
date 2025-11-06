<?php

require_once __DIR__ . '/../models/Restaurant.php';

class RestaurantController {
    
    public function list() {
        
       if (!isset($_SESSION['user_id'])) { header('Location: ?route=login'); exit; } 

        $restaurantModel = new Restaurant();
        $restaurants = $restaurantModel->getAllRestaurants();
        
       
        require_once __DIR__ . '/../views/restaurant/list.php';
    }

   
    public function create() {
    
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
    
    
}