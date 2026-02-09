<?php

class CommentRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function add($productId, $userId, $comment) {
        $sql = "INSERT INTO product_comments (product_id, user_id, comment) VALUES (:pid, :uid, :comment)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':pid' => $productId,
            ':uid' => $userId,
            ':comment' => $comment
        ]);
        return $this->conn->lastInsertId();
    }

    public function getAllByProductId($productId) {
        $sql = "SELECT c.*, u.name as user_name 
                FROM product_comments c
                JOIN User u ON c.user_id = u.id
                WHERE c.product_id = :pid
                ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':pid' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}