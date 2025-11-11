<?php

require_once __DIR__ . '/AbstractManager.php'; 
require_once __DIR__ . '/User.php';

class UserManager extends AbstractManager {

    
    
    public function register(User $user, string $verificationToken): ?int { 
        $stmt = $this->db->prepare("SELECT id FROM UTILISATEUR WHERE email = :email");
        $stmt->execute([':email' => $user->getEmail()]);
        if ($stmt->fetch()) {
            return null; 
        }

        $hashed_password = password_hash($user->getMotDePasse(), PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare("
            INSERT INTO UTILISATEUR (nom, prenom, email, telephone, mot_de_passe, role, is_verified, verification_token)
            VALUES (:nom, :prenom, :email, :telephone, :hashed_password, :role, 0, :verification_token)
        "); // <- ИЗМЕНЕН SQL

        $params = [
            ':nom' => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':email' => $user->getEmail(),
            ':telephone' => $user->getTelephone(),
            ':hashed_password' => $hashed_password,
            ':role' => $user->getRole(),
            ':verification_token' => $verificationToken 
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
             ->setMotDePasse($userData['mot_de_passe']) 
             ->setRole($userData['role']); 

        return $user;
    }
        return null;
    }
    public function getUserById(int $id): ?User {
        $sql = "SELECT id, nom, prenom, email, telephone FROM UTILISATEUR WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $user = new User();
            $user->setId($userData['id'])
                 ->setNom($userData['nom'])
                 ->setPrenom($userData['prenom'])
                 ->setEmail($userData['email'])
                 ->setTelephone($userData['telephone']);
            return $user;
        }
        return null;
    }
    
    
    public function update(User $user, $password = null): bool {
        
        $sql = "UPDATE UTILISATEUR SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone"; 
        
        if ($password) {
            $sql .= ", mot_de_passe = :password";
        }
        
        $sql .= " WHERE id = :id"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $user->getId(), PDO::PARAM_INT);
        $stmt->bindParam(':nom', $user->getNom());
        $stmt->bindParam(':prenom', $user->getPrenom());
        $stmt->bindParam(':email', $user->getEmail());
        $stmt->bindParam(':telephone', $user->getTelephone());
        
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
        }
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            
            return false; 
        }
    }
    
    public function findUserByToken(string $token): array|bool {
        $stmt = $this->db->prepare("SELECT * FROM UTILISATEUR WHERE verification_token = :token AND is_verified = 0");
        $stmt->execute([':token' => $token]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    
    
    public function verifyUser(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE UTILISATEUR SET is_verified = 1, verification_token = NULL WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    }

   
   public function findUserByEmail(string $email): array|bool {
    $stmt = $this->db->prepare("SELECT id, nom, prenom, email, telephone, mot_de_passe, role, is_verified FROM UTILISATEUR WHERE email = :email");
    $stmt->execute([':email' => $email]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC); 
}
}