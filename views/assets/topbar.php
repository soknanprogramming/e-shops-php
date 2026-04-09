<div class="header-fixed-container">
    <div class="navbar-main" id="navbarMain">
        <div class="container-nav">
            <div class="navbar-content">
                <div class="logo">
                    <a href="home.php">Sana</a>
                </div>

                <!-- Search Form -->
                <form action="home.php" method="GET" class="header-search">
                    <?php
                    foreach ($_GET as $key => $value) {
                        if ($key !== 'name' && $key !== 'page') {
                            echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
                        }
                    }
                    ?>
                    <div class="search-input-wrapper">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="name" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>">
                    </div>
                    <button type="submit">Search</button>
                </form>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>

                <div class="links" id="navLinks">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="user_dashboard.php">Dashboard</a>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <a href="admin.php">Admin</a>
                        <?php endif; ?>
                        <a href="product_create.php" class="btn-post">Post Product</a>
                        <a href="logout.php" class="btn-logout">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php" class="btn-register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        padding-top: 80px !important;
    }

    .navbar-main {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 70px;
        background-color: #1a3325;
        z-index: 1001;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .container-nav {
        max-width: 1440px;
        margin: 0 auto;
        padding: 0 6rem;
        width: 100%;
        height: 100%;
    }

    .navbar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
        width: 100%;
        height: 100%;
    }

    .logo a {
        color: #fef3d5;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.625rem;
        letter-spacing: -0.05em;
        transition: color 0.2s;
    }
    .logo a:hover { color: #ffffff; }

    .header-search {
        flex-grow: 1;
        max-width: 560px;
        display: flex;
        background: rgba(255,255,255,0.08);
        border-radius: 9999px;
        padding: 4px;
        border: 1px solid rgba(255,255,255,0.15);
        transition: all 0.2s;
    }
    .header-search:focus-within {
        background: rgba(255,255,255,0.12);
        border-color: rgba(255,255,255,0.3);
    }
    .search-input-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 16px;
        flex-grow: 1;
        color: rgba(255,255,255,0.5);
    }
    .header-search input {
        background: transparent;
        border: none;
        color: white;
        width: 100%;
        outline: none;
        font-size: 0.9rem;
        font-family: 'Public Sans', sans-serif;
    }
    .header-search input::placeholder { color: rgba(255,255,255,0.4); }
    .header-search button {
        background: #9d7c39;
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 9999px;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: background 0.2s;
    }
    .header-search button:hover { background: #b08f45; }

    .links {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-shrink: 0;
    }
    .links a {
        color: rgba(255, 255, 255, 0.75);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        transition: color 0.2s;
    }
    .links a:hover { color: #fef3d5; }

    .btn-post {
        background: #9d7c39 !important;
        color: #fff !important;
        padding: 8px 18px !important;
        border-radius: 8px !important;
    }
    .btn-post:hover { background: #b08f45 !important; }

    .btn-register {
        background: rgba(255,255,255,0.1) !important;
        color: #fef3d5 !important;
        padding: 8px 18px !important;
        border-radius: 8px !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
    }
    .btn-register:hover {
        background: rgba(255,255,255,0.15) !important;
        border-color: rgba(255,255,255,0.3) !important;
    }

    .btn-logout {
        opacity: 0.7;
    }
    .btn-logout:hover { opacity: 1; }

    .mobile-menu-btn {
        display: none;
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .container-nav { padding: 0 2rem; }
    }

    @media (max-width: 768px) {
        body { padding-top: 70px !important; }
        .navbar-main { height: auto; padding: 10px 0; }
        .navbar-content { flex-wrap: wrap; gap: 10px; }
        .header-search { order: 3; width: 100%; max-width: 100%; }
        .logo { order: 1; }
        .mobile-menu-btn { display: block; order: 2; }
        .links {
            display: none;
            width: 100%;
            order: 4;
            flex-direction: column;
            gap: 8px;
            padding: 12px 0 4px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .links.active { display: flex; }
        .links a {
            padding: 10px 12px;
            border-radius: 8px;
        }
        .links a:hover { background: rgba(255,255,255,0.05); }
        .btn-post, .btn-register {
            text-align: center;
        }
    }
</style>

<script>
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        document.getElementById('navLinks').classList.toggle('active');
    });
</script>