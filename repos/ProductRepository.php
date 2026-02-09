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

    public function search($params = []) {
        $sql = "SELECT p.*, pi.main_image, c.name as category_name, u.name as owner_name 
                FROM Product p 
                LEFT JOIN product_image pi ON p.product_image_id = pi.id 
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN User u ON p.owner_id = u.id
                WHERE 1=1";
        
        $args = [];

        if (!empty($params['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $args[':category_id'] = $params['category_id'];
        }

        if (!empty($params['min_price'])) {
            $sql .= " AND p.prices >= :min_price";
            $args[':min_price'] = $params['min_price'];
        }

        if (!empty($params['max_price'])) {
            $sql .= " AND p.prices <= :max_price";
            $args[':max_price'] = $params['max_price'];
        }

        if (!empty($params['has_discount'])) {
            $sql .= " AND p.discounts > 0";
        }

        if (!empty($params['name'])) {
            $sql .= " AND p.name LIKE :name";
            $args[':name'] = '%' . $params['name'] . '%';
        }

        if (!empty($params['location'])) {
            $sql .= " AND p.location LIKE :location";
            $args[':location'] = '%' . $params['location'] . '%';
        }

        if (!empty($params['seller'])) {
            $sql .= " AND u.name LIKE :seller";
            $args[':seller'] = '%' . $params['seller'] . '%';
        }

        if (isset($params['sort']) && $params['sort'] === 'oldest') {
            $sql .= " ORDER BY p.id ASC";
        } else {
            $sql .= " ORDER BY p.id DESC";
        }

        if (isset($params['limit']) && isset($params['offset'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->conn->prepare($sql);
        
        foreach ($args as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        if (isset($params['limit']) && isset($params['offset'])) {
            $stmt->bindValue(':limit', (int) $params['limit'], PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $params['offset'], PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countSearch($params = []) {
        $sql = "SELECT COUNT(*) as total 
                FROM Product p 
                LEFT JOIN User u ON p.owner_id = u.id
                WHERE 1=1";
        
        $args = [];

        if (!empty($params['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $args[':category_id'] = $params['category_id'];
        }

        if (!empty($params['min_price'])) {
            $sql .= " AND p.prices >= :min_price";
            $args[':min_price'] = $params['min_price'];
        }

        if (!empty($params['max_price'])) {
            $sql .= " AND p.prices <= :max_price";
            $args[':max_price'] = $params['max_price'];
        }

        if (!empty($params['has_discount'])) {
            $sql .= " AND p.discounts > 0";
        }

        if (!empty($params['name'])) {
            $sql .= " AND p.name LIKE :name";
            $args[':name'] = '%' . $params['name'] . '%';
        }

        if (!empty($params['location'])) {
            $sql .= " AND p.location LIKE :location";
            $args[':location'] = '%' . $params['location'] . '%';
        }

        if (!empty($params['seller'])) {
            $sql .= " AND u.name LIKE :seller";
            $args[':seller'] = '%' . $params['seller'] . '%';
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($args);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getByCategoryId($categoryId) {
        $sql = "SELECT p.*, pi.main_image, c.name as category_name, u.name as owner_name 
                FROM Product p 
                LEFT JOIN product_image pi ON p.product_image_id = pi.id 
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN User u ON p.owner_id = u.id
                WHERE p.category_id = :category_id
                ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByOwnerId($ownerId) {
        $sql = "SELECT p.*, pi.main_image, c.name as category_name 
                FROM Product p 
                LEFT JOIN product_image pi ON p.product_image_id = pi.id 
                LEFT JOIN category c ON p.category_id = c.id
                WHERE p.owner_id = :owner_id
                ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':owner_id' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT p.*, 
                       pi.main_image, pi.image1, pi.image2, pi.image3, pi.image4, pi.image5,
                       c.name as category_name, 
                       u.name as owner_name,
                       up.phone1, up.phone2
                FROM Product p 
                LEFT JOIN product_image pi ON p.product_image_id = pi.id 
                LEFT JOIN category c ON p.category_id = c.id
                LEFT JOIN User u ON p.owner_id = u.id
                LEFT JOIN user_profile up ON p.profile_id = up.id
                WHERE p.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        // 1. Insert Image first to get the ID
        $sqlImg = "INSERT INTO product_image (main_image, image1, image2, image3, image4, image5) 
                   VALUES (:main_image, :image1, :image2, :image3, :image4, :image5)";
        $stmtImg = $this->conn->prepare($sqlImg);
        $stmtImg->execute([
            ':main_image' => $data['image'],
            ':image1' => $data['image1'] ?? null,
            ':image2' => $data['image2'] ?? null,
            ':image3' => $data['image3'] ?? null,
            ':image4' => $data['image4'] ?? null,
            ':image5' => $data['image5'] ?? null
        ]);
        $imageId = $this->conn->lastInsertId();

        // 2. Get or Create Profile ID (Fix for Foreign Key Constraint)
        $stmtProfile = $this->conn->prepare("SELECT id FROM user_profile WHERE user_id = :uid");
        $stmtProfile->execute([':uid' => $data['owner_id']]);
        $profileData = $stmtProfile->fetch(PDO::FETCH_ASSOC);
        
        if ($profileData) {
            $profileId = $profileData['id'];
        } else {
            // Create default profile if not exists (phone1 is required)
            $sqlCreateProfile = "INSERT INTO user_profile (user_id, phone1) VALUES (:uid, '012345678')";
            $stmtCreateProfile = $this->conn->prepare($sqlCreateProfile);
            $stmtCreateProfile->execute([':uid' => $data['owner_id']]);
            $profileId = $this->conn->lastInsertId();
        }

        // 3. Ensure Liked/Comment IDs exist (Fix for Foreign Key Constraint)
        $likedId = 1;
        $stmtCheckLiked = $this->conn->query("SELECT id FROM liked WHERE id = 1");
        if (!$stmtCheckLiked->fetch()) {
             $this->conn->query("INSERT INTO liked (user_id) VALUES ({$data['owner_id']})");
             $likedId = $this->conn->lastInsertId();
        }

        $commentId = 1;
        $stmtCheckComment = $this->conn->query("SELECT id FROM comment WHERE id = 1");
        if (!$stmtCheckComment->fetch()) {
             $this->conn->query("INSERT INTO comment (user_id, comment) VALUES ({$data['owner_id']}, 'No comments')");
             $commentId = $this->conn->lastInsertId();
        }

        // 4. Insert Product
        $sql = "INSERT INTO Product (name, prices, discounts, category_id, owner_id, product_image_id, location, description, showed, profile_id, liked_id, comment_id) 
                VALUES (:name, :prices, :discounts, :category_id, :owner_id, :product_image_id, :location, :description, 1, :profile_id, :liked_id, :comment_id)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':prices' => $data['prices'],
            ':discounts' => $data['discounts'],
            ':category_id' => $data['category_id'],
            ':owner_id' => $data['owner_id'],
            ':product_image_id' => $imageId,
            ':location' => $data['location'],
            ':description' => $data['description'],
            ':profile_id' => $profileId,
            ':liked_id' => $likedId,
            ':comment_id' => $commentId
        ]);
        
        return $this->conn->lastInsertId();
    }
}