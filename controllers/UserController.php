<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserManager.php';

class UserController {
    
    public function register() {
        $userManager = new UserManager();
        $error = null; // Мы будем использовать эту переменную для передачи ошибок в HTML
        
        // Ваш секретный код
        $SECRET_CODE = '200421'; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $role = 'client'; // По умолчанию регистрируем как клиента

            // --- ИСПРАВЛЕНИЕ: Читаем 'password' из формы ---
            $password = $_POST['password'] ?? ''; 
            
            try {
                // 1. Определяем, какая кнопка была нажата
                if (isset($_POST['register_owner'])) {
                    // --- Регистрация Владельца ---
                    $role = 'owner';
                    $submitted_code = $_POST['secret_code'] ?? '';

                    // Проверяем секретный код
                    if ($submitted_code !== $SECRET_CODE) {
                        $error = "Неверный секретный код владельца.";
                    }
                    
                } elseif (isset($_POST['register_client'])) {
                    // --- Регистрация Клиента ---
                    $role = 'client';
                }

                // 2. Если ошибок (например, неверный код) нет, продолжаем
                if (!$error) {
                    
                    // --- ИСПРАВЛЕНИЕ: Валидация пароля ---
                    if (empty($password)) {
                        $error = "Пароль не может быть пустым.";
                    }
                    
                    // (Вы можете добавить здесь и другие проверки: длина пароля и т.д.)
                }
                
                if (!$error) {
                    
                    // Устанавливаем данные из формы в объект User
                    $user->setNom($_POST['nom'])
                         ->setPrenom($_POST['prenom'])
                         ->setEmail($_POST['email'])
                         ->setTelephone($_POST['telephone'] ?? '')
                         ->setMotDePasse($password) // <-- ИСПОЛЬЗУЕМ $password
                         ->setRole($role); // <- Устанавливаем определенную роль

                    // 3. Пытаемся зарегистрировать
                    $userId = $userManager->register($user);

                    if ($userId) {
                        $_SESSION['success_message'] = "Регистрация прошла успешно. Теперь войдите в систему.";
                        header('Location: ?route=login');
                        die();
                    } else {
                        // Эта ошибка сработает, если email уже занят (из вашего UserManager)
                        $error = "Ошибка: Email уже существует или проблема с регистрацией.";
                    }
                }

            } catch (InvalidArgumentException $e) {
                // Эта ошибка сработает, если email некорректный (из сеттера User)
                $error = "Ошибка: " . $e->getMessage();
            }
        } 
        
        // Загружаем вид (и передаем $error, если он есть)
        require_once __DIR__ . '/../views/user/register.php';
    }

    // --- ОБНОВЛЕННЫЙ МЕТОД LOGIN ---
    public function login() {
        $userManager = new UserManager();
        $error = null; // Для передачи ошибки во view

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // ИСПРАВЛЕНО: Используем 'password' для единообразия с формой регистрации
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $loggedInUser = $userManager->login($email, $password);

            if ($loggedInUser) {
                // Успешный вход
                $_SESSION['user_id'] = $loggedInUser->getId();
                $_SESSION['user_nom'] = $loggedInUser->getNom();
                
                // --- НОВАЯ СТРОКА: СОХРАНЯЕМ РОЛЬ В СЕССИИ ---
                $_SESSION['user_role'] = $loggedInUser->getRole(); 

                header('Location: ?route=home');
                die();
            } else {
                $error = "Неверный email или пароль.";
            }
        } 
        
        // Загружаем вид (и передаем $error, если он есть)
        require_once __DIR__ . '/../views/user/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ?route=home');
        die();
    }
    
    
    // --- ОБНОВЛЕННЫЙ МЕТОД PROFILE ---
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
            
            // ИСПРАВЛЕНО: Убрали FILTER_SANITIZE_STRING
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $telephone = trim($_POST['telephone'] ?? '');
            
            // ИСПРАВЛЕНО: Используем 'password' для единообразия
            $password = filter_input(INPUT_POST, 'password');
            $password_confirm = filter_input(INPUT_POST, 'password_confirm');
            
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
                        
                        $_SESSION['user_nom'] = $user->getNom();
                        
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