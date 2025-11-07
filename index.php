<?php
session_start();

require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ReservationController.php'; 
require_once __DIR__ . '/controllers/RestaurantController.php'; // СТРОКА 9: ПОДКЛЮЧЕН

$route = isset($_GET['route']) ? $_GET['route'] : 'home';

require_once __DIR__ . '/views/layout/header.php';

if ($route === 'home') {
    
    $restaurantController = new RestaurantController();
    $restaurantController->indexPublic();
} else if ($route === 'register') {
    $userController = new UserController();
    $userController->register();
} else if ($route === 'login') {
    $userController = new UserController();
    $userController->login();
} else if ($route === 'logout') {
    $userController = new UserController();
    $userController->logout();
} else if ($route === 'reservation/create') { 
    $reservationController = new ReservationController();
    $reservationController->create();
} else if ($route === 'restaurant/list') {
    $restaurantController = new RestaurantController();
    $restaurantController->list();
} else if ($route === 'restaurant/create') {
    $restaurantController = new RestaurantController();
    $restaurantController->create();
} else if ($route === 'restaurant/delete') { 
    $restaurantController = new RestaurantController();
    $restaurantController->delete();
} else if ($route === 'restaurant/edit') { 
    $restaurantController = new RestaurantController();
    $restaurantController->edit();
} else { 
    echo "404 - Страница не найдена.";
}

require_once __DIR__ . '/views/layout/footer.php';