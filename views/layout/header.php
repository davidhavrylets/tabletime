<?php
// Мы не подключаем здесь модели, так как вся необходимая информация (роль, имя)
// теперь находится в сессии ($_SESSION). Это "чистый" подход.
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
            // Получаем роль из сессии. По умолчанию 'guest' (гость), если не вошел.
            $userRole = $_SESSION['user_role'] ?? 'guest';
            ?>
            
            <a href="?route=home">Главная</a>
            
            <?php if ($userRole === 'owner'): ?>
                <a href="?route=table/manage">Упр. Столиками</a>
                <a href="?route=reservation/manage">Упр. Бронями</a>
                <a href="?route=restaurant/edit">Мой Ресторан</a>
                
            <?php elseif ($userRole === 'client'): ?>
                <a href="?route=reservation/list">Мои Бронирования</a>
                <a href="?route=restaurant/create">Стать Владельцем</a>
            
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