<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableTime - Резервация столов</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
<nav>
    <a href="?route=home">Главная</a>
    <a href="?route=user/profile">Мой Профиль</a>
    <?php 
    
require_once __DIR__ . '/../../models/Restaurant.php'; // Выходим из layout/, выходим из views/
    require_once __DIR__ . '/../../models/Reservation.php'; // Выходим из layout/, выходим из views/

    if (isset($_SESSION['user_id'])): 
        $userId = $_SESSION['user_id'];
        
        
        $restaurantModel = new Restaurant();
        $reservationModel = new Reservation();

        
        $restaurant = $restaurantModel->getRestaurantByUserId($userId);
    ?>
        
        Привет, <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
        
        <a href="?route=reservation/list">Мои бронирования</a> 
        
        <a href="?route=table/manage">Управление рестораном</a> 

        <?php 
        
        if ($restaurant): 
            $restaurantId = $restaurant['id'];
            $pendingCount = $reservationModel->countPendingReservations($restaurantId);
        ?>
            <a href="?route=reservation/manage" style="font-weight: bold;">
                Управление Бронями 
                <?php if ($pendingCount > 0): ?>
                    <span style="color: red; font-weight: bold;">(<?php echo $pendingCount; ?>)</span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
        
        <a href="?route=logout">Выход</a> 
        
    <?php else: ?>
        <a href="?route=register">Регистрация</a> | <a href="?route=login">Вход</a>
    <?php endif; ?>
</nav>
    </header>
    <main>