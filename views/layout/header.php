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
    <div class="header-inner">
        
        <a href="?route=home" class="logo">
            <img 
                src="assets/images/tabletime_logo.png" 
                alt="TableTime Logo" 
                style="height: 40px; vertical-align: middle;" 
                loading="lazy"
            >
        </a>
        
        <nav>
            <ul>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] === 'client'): ?>
                        <li><a href="?route=reservation/list">Мои бронирования</a></li>
                       <li><a href="?route=user/profile">Мой профиль</a></li> 
                        <li><a href="?route=logout">Выйти</a></li>
                    <?php elseif ($_SESSION['user_role'] === 'owner'): ?>
                        <li><a href="?route=restaurant/list">Мои рестораны</a></li>
                        <li><a href="?route=reservation/manage">Управление бронированиями</a></li>
                        <li><a href="?route=logout">Выйти</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="?route=register">Регистрация</a></li>
                    <li><a href="?route=login">Войти</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
    </div>
</header>
    <main>