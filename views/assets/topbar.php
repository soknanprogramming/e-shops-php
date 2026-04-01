<div class="header-fixed-container">
    <!-- Tier 1: Main Nav (Always Visible at Top) -->
    <div class="navbar-main" id="navbarMain">
        <div class="container-nav">
            <div class="navbar-content">
                <div class="logo">
                    <a href="home.php">Sana</a>
                </div>
                
                <form action="home.php" method="GET" class="header-search">
                    <div class="search-input-wrapper">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="name" placeholder="Search nomenclature..." value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>">
                    </div>
                    <button type="submit">Search</button>
                </form>

                <div class="links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="user_dashboard.php">Dashboard</a>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <a href="admin.php" class="admin-link">Admin</a>
                        <?php endif; ?>
                        <a href="product_create.php" class="btn-post">New Entry</a>
                        <a href="logout.php" class="btn-logout">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php" class="btn-register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tier 2: Advanced Filters (Slides Behind Tier 1) -->
    <div class="filter-bar" id="filterBar">
        <div class="container-nav">
            <form action="home.php" method="GET" class="advanced-filters">
                <?php if(isset($_GET['name'])): ?>
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($_GET['name']); ?>">
                <?php endif; ?>
                <?php if(isset($_GET['category_id'])): ?>
                    <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($_GET['category_id']); ?>">
                <?php endif; ?>

                <div class="filter-item">
                    <label>Origin</label>
                    <input type="text" name="location" placeholder="City/Province" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
                </div>
                
                <div class="filter-item">
                    <label>Min $</label>
                    <input type="number" name="min_price" placeholder="0" value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">
                </div>

                <div class="filter-item">
                    <label>Max $</label>
                    <input type="number" name="max_price" placeholder="Any" value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
                </div>

                <div class="filter-item">
                    <label>Sort</label>
                    <select name="sort">
                        <option value="newest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest</option>
                        <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest</option>
                    </select>
                </div>

                <div class="filter-checkboxes">
                    <label class="check-label">
                        <input type="checkbox" name="has_discount" value="1" <?php echo isset($_GET['has_discount']) ? 'checked' : ''; ?>>
                        Offers
                    </label>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <label class="check-label">
                        <input type="checkbox" name="liked_only" value="1" <?php echo isset($_GET['liked_only']) ? 'checked' : ''; ?>>
                        Saved
                    </label>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-apply">Apply</button>
                <a href="home.php" class="btn-reset-header">Reset</a>
            </form>
        </div>
    </div>
</div>

<style>
    /* Always keep same padding to prevent body jumps */
    body {
        padding-top: 130px !important; 
    }

    .navbar-main {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 70px;
        background-color: #1a3325;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        z-index: 1001; /* Above Filter Bar */
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .filter-bar {
        position: fixed;
        top: 70px;
        left: 0;
        width: 100%;
        height: 50px;
        background-color: #1a3325;
        border-bottom: 2px solid #9d7c39;
        display: flex;
        align-items: center;
        z-index: 1000; /* Below Navbar Main */
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        /* Immediate transition if any */
        transition: transform 0.2s ease-in-out; 
    }

    /* Hide by sliding BEHIND the main nav */
    .filter-hidden {
        transform: translateY(-100%);
        pointer-events: none; /* Prevent clicks when hidden */
    }

    .container-nav {
        max-width: 1440px;
        margin: 0 auto;
        padding: 0 6rem;
        width: 100%;
    }
    @media (max-width: 1024px) { .container-nav { padding: 0 1.5rem; } }

    .navbar-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
        width: 100%;
    }

    .logo a { color: #fff; text-decoration: none; font-weight: 800; font-size: 1.5rem; letter-spacing: -0.05em; }

    .header-search {
        flex-grow: 1;
        max-width: 600px;
        display: flex;
        background: rgba(255,255,255,0.1);
        border-radius: 9999px;
        padding: 4px;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .header-search:focus-within { background: rgba(255,255,255,0.15); border-color: #fef3d5; }
    .search-input-wrapper { display: flex; align-items: center; gap: 10px; padding: 0 15px; flex-grow: 1; color: rgba(255,255,255,0.6); }
    .header-search input { background: transparent; border: none; color: white; width: 100%; outline: none; font-size: 0.9rem; }
    .header-search button { background: #9d7c39; color: white; border: none; padding: 8px 20px; border-radius: 9999px; cursor: pointer; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; }

    .advanced-filters { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; width: 100%; }
    .filter-item { display: flex; align-items: center; gap: 8px; }
    .filter-item label { color: rgba(255,255,255,0.8); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
    .filter-item input, .filter-item select { background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff; font-size: 0.85rem; padding: 6px 10px; border-radius: 4px; outline: none; max-width: 120px; }
    .filter-item select option { background-color: #1a3325; color: #ffffff; }
    
    .filter-checkboxes { display: flex; align-items: center; gap: 1rem; white-space: nowrap; }
    .check-label { color: #fef3d5; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px; cursor: pointer; }
    .check-label input { accent-color: #9d7c39; }

    .btn-apply { background: transparent; color: #fef3d5; border: 1px solid #9d7c39; padding: 4px 12px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .btn-apply:hover { background: #9d7c39; color: white; }
    .btn-reset-header { color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.75rem; font-weight: 600; }

    .links { display: flex; align-items: center; gap: 1.5rem; }
    .links a { color: rgba(255, 255, 255, 0.8); text-decoration: none; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; }
    .links a:hover { color: #fef3d5; }
    .btn-post { background: #9d7c39; color: #fff !important; padding: 8px 16px !important; border-radius: 4px !important; }

    @media (max-width: 768px) {
        .navbar-main { height: auto; padding: 1rem 0; }
        .navbar-content { flex-wrap: wrap; }
        .header-search { order: 3; max-width: 100%; }
        body { padding-top: 180px !important; }
    }
</style>

<script>
    (function() {
        let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const filterBar = document.getElementById('filterBar');

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // At the top, always show
            if (scrollTop <= 5) {
                filterBar.classList.remove('filter-hidden');
                lastScrollTop = scrollTop;
                return;
            }

            if (scrollTop > lastScrollTop) {
                // Scrolling Down -> Hide immediately
                if (!filterBar.classList.contains('filter-hidden')) {
                    filterBar.classList.add('filter-hidden');
                }
            } else {
                // Scrolling Up -> Show immediately
                if (filterBar.classList.contains('filter-hidden')) {
                    filterBar.classList.remove('filter-hidden');
                }
            }
            lastScrollTop = scrollTop;
        }, { passive: true });
    })();
</script>