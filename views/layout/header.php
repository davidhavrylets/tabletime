<?php

?>
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
            <?php 
           
            $userRole = $_SESSION['user_role'] ?? 'guest';
            ?>
            
            <a href="?route=home">Главная</a>
            
            <?php if ($userRole === 'owner'): ?>
                
                <a href="?route=restaurant/list">Мои Рестораны</a>
                
                <a href="?route=reservation/manage">Упр. Бронями</a>
                
            <?php elseif ($userRole === 'client'): ?>
                <a href="?route=reservation/list">Мои Бронирования</a>
                
            
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?route=user/profile">Профиль (<?php echo htmlspecialchars($_SESSION['user_nom']); ?>)</a>
                <a href="?route=logout">Выход</a>
            <?php else: ?>
                <a href="?route=register">Регистрация</a>
                <a href="?route=login">Вход</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>