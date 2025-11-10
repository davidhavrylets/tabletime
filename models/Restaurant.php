<?php

require_once __DIR__ . '/../config/Database.php';

class Restaurant {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }

 
    public function createRestaurant($nom, $adresse, $description, $utilisateurId) {
        $sql = "INSERT INTO restaurant (nom, adresse, description, UTILISATEUR_id) 
                VALUES (:nom, :adresse, :description, :utilisateurId)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':utilisateurId', $utilisateurId); 

        return $stmt->execute();
    }


    public function getAllRestaurants() {
        $sql = "SELECT * FROM restaurant";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRestaurants(string $search = '', string $sort = 'id', string $order = 'ASC') {
        $sql = "SELECT * FROM restaurant WHERE 1=1";
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
        $sql = "SELECT * FROM restaurant WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $restaurant = $stmt->fetch(PDO::FETCH_ASSOC); 

        return $restaurant;
    }

    /**
     * 1. МЕТОД В ЕДИНСТВЕННОМ ЧИСЛЕ (КОТОРЫЙ ИСПРАВИТ ОШИБКУ)
     * (Используется в TableController и ReservationController)
     * (Этот метод у вас уже был, но, видимо, пропал)
     */
    public function getRestaurantByUserId($userId) {
        // Убедитесь, что колонка 'UTILISATEUR_id' (из вашего кода)
        $sql = "SELECT * FROM restaurant WHERE UTILISATEUR_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 2. МЕТОД ВО МНОЖЕСТВЕННОМ ЧИСЛЕ
     * (Используется в RestaurantController для админ-панели 'restaurant/list')
     */
    public function getRestaurantsByUserId($userId) {
        $sql = "SELECT * FROM restaurant WHERE UTILISATEUR_id = :user_id";
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
        if ($id == 1) { // (Ваша защитная логика)
            return false;
        }
        $sql = "DELETE FROM restaurant WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * 3. МЕТОД ПРОВЕРКИ ВЛАДЕЛЬЦА
     * (Используется в RestaurantController для edit() и delete())
     */
    public function isOwnerOfRestaurant($userId, $restaurantId) {
        $sql = "SELECT id FROM restaurant WHERE id = :restaurantId AND UTILISATEUR_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':restaurantId', $restaurantId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (bool)$stmt->fetch();
    }
}