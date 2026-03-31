<div class="navbar-wrapper">
    <!-- Tier 1: Main Nav -->
    <div class="navbar container-nav">
        <div class="logo">
            <a href="home.php">Sana</a>
        </div>
        
        <!-- Compact Search Bar in Header -->
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

    <!-- Tier 2: Advanced Filters (Only visible/meaningful for home.php, but global here) -->
    <div class="filter-bar">
        <div class="container-nav">
            <form action="home.php" method="GET" class="advanced-filters">
                <!-- Keep hidden name if it was in header search but user submits here -->
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
    .navbar-wrapper {
        background-color: #1a3325; /* Deep Forest Green */
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 2px solid #9d7c39; /* Sacred Gold */
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .container-nav {
        max-width: 1440px;
        margin: 0 auto;
        padding: 0 6rem;
    }
    @media (max-width: 1024px) {
        .container-nav { padding: 0 1.5rem; }
    }

    /* Tier 1 */
    .navbar { 
        height: 70px;
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        gap: 2rem;
    }
    .logo a { 
        color: #fff; 
        text-decoration: none; 
        font-weight: 800; 
        font-size: 1.5rem; 
        letter-spacing: -0.05em;
        flex-shrink: 0;
    }

    /* Header Search */
    .header-search {
        flex-grow: 1;
        max-width: 600px;
        display: flex;
        background: rgba(255,255,255,0.1);
        border-radius: 9999px;
        padding: 4px;
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s;
    }
    .header-search:focus-within {
        background: rgba(255,255,255,0.15);
        border-color: #fef3d5;
    }
    .search-input-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 15px;
        flex-grow: 1;
        color: rgba(255,255,255,0.6);
    }
    .header-search input {
        background: transparent;
        border: none;
        color: white;
        width: 100%;
        outline: none;
        font-size: 0.9rem;
    }
    .header-search input::placeholder { color: rgba(255,255,255,0.5); }
    .header-search button {
        background: #9d7c39; /* Sacred Gold */
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 9999px;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
    }

    /* Tier 2: Filter Bar */
    .filter-bar {
        background: rgba(0,0,0,0.1);
        padding: 8px 0;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .advanced-filters {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .filter-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-item label {
        color: rgba(255,255,255,0.6);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .filter-item input, .filter-item select {
        background: rgba(0, 0, 0, 0.2); /* Darker background for contrast */
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff; /* Bright white text */
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 4px;
        outline: none;
        max-width: 120px;
        transition: all 0.3s;
    }
    .filter-item input:focus, .filter-item select:focus {
        background: rgba(0, 0, 0, 0.4);
        border-color: #9d7c39;
    }
    /* Style options specifically for better visibility in some browsers */
    .filter-item select option {
        background-color: #1a3325;
        color: #ffffff;
    }
    .filter-checkboxes {
        display: flex;
        gap: 1rem;
    }
    .check-label {
        color: #fef3d5;
        font-size: 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .check-label input { accent-color: #9d7c39; }

    .btn-apply {
        background: transparent;
        color: #fef3d5;
        border: 1px solid #9d7c39;
        padding: 4px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        transition: all 0.3s;
    }
    .btn-apply:hover { background: #9d7c39; color: white; }
    .btn-reset-header {
        color: rgba(255,255,255,0.5);
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .links { display: flex; align-items: center; gap: 1.5rem; flex-shrink: 0; }
    .links a { 
        color: rgba(255, 255, 255, 0.8); 
        text-decoration: none; 
        font-weight: 600; 
        font-size: 0.8rem; 
        transition: all 0.3s ease;
        text-transform: uppercase;
    }
    .links a:hover { color: #fef3d5; }
    
    .btn-post { 
        background: #9d7c39; 
        color: #fff !important; 
        padding: 8px 16px !important; 
        border-radius: 4px !important; 
    }
    .admin-link { color: #fef3d5 !important; }
    .btn-logout { opacity: 0.6; font-size: 0.75rem !important; }

    @media (max-width: 768px) {
        .navbar { height: auto; padding: 1rem 1.5rem; flex-wrap: wrap; }
        .header-search { order: 3; max-width: 100%; }
        .advanced-filters { gap: 0.8rem; }
    }
</style>