<?php
require_once __DIR__ . '/../models/Restaurant.php';

function isUserOwner() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    // Вызов модели тут не идеален, но прост для nav bar
    $restaurantModel = new Restaurant(); 
    return $restaurantModel->isOwner($_SESSION['user_id']);
}