<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/User.php';

class UserManager {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

    public function register(User $user): ?int {
        $stmt = $this->db->prepare("SELECT id FROM UTILISATEUR WHERE email = :email");
        $stmt->execute([':email' => $user->getEmail()]);
        if ($stmt->fetch()) {
            return null;
        }

        $hashed_password = password_hash($user->getMotDePasse(), PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare("
            INSERT INTO UTILISATEUR (nom, prenom, email, telephone, mot_de_passe)
            VALUES (:nom, :prenom, :email, :telephone, :hashed_password)
        ");
        
        $params = [
            ':nom' => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':email' => $user->getEmail(),
            ':telephone' => $user->getTelephone(),
            ':hashed_password' => $hashed_password
        ];
        
        if ($stmt->execute($params)) {
            return (int) $this->db->lastInsertId();
        }
        return null;
    }

    public function login(string $email, string $mot_de_passe): ?User {
        $stmt = $this->db->prepare("SELECT * FROM UTILISATEUR WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($mot_de_passe, $userData['mot_de_passe'])) {
            $user = new User();
            $user->setId($userData['id'])
                 ->setNom($userData['nom'])
                 ->setPrenom($userData['prenom'])
                 ->setEmail($userData['email'])
                 ->setTelephone($userData['telephone'])
                 ->setMotDePasse($userData['mot_de_passe']); // пароль здесь не нужен, но для примера

            return $user;
        }
        return null;
    }
}