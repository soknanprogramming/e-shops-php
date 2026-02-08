<?php

class ProductRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        // Join Product with product_image to get the main image filename
        $sql = "SELECT p.*, pi.main_image, c.name as category_name, u.name as owner_name 
                FROM Product p 
                LEFT JOIN product_image pi ON p.product_image_id = pi.id 
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN User u ON p.owner_id = u.id
                ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        // 1. Insert Image first to get the ID
        $sqlImg = "INSERT INTO product_image (main_image) VALUES (:main_image)";
        $stmtImg = $this->conn->prepare($sqlImg);
        $stmtImg->execute([':main_image' => $data['image']]);
        $imageId = $this->conn->lastInsertId();

        // 2. Insert Product
        // Note: We are using default IDs (1) for profile, liked, and comment to satisfy DB constraints for this example.
        $sql = "INSERT INTO Product (name, prices, category_id, owner_id, product_image_id, showed, profile_id, liked_id, comment_id) 
                VALUES (:name, :prices, :category_id, :owner_id, :product_image_id, 1, 1, 1, 1)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':prices' => $data['prices'],
            ':category_id' => $data['category_id'],
            ':owner_id' => $data['owner_id'],
            ':product_image_id' => $imageId
        ]);
        
        return $this->conn->lastInsertId();
    }
}