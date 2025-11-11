<?php


require_once __DIR__ . '/../config/Database.php';


abstract class AbstractManager {
    
   
    protected PDO $db; 

    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getPdo();
    }
}