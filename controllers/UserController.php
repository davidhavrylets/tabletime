<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserManager.php';
require_once __DIR__ . '/../services/MailService.php'; 

class UserController {
    
    private UserManager $userManager;
    private MailService $mailService;
    
    public function __construct() {
        $this->userManager = new UserManager(); 
        $this->mailService = new MailService(); 
    }

    public function register() {
        
        $error = null; 
        // Инициализируем переменные для сохранения данных в форме после ошибки
        $nom = $_POST['nom'] ?? '';
        $email = $_POST['email'] ?? '';
        
        $SECRET_CODE = '200421'; // Секретный код оставляем, на случай, если логика владельца будет возвращена.

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Получаем данные из новой формы (mot_de_passe, mot_de_passe_confirm, privacy_policy)
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $motDePasseConfirm = $_POST['mot_de_passe_confirm'] ?? '';
            $privacyPolicy = $_POST['privacy_policy'] ?? ''; // Новая обязательная галочка
            
            // --- 1. ПРОВЕРКА ВАЛИДНОСТИ И НОВЫХ ПОЛЕЙ ---
            
            if (empty($nom)) {
                $error = "Будь ласка, введіть ваше ім'я.";
            }
            
            if (empty($motDePasse) || empty($motDePasseConfirm)) {
                $error = "Пароль не може бути порожнім.";
            } elseif ($motDePasse !== $motDePasseConfirm) {
                $error = "Паролі не співпадають.";
            }

            // 💥 ОБЯЗАТЕЛЬНАЯ ПРОВЕРКА: Принятие политики конфиденциальности
            if (!$error && empty($privacyPolicy)) {
                $error = "Вы должны принять Политику конфиденциальности.";
            }
            
            // При регистрации роль всегда 'client', старую логику выбора роли удаляем.
            $role = 'client'; 
            
            // --- 2. ОБРАБОТКА РЕГИСТРАЦИИ ---
            
            if (!$error) {
                
                $user = new User();

                try {
                    // Устанавливаем поля. 'prenom' и 'telephone' устанавливаем в пустые строки,
                    // поскольку их нет в новой форме регистрации.
                    $user->setNom($nom)
                         ->setPrenom('') 
                         ->setEmail($email)
                         ->setTelephone('') 
                         ->setMotDePasse($motDePasse)
                         ->setRole($role);
                    
                    $verificationToken = bin2hex(random_bytes(32)); 
                    
                    $userId = $this->userManager->register($user, $verificationToken);

                    if ($userId) {
                        
                        $verificationLink = "http://" . $_SERVER['HTTP_HOST'] . "/tabletime/?route=user/verify&token=" . $verificationToken;
                        
                        $emailBody = "<h1>Подтверждение регистрации TableTime</h1>";
                        // Используем Nom (Имя)
                        $emailBody .= "<p>Здравствуйте, " . htmlspecialchars($user->getNom()) . ".</p>";
                        $emailBody .= "<p>Чтобы завершить регистрацию и активировать ваш аккаунт, пожалуйста, перейдите по следующей ссылке:</p>";
                        $emailBody .= "<p><a href='{$verificationLink}'>Активировать мой аккаунт</a></p>";
                        $emailBody .= "<p>Если ссылка не работает, скопируйте ее в браузер: {$verificationLink}</p>";

                        $mailSent = $this->mailService->sendEmail(
                            $user->getEmail(), 
                            "Подтверждение регистрации TableTime", 
                            $emailBody
                        );
                        
                        if ($mailSent) {
                            $_SESSION['success_message'] = "Вы успешно зарегистрированы! Пожалуйста, проверьте свою почту ({$user->getEmail()}) для активации аккаунта.";
                        } else {
                            $_SESSION['error_message'] = "Регистрация успешна, но не удалось отправить письмо с подтверждением. Свяжитесь с поддержкой.";
                        }
                        header('Location: ?route=login');
                        die();
                        
                    } else {
                        $error = "Ошибка: Email уже существует или проблема с регистрацией.";
                    }
                } catch (InvalidArgumentException $e) {
                    $error = "Ошибка: " . $e->getMessage();
                }
            }

        } 
        
        // Передаем $nom и $email, чтобы они сохранились в форме в случае ошибки
        require_once __DIR__ . '/../views/user/register.php';
    }

    public function login() {
        
        $error = null; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $loggedInUser = $this->userManager->login($email, $password);

            if ($loggedInUser) {
                
                $rawUserData = $this->userManager->findUserByEmail($email);
                
                if (!$rawUserData || !isset($rawUserData['is_verified']) || (int)$rawUserData['is_verified'] === 0) {
                    session_unset();
                    session_destroy();
                    $error = "Пожалуйста, проверьте свою почту и активируйте аккаунт перед входом.";
                } else {
                    $_SESSION['user_id'] = $loggedInUser->getId();
                    $_SESSION['user_nom'] = $loggedInUser->getNom();
                    $_SESSION['user_role'] = $loggedInUser->getRole(); 

                    header('Location: ?route=home');
                    die();
                }
                
            } else {
                $error = "Неверный email или пароль.";
            }
        } 
        
        require_once __DIR__ . '/../views/user/login.php';
    }

    public function verify(): void {
        $token = $_GET['token'] ?? null;
        
        if (!$token) {
            $_SESSION['error_message'] = "Недействительный или отсутствующий токен верификации.";
            header('Location: ?route=login');
            exit;
        }

        $user = $this->userManager->findUserByToken($token); 

        if ($user) {
            
            if ($this->userManager->verifyUser($user['id'])) {
                $_SESSION['success_message'] = "Ваш аккаунт успешно активирован! Теперь вы можете войти.";
            } else {
                $_SESSION['error_message'] = "Ошибка при активации аккаунта. Попробуйте позже.";
            }
        } else {
            $_SESSION['error_message'] = "Токен недействителен или ваш аккаунт уже активирован.";
        }

        header('Location: ?route=login');
        exit;
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
        
        $user = $this->userManager->getUserById($userId);
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
            
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $telephone = trim($_POST['telephone'] ?? '');
            
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
                    
                    if ($this->userManager->update($user, $passwordToUpdate)) {
                        $success = "Профиль успешно обновлен.";
                        
                        $_SESSION['user_nom'] = $user->getNom();
                        
                        $user = $this->userManager->getUserById($userId);
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