<?php

class ProfileRepository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getByUserId($userId) {
        $sql = "SELECT * FROM user_profile WHERE user_id = :uid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($userId, $data) {
        // Check if profile exists
        $existing = $this->getByUserId($userId);

        if ($existing) {
            $sql = "UPDATE user_profile SET phone1 = :p1, phone2 = :p2, bio = :bio, user_image = :uimg, background_image = :bimg WHERE user_id = :uid";
        } else {
            $sql = "INSERT INTO user_profile (user_id, phone1, phone2, bio, user_image, background_image) VALUES (:uid, :p1, :p2, :bio, :uimg, :bimg)";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':uid' => $userId,
            ':p1' => $data['phone1'],
            ':p2' => $data['phone2'] ?? null,
            ':bio' => $data['bio'] ?? null,
            ':uimg' => $data['user_image'] ?? null,
            ':bimg' => $data['background_image'] ?? null
        ]);
    }
}