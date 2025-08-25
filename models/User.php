<?php
// models/User.php

require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

    public function register($nom, $prenom, $email, $telephone, $mot_de_passe) {
        // Проверяем, существует ли email
        $stmt = $this->db->prepare("SELECT id FROM UTILISATEUR WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            return false; // Email уже существует
        }

        $hashed_password = password_hash($mot_de_passe, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare("
            INSERT INTO UTILISATEUR (nom, prenom, email, telephone, mot_de_passe)
            VALUES (:nom, :prenom, :email, :telephone, :hashed_password)
        ");
        
        $params = [
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':hashed_password' => $hashed_password
        ];
        
        if ($stmt->execute($params)) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function login($email, $mot_de_passe) {
        $stmt = $this->db->prepare("SELECT * FROM UTILISATEUR WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }
}