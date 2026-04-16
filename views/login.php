<?php session_start(); ?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ចូលប្រើ - Sana</title>
    <link rel="icon" href="../icon/e-commerce-logo.png" sizes="any" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Moul&family=Hanuman:wght@100;400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3325;
            --primary-container: #2a5038;
            --primary-light: rgba(26, 51, 37, 0.08);
            --secondary: #9d7c39;
            --secondary-light: rgba(157, 124, 57, 0.12);
            --tertiary: #7e000a;
            --bg-body: #faf7f2;
            --surface: #ffffff;
            --on-surface: #201b09;
            --on-surface-variant: #6b6355;
            --outline: rgba(74, 69, 56, 0.12);
            --outline-strong: rgba(74, 69, 56, 0.25);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            
            /* Khmer Typographic Recalibration */
            --font-headline: 'Moul', serif;
            --font-body: 'Hanuman', serif;
            --lh-body: 1.9;
            --lh-headline: 1.5;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-body);
            color: var(--on-surface);
            min-height: 100vh;
            display: flex;
            line-height: var(--lh-body);
            font-size: 1.05rem;
        }

        /* Left: Brand Panel */
        .brand-panel {
            flex: 1;
            background: linear-gradient(160deg, var(--primary) 0%, #0f1f16 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 280px;
            height: 280px;
            background: var(--secondary);
            border-radius: 50%;
            opacity: 0.12;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -40px;
            width: 200px;
            height: 200px;
            background: var(--primary-container);
            border-radius: 50%;
            opacity: 0.2;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 360px;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
        }

        .brand-logo svg { width: 32px; height: 32px; color: #fff; }

        .brand-content h1 {
            font-family: var(--font-headline);
            font-size: 2rem;
            font-weight: 400;
            color: #ffffff;
            margin: 0 0 0.75rem;
            line-height: var(--lh-headline);
        }

        .brand-content p {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.8;
            margin: 0;
        }

        /* Right: Form Panel */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-header .welcome-text {
            font-family: var(--font-headline);
            font-size: 1.5rem;
            font-weight: 400;
            color: var(--primary);
            margin: 0 0 0.5rem;
            line-height: var(--lh-headline);
        }

        .login-header .subtitle {
            font-size: 0.95rem;
            color: var(--on-surface-variant);
            margin: 0;
        }

        /* Error Message */
        .error-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
        }

        .error-alert svg {
            width: 18px;
            height: 18px;
            color: var(--tertiary);
            flex-shrink: 0;
            margin-top: 1px;
        }

        .error-alert p {
            font-size: 0.825rem;
            color: var(--tertiary);
            font-weight: 600;
            margin: 0;
        }

        /* Form Fields */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--on-surface-variant);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper svg.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--on-surface-variant);
            opacity: 0.4;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 44px 12px 44px;
            background: var(--surface);
            border: 1.5px solid var(--outline);
            border-radius: var(--radius-sm);
            font-size: 0.925rem;
            font-family: var(--font-body);
            color: var(--on-surface);
            outline: none;
            transition: all 0.2s;
        }

        .input-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .input-wrapper input:focus ~ svg.input-icon {
            opacity: 1;
            color: var(--primary);
        }

        .input-wrapper input::placeholder {
            color: rgba(107, 99, 85, 0.4);
        }

        /* Password Toggle */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--on-surface-variant);
            opacity: 0.4;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover { opacity: 0.7; }
        .toggle-password svg { width: 18px; height: 18px; display: block; }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: var(--font-headline);
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background: var(--primary-container);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1.5rem 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--outline);
        }

        .divider span {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--on-surface-variant);
            text-transform: uppercase;
        }

        /* Register Link */
        .register-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            color: var(--on-surface-variant);
            font-size: 0.875rem;
            font-weight: 500;
            padding: 10px;
            border-radius: var(--radius-sm);
            transition: all 0.2s;
        }

        .register-link:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .register-link svg { width: 16px; height: 16px; }

        /* Responsive */
        @media (max-width: 992px) {
            .brand-panel { display: none; }
            .form-panel { padding: 1.5rem; }
        }

        @media (max-width: 480px) {
            .form-panel { padding: 1.25rem; }
            .login-card { max-width: 100%; }
            .login-header .welcome-text { font-size: 1.35rem; }
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            color: #fff;
            font-weight: 600;
            font-size: 0.825rem;
            z-index: 9999;
            animation: toastIn 0.3s ease, toastOut 0.3s ease 2.7s forwards;
        }

        .toast-success { background: var(--primary); }
        .toast-error { background: var(--tertiary); }

        @keyframes toastIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes toastOut { from { opacity: 1; } to { opacity: 0; transform: translateY(10px); } }
    </style>
</head>
<body>
<?php
    $loginEmail = $_SESSION['login_email'] ?? '';
    unset($_SESSION['login_email']);
?>
    <!-- Left Brand Panel -->
    <div class="brand-panel">
        <div class="brand-content">
            <div class="brand-logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path></svg>
            </div>
            <h1>សូមស្វាគមន៍មកកាន់សាណា</h1>
            <p>ទីផ្សារដែលអាចទុកចិត្តបានរបស់អ្នកក្នុងការទិញ និងលក់អ្វីៗគ្រប់យ៉ាង។ ចូលរួមជាមួយអ្នកប្រើប្រាស់រាប់ពាន់នាក់ដែលកំពុងធ្វើការជួញដូរដោយសុវត្ថិភាព។</p>
        </div>
    </div>

    <!-- Right Form Panel -->
    <div class="form-panel">
        <div class="login-card">
            <div class="login-header">
                <h2 class="welcome-text">ចូលប្រើប្រាស់គណនីរបស់អ្នក</h2>
                <p class="subtitle">សូមបញ្ចូលព័ត៌មានសម្ងាត់របស់អ្នកដើម្បីបន្ត</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-alert">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p><?php echo htmlspecialchars($_GET['error']); ?></p>
                </div>
            <?php endif; ?>

            <form action="../controllers/auth.php" method="POST">
                <div class="form-group">
                    <label for="email">អាសយដ្ឋានអ៊ីមែល</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required placeholder="your@email.com" autocomplete="email" value="<?php echo htmlspecialchars($loginEmail); ?>">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">លេខសម្ងាត់</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="បញ្ចូលលេខសម្ងាត់របស់អ្នក" autocomplete="current-password">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <button type="button" class="toggle-password" onclick="togglePass()">
                            <svg id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="eyeIconOff" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-submit">ចូលប្រើប្រាស់</button>
            </form>

            <div class="divider">
                <span>ទើបមកដល់មែនទេ?</span>
            </div>

            <a href="register.php" class="register-link">
                បង្កើតគណនីថ្មី
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- Toast Notification -->
    <?php if (isset($_GET['success'])): ?>
        <div class="toast toast-success">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <script>
        function togglePass() {
            const input = document.getElementById('password');
            if (input.type === 'password') {
                input.type = 'text';
                document.getElementById('eyeIcon').style.display = 'none';
                document.getElementById('eyeIconOff').style.display = 'block';
            } else {
                input.type = 'password';
                document.getElementById('eyeIcon').style.display = 'block';
                document.getElementById('eyeIconOff').style.display = 'none';
            }
        }
    </script>
</body>
</html>
