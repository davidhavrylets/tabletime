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
       
        
        require_once __DIR__ . '/../views/restaurant/create.php';
    }
    
    
}