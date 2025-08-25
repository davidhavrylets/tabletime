<?php

session_start();

require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/ReservationController.php'; 

$route = isset($_GET['route']) ? $_GET['route'] : 'home';

require_once __DIR__ . '/views/layout/header.php';

if ($route === 'home') {
    require_once __DIR__ . '/views/home/index.php';
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
} else {
    echo "404 - Страница не найдена.";
}

require_once __DIR__ . '/views/layout/footer.php';