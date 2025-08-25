<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserManager.php';

class UserController {
    
    public function register() {
        $userManager = new UserManager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            try {
                $user->setNom($_POST['nom'])
                     ->setPrenom($_POST['prenom'])
                     ->setEmail($_POST['email'])
                     ->setTelephone($_POST['telephone'] ?? '')
                     ->setMotDePasse($_POST['mot_de_passe']);

                $userId = $userManager->register($user);

                if ($userId) {
                    header('Location: ?route=login');
                    die();
                } else {
                    echo "Ошибка: Email уже существует или проблема с регистрацией.";
                }
            } catch (InvalidArgumentException $e) {
                echo "Ошибка: " . $e->getMessage();
            }
        } else {
            require_once __DIR__ . '/../views/user/register.php';
        }
    }

    public function login() {
        $userManager = new UserManager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loggedInUser = $userManager->login($_POST['email'], $_POST['mot_de_passe']);

            if ($loggedInUser) {
                $_SESSION['user_id'] = $loggedInUser->getId();
                $_SESSION['user_nom'] = $loggedInUser->getNom();
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