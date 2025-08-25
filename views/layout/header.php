<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableTime - Резервация столов</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
<nav>
    <a href="?route=home">Главная</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        Привет, <?php echo htmlspecialchars($_SESSION['user_nom']); ?>
        <a href="?route=logout">Выход</a> <?php else: ?>
        <a href="?route=register">Регистрация</a> | <a href="?route=login">Вход</a>
    <?php endif; ?>
</nav>
    </header>
    <main>