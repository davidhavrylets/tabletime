<?php

require_once __DIR__ . '/../models/Table.php';
require_once __DIR__ . '/../models/Restaurant.php';

class TableController {
    
    /**
     * 1. РЕДАКТИРОВАНИЕ СТОЛИКА
     */
    public function edit() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tableId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);
        $redirectUrl = $restaurantId ? '?route=table/manage&restaurant_id=' . $restaurantId : '?route=restaurant/list';

        if (!$tableId) {
            $_SESSION['error_message'] = "Le numéro d'identification de la table n'est pas fourni.";
            header('Location: ' . $redirectUrl);
            exit;
        }

        $tableModel = new Table();
        $restaurantModel = new Restaurant();
        
        $table = $tableModel->getTableById($tableId);
        $restaurant = $restaurantModel->getRestaurantById($table['restaurant_id']);

        if (!$table || !$restaurant || $restaurant['utilisateur_id'] != $userId) {
            $_SESSION['error_message'] = "La table n'a pas été trouvée ou vous n'avez pas les droits nécessaires pour la modifier.";
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $capacity = filter_input(INPUT_POST, 'capacite', FILTER_VALIDATE_INT);
            
            $numero = trim($_POST['numero'] ?? ''); 
            
            if (!$capacity || $capacity <= 0 || empty($numero)) {
                $error = "Veuillez saisir le numéro/nom et la capacité corrects.";
            } else {
                
                if ($tableModel->updateTable($tableId, $capacity, $numero)) { 
                    $_SESSION['success_message'] = "La table « {$numero} » a été mise à jour avec succès.";
                    header('Location: ' . $redirectUrl);
                    exit;
                } else {
                    $error = "Erreur lors de la mise à jour de la table dans la base de données.";
                }
            }
            
            $table['capacite'] = $capacity; 
            
            $table['numero'] = $numero;
        }

        $userRestaurant = $restaurant; 
        require_once __DIR__ . '/../views/table/edit.php';
    }


    /**
     * 2. УДАЛЕНИЕ СТОЛИКА
     */
    public function delete() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tableId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);
        $redirectUrl = $restaurantId ? '?route=table/manage&restaurant_id=' . $restaurantId : '?route=restaurant/list';

        if (!$tableId) {
            $_SESSION['error_message'] = "Le numéro d'identification de la table n'est pas fourni.";
            header('Location: ' . $redirectUrl);
            exit;
        }

        $tableModel = new Table();
        $restaurantModel = new Restaurant();
        
        $table = $tableModel->getTableById($tableId);
        $restaurant = $restaurantModel->getRestaurantById($table['restaurant_id']);

        if (!$table || !$restaurant || $restaurant['utilisateur_id'] != $userId) {
            $_SESSION['error_message'] = "La table n'a pas été trouvée ou vous n'avez pas les droits nécessaires pour la supprimer.";
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        if ($tableModel->deleteTable($tableId)) {
            
            $tableName = $table['numero'] ?? 'ID: ' . $table['id']; 
            $_SESSION['success_message'] = "La table « {$tableName} » a été supprimée avec succès.";
        } else {
            $_SESSION['error_message'] = "Impossible de supprimer la table.";
        }

        header('Location: ' . $redirectUrl);
        exit;
    }


    /**
     * 3. УПРАВЛЕНИЕ (ГЛАВНАЯ СТРАНИЦА АДМИНКИ СТОЛИКОВ)
     */
    public function manage() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
            header('Location: ?route=login');
            exit;
        }
        
        $ownerId = $_SESSION['user_id'];
        $restaurantId = filter_input(INPUT_GET, 'restaurant_id', FILTER_VALIDATE_INT);

        if (!$restaurantId) {
             $_SESSION['error_message'] = "Не указан ID ресторана.";
             header('Location: ?route=restaurant/list'); 
             exit;
        }

        $restaurantModel = new Restaurant();
        $userRestaurant = $restaurantModel->getRestaurantById($restaurantId); 

        if (!$userRestaurant || $userRestaurant['utilisateur_id'] != $ownerId) {
            $_SESSION['error_message'] = "Le restaurant n'a pas été trouvé ou vous n'avez pas les droits d'accès. (ID du restaurant : {$restaurantId}, ID du propriétaire : {$ownerId})";
            header('Location: ?route=restaurant/list'); 
            exit;
        }

        $tableModel = new Table(); 
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $capacity = $_POST['capacite'] ?? null;
            
            $numero = trim($_POST['numero'] ?? ''); 

            if ($capacity && is_numeric($capacity) && $capacity > 0 && !empty($numero)) {
                
                
                $isCreated = $tableModel->createTable((int)$capacity, $restaurantId, $numero); 
                
                if ($isCreated) {
                    $_SESSION['success_message'] = "Столик '{$numero}' (вместимость {$capacity}) успешно добавлен.";
                    header('Location: ?route=table/manage&restaurant_id=' . $restaurantId);
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout d'une table à la base de données.";
                }
            } else {
                $error = "Veuillez entrer un numéro/nom et une capacité de table valides.";
            }
        }
        
        $tables = $tableModel->getTablesByRestaurantId($restaurantId);
        
        require_once __DIR__ . '/../views/table/manage.php';
    }
}