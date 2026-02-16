<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/UserRepository.php';
require_once '../repos/ProfileRepository.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userRepo = new UserRepository($conn);
$profileRepo = new ProfileRepository($conn);

$user = $userRepo->findById($_SESSION['user_id']);
$profile = $profileRepo->getByUserId($_SESSION['user_id']);

// Default values if profile doesn't exist yet
$phone1 = $profile['phone1'] ?? '';
$phone2 = $profile['phone2'] ?? '';
$bio = $profile['bio'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; min-height: 100vh; background-color: #f0f2f5; }
        .main-content { flex-grow: 1; padding: 20px; display: flex; justify-content: center; align-items: flex-start; }
        .profile-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 600px; margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
        button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background-color: #0056b3; }
        .success-msg { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .error-msg { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include './assets/user_sidebar.php'; ?>
    <div class="main-content">
        <div class="profile-container">
            <h2>My Profile</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="success-msg"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>

            <form action="../controllers/profile.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Profile Picture</label>
                    <?php if(!empty($profile['user_image'])): ?>
                        <img src="../uploads/profiles/<?php echo htmlspecialchars($profile['user_image']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; display: block; margin-bottom: 10px;">
                    <?php endif; ?>
                    <input type="file" name="user_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Background Image</label>
                    <?php if(!empty($profile['background_image'])): ?>
                        <img src="../uploads/profiles/<?php echo htmlspecialchars($profile['background_image']); ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: 4px; display: block; margin-bottom: 10px;">
                    <?php endif; ?>
                    <input type="file" name="background_image" accept="image/*">
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email (Cannot be changed)</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background-color: #e9ecef;">
                </div>

                <div class="form-group">
                    <label for="phone1">Phone Number 1 (Required)</label>
                    <input type="text" name="phone1" value="<?php echo htmlspecialchars($phone1); ?>" required placeholder="012345678">
                </div>

                <div class="form-group">
                    <label for="phone2">Phone Number 2 (Optional)</label>
                    <input type="text" name="phone2" value="<?php echo htmlspecialchars($phone2); ?>" placeholder="012345678">
                </div>

                <div class="form-group">
                    <label for="bio">Bio / About Me</label>
                    <textarea name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($bio); ?></textarea>
                </div>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>