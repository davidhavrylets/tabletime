<?php

class UserController {
    
    public function register() {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $userModel->register(
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['telephone'] ?? '',
                $_POST['mot_de_passe']
            );

            if ($userId) {
                header('Location: ?route=login');
                die();
            } else {
                echo "Ошибка: Email уже существует или проблема с регистрацией.";
            }
        } else {
            require_once __DIR__ . '/../views/user/register.php';
        }
    }

    public function login() {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loggedIn = $userModel->login($_POST['email'], $_POST['mot_de_passe']);

            if ($loggedIn) {
                $_SESSION['user_id'] = $loggedIn['id'];
                $_SESSION['user_nom'] = $loggedIn['nom'];
                header('Location: ?route=home');
                die();
            } else {
                echo "Неверный email или пароль.";
            }
        } else {
            require_once __DIR__ . '/../views/user/login.php';
        }
    }
     public function logout() {
        session_destroy(); 
        header('Location: ?route=home'); 
        die();
    }
}