<?php

require_once __DIR__ . '/AbstractManager.php';

class Restaurant extends AbstractManager {

   public function createRestaurant($nom, $adresse, $description, $utilisateurId, $photoFilename = null) {
        
        $sql = "INSERT INTO restaurant (nom, adresse, description, utilisateur_id, photo_filename) 
                VALUES (:nom, :adresse, :description, :utilisateurId, :photo_filename)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':utilisateurId', $utilisateurId); 
        $stmt->bindParam(':photo_filename', $photoFilename); 

        return $stmt->execute();
    }


    public function getAllRestaurants() {
        $sql = "SELECT * FROM restaurant";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRestaurants(string $search = '', string $sort = 'id', string $order = 'ASC') {
        
        $sql = "SELECT id, nom, adresse, description, utilisateur_id, photo_filename FROM restaurant WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (nom LIKE :search OR adresse LIKE :search OR description LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $allowedSort = ['id', 'nom', 'adresse']; 
        $allowedOrder = ['ASC', 'DESC'];
        
        $sort = in_array($sort, $allowedSort) ? $sort : 'id';
        $order = in_array($order, $allowedOrder) ? $order : 'ASC';

        $sql .= " ORDER BY {$sort} {$order}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRestaurantById($id) {
        
       $sql = "SELECT id, nom, adresse, description, utilisateur_id, photo_filename FROM restaurant WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $restaurant = $stmt->fetch(PDO::FETCH_ASSOC); 

        return $restaurant;
    }

 
    public function getRestaurantByUserId($userId) {
        
        $sql = "SELECT * FROM restaurant WHERE utilisateur_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   
    public function getRestaurantsByUserId($userId) {
       
        $sql = "SELECT * FROM restaurant WHERE utilisateur_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRestaurant($id, $nom, $adresse, $description) {
        
        $sql = "UPDATE restaurant 
                SET nom = :nom, adresse = :adresse, description = :description 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function deleteRestaurant($id) {
        
        if ($id == 1) { 
            return false;
        }
        $sql = "DELETE FROM restaurant WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    
    public function isOwnerOfRestaurant($userId, $restaurantId) {
        
        $sql = "SELECT id FROM restaurant WHERE id = :restaurantId AND utilisateur_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurantId', $restaurantId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (bool)$stmt->fetch();
    }
}