<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f4f6f8; margin: 0; }
        .login-container { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; border: 1px solid #e9ecef; }
        h2 { text-align: center; margin-bottom: 30px; color: #1a202c; font-weight: 700; font-size: 1.8rem; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #4a5568; font-weight: 500; font-size: 0.95rem; }
        input { width: 100%; padding: 12px 15px; border: 1px solid #ced4da; border-radius: 8px; box-sizing: border-box; font-family: inherit; transition: all 0.2s; font-size: 1rem; }
        input:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }
        button { width: 100%; padding: 14px; background-color: #007bff; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        button:hover { background-color: #0056b3; }
        .error-msg { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; }
        .register-link { text-align: center; margin-top: 20px; display: block; color: #007bff; text-decoration: none; font-weight: 500; font-size: 0.95rem; }
        .register-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-msg">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="../controllers/auth.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <a href="register.php" class="register-link">Don't have an account? Register</a>
    </div>
</body>
</html>