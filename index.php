<?php
session_start();



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require __DIR__ . '/libraries/PHPMailer/src/Exception.php';
require __DIR__ . '/libraries/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/libraries/PHPMailer/src/SMTP.php';

require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ReservationController.php'; 
require_once __DIR__ . '/controllers/RestaurantController.php';
require_once __DIR__ . '/controllers/TableController.php';



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
    
// --- УПРАВЛЕНИЯ СТОЛИКАМИ (TABLE CRUD) ---
} else if ($route === 'table/manage') {
    $tableController = new TableController();
    $tableController->manage();
} else if ($route === 'table/edit') {
    $tableController = new TableController();
    $tableController->edit();
} else if ($route === 'table/delete') {
    $tableController = new TableController();
    $tableController->delete();
// ----------------------------------------------------------------------
    
} else if ($route === 'reservation/list') {
    $controller = new ReservationController();
    $controller->list();
} else if ($route === 'reservation/manage') {
    $controller = new ReservationController();
    $controller->manage();
} else if ($route === 'reservation/confirm') {
    $controller = new ReservationController();
    $controller->confirm();
} else if ($route === 'reservation/cancel') {
    $controller = new ReservationController();
    $controller->cancel();
} else if ($route === 'user/profile') {
    $controller = new UserController();
    $controller->profile();


} else if ($route === 'user/verify') {
    $controller = new UserController();
    $controller->verify();


} else {
    echo "404 - Страница не найдена.";
}

require_once __DIR__ . '/views/layout/footer.php';