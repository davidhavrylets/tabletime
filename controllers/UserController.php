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
        // Инициализируем переменные, чтобы избежать ошибок "undefined" при первом рендере
        $nom = $_POST['nom'] ?? ''; 
        $email = $_POST['email'] ?? '';
        
        $SECRET_CODE = '200421'; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $motDePasse = $_POST['mot_de_passe'] ?? '';
            $motDePasseConfirm = $_POST['mot_de_passe_confirm'] ?? '';
            $privacyPolicy = $_POST['privacy_policy'] ?? '';
            $ownerCode = $_POST['owner_code'] ?? ''; 
            
            if (empty($nom)) {
                $error = "Veuillez entrer votre nom.";
            }
            
            if (empty($motDePasse) || empty($motDePasseConfirm)) {
                $error = "Le mot de passe ne peut pas être vide.";
            } elseif ($motDePasse !== $motDePasseConfirm) {
                $error = "Les mots de passe ne correspondent pas.";
            }

            if (!$error && empty($privacyPolicy)) {
                $error = "Vous devez accepter la politique de confidentialité.";
            }
            
            
            $role = 'client';
            if (!$error && !empty($ownerCode) && $ownerCode === $SECRET_CODE) {
                $role = 'owner';
            }
            
            
            if (!$error) {
                
                $user = new User();

                try {
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
                        
                        $emailBody = "<h1>Confirmation d'inscription TableTime</h1>";
                        $emailBody .= "<p>Bonjour, " . htmlspecialchars($user->getNom()) . ".</p>";
                        $emailBody .= "<p>Pour terminer votre inscription et activer votre compte, veuillez cliquer sur le lien suivant:</p>";
                        $emailBody .= "<p><a href='{$verificationLink}'>Activer mon compte</a></p>";
                        $emailBody .= "<p>Si le lien ne fonctionne pas, copiez-le dans votre navigateur : {$verificationLink}</p>";

                        $mailSent = $this->mailService->sendEmail(
                            $user->getEmail(), 
                            "Подтверждение регистрации TableTime", 
                            $emailBody
                        );
                        
                        if ($mailSent) {
                            $_SESSION['success_message'] = "Vous êtes désormais inscrit ! Veuillez vérifier votre boîte mail. ({$user->getEmail()}) pour activer votre compte.";
                        } else {
                            $_SESSION['error_message'] = "L'inscription a été effectuée avec succès, mais l'e-mail de confirmation n'a pas pu être envoyé. Veuillez contacter le service d'assistance.";
                        }
                        
                        // ГАРАНТИРУЕМ ПЕРЕНАПРАВЛЕНИЕ:
                        header('Location: ?route=login');
                        exit; // Используем exit для надежной остановки скрипта
                        
                    } else {
                        // ОШИБКА: Email уже существует (возвращается из UserManager)
                        $error = "Erreur: Email déjà existant ou problème d'inscription.";
                    }
                } catch (InvalidArgumentException $e) {
                    $error = "Erreur: " . $e->getMessage();
                }
            }

        } 
        
        // Отображение формы с ошибкой (если она есть)
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
                    $error = "Veuillez vérifier votre e-mail et activer votre compte avant de vous connecter.";
                } else {
                    $_SESSION['user_id'] = $loggedInUser->getId();
                    $_SESSION['user_nom'] = $loggedInUser->getNom();
                    $_SESSION['user_role'] = $loggedInUser->getRole(); 

                    header('Location: ?route=home');
                    exit; // ИСПОЛЬЗУЙТЕ exit
                }
                
            } else {
                $error = "Adresse e-mail ou mot de passe incorrect.";
            }
        } 
        
        require_once __DIR__ . '/../views/user/login.php';
    }

    public function verify(): void {
        $token = $_GET['token'] ?? null;
        
        if (!$token) {
            $_SESSION['error_message'] = "Jeton de vérification invalide ou manquant.";
            header('Location: ?route=login');
            exit;
        }

        $user = $this->userManager->findUserByToken($token); 

        if ($user) {
            
            if ($this->userManager->verifyUser($user['id'])) {
                $_SESSION['success_message'] = "Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'activation du compte. Veuillez réessayer plus tard.";
            }
        } else {
            $_SESSION['error_message'] = "Le jeton est invalide ou votre compte est déjà activé.";
        }

        header('Location: ?route=login');
        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: ?route=home');
        exit; // ИСПОЛЬЗУЙТЕ exit
    }
    
    
    // --- ИСПРАВЛЕННЫЙ МЕТОД PROFILE ---
    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez vous connecter pour consulter votre profil.";
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $error = null;
        $success = null;

        
        $user = $this->userManager->getUserById($userId);

        if (!$user) {
            $_SESSION['error_message'] = "Utilisateur non trouvé.";
            header('Location: ?route=home');
            exit;
        }
        
        
        $user_data = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'role' => $user->getRole(), 
        ];

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $action = $_POST['action_type'] ?? null; 

            if ($action === 'update_info') {
                // ===================================
                // A. ЛОГИКА ОБНОВЛЕНИЯ ЛИЧНЫХ ДАННЫХ
                // ===================================
                $nom = trim($_POST['nom'] ?? '');
                $prenom = trim($_POST['prenom'] ?? '');
                $telephone = trim($_POST['telephone'] ?? '');
                $email = $user->getEmail(); // Email не меняем
                
                if (empty($nom) || empty($prenom)) {
                    $error = "Le nom et le prénom sont obligatoires.";
                }

                if (!$error) {
                    try {
                        
                        $user->setNom($nom)
                            ->setPrenom($prenom)
                            ->setTelephone($telephone);
                        
                        
                        if ($this->userManager->update($user, null)) {
                            $success = "Vos informations personnelles ont été mises à jour avec succès.";
                            $_SESSION['user_nom'] = $user->getNom(); // Обновляем сессию
                        } else {
                            $error = "Erreur lors de la mise à jour des données. Veuillez réessayer.";
                        }
                    } catch (InvalidArgumentException $e) {
                        $error = $e->getMessage();
                    }
                }
                
            } elseif ($action === 'change_password') {
                // ===================================
                // B. ЛОГИКА СМЕНЫ ПАРОЛЯ
                // ===================================
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                
                if (empty($newPassword) || $newPassword !== $confirmPassword) {
                    $error = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
                } 
                
                else {
                    $currentUserData = $this->userManager->findUserByEmail($user->getEmail());
                    
                    if (!$currentUserData || !password_verify($currentPassword, $currentUserData['mot_de_passe'])) {
                        $error = "Mot de passe actuel incorrect.";
                    }
                }

                if (!$error) {
                    
                    if ($this->userManager->update($user, $newPassword)) {
                        $success = "Le mot de passe a été changé avec succès !";
                    } else {
                        $error = "Erreur lors de la modification du mot de passe.";
                    }
                }
            }
            
            
        
            
            if ($success) {
                $_SESSION['success_message'] = $success;
            } elseif ($error) {
                $_SESSION['error_message'] = $error;
            }
            
            
            
            header('Location: ?route=user/profile');
            exit;
        }
        
        $user = $this->userManager->getUserById($userId);
        $user_data = [
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'telephone' => $user->getTelephone(),
            'role' => $user->getRole(), 
        ];
        
        include __DIR__ . '/../views/user/profile.php';
    } 
}
