<?php
// index.php

session_start();

require_once __DIR__ . '/models/User.php';

$route = isset($_GET['route']) ? $_GET['route'] : 'home';


require_once __DIR__ . '/views/layout/header.php';


if ($route === 'home') {
    require_once __DIR__ . '/views/home/index.php';
} else if ($route === 'register') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new User();
        $userId = $user->register(
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['email'],
            $_POST['telephone'] ?? '',
            $_POST['mot_de_passe']
        );
        if ($userId) {
            header('Location: ?route=login');
            exit;
        } else {
            echo "Ошибка: Email уже существует или проблема с регистрацией.";
        }
    } else {
        require_once __DIR__ . '/views/user/register.php';
    }
} else if ($route === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new User();
        $loggedIn = $user->login($_POST['email'], $_POST['mot_de_passe']);
        if ($loggedIn) {
            $_SESSION['user_id'] = $loggedIn['id'];
            $_SESSION['user_nom'] = $loggedIn['nom'];
            header('Location: ?route=home');
            exit;
        } else {
            echo "Неверный email или пароль.";
        }
    } else {
        require_once __DIR__ . '/views/user/login.php';
    }
} else {
    echo "404 - Страница не найдена.";
}


require_once __DIR__ . '/views/layout/footer.php';