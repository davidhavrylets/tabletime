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
    
    
    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Вы должны войти, чтобы просмотреть профиль.";
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userManager = new UserManager();
        
       
        $user = $userManager->getUserById($userId);
        $error = null;
        $success = null;

        if (!$user) {
            $_SESSION['error_message'] = "Пользователь не найден.";
            header('Location: ?route=home');
            exit;
        }
        
        
        $user_data = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
            $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'mot_de_passe');
            $password_confirm = filter_input(INPUT_POST, 'mot_de_passe_confirm');
            
            
            $user_data = [
                'prenom' => $prenom,
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
            ];

           
            if (!$email) {
                $error = "Некорректный формат Email.";
            } 
            
            
            if (!empty($password) && $password !== $password_confirm) {
                $error = "Пароли не совпадают.";
            }

            
            if (!$error) {
                
                
                try {
                    $user->setNom($nom)
                         ->setPrenom($prenom)
                         ->setEmail($email)
                         ->setTelephone($telephone);
                } catch (InvalidArgumentException $e) {
                    $error = $e->getMessage();
                }

                if (!$error) {
                    
                    $passwordToUpdate = empty($password) ? null : $password;
                    
                    if ($userManager->update($user, $passwordToUpdate)) {
                        $success = "Профиль успешно обновлен.";
                        
                        // Обновляем данные в сессии
                        $_SESSION['user_nom'] = $user->getNom();
                        
                        // Перезагружаем объект, чтобы получить актуальные данные
                        $user = $userManager->getUserById($userId);
                        $user_data = [
                            'prenom' => $user->getPrenom(),
                            'nom' => $user->getNom(),
                            'email' => $user->getEmail(),
                            'telephone' => $user->getTelephone(),
                        ];
                        
                    } else {
                        $error = "Ошибка при обновлении профиля. Возможно, Email уже используется.";
                    }
                }
            }
        }
        
        include 'views/user/profile.php';
    }
}