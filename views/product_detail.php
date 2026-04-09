<?php
session_start();
require_once '../configs/connect.php';
require_once '../repos/ProductRepository.php';
require_once '../repos/LikeRepository.php';
require_once '../repos/CommentRepository.php';

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$productRepo = new ProductRepository($conn);
$product = $productRepo->getById($_GET['id']);

if (!$product) {
    echo "Product not found.";
    exit();
}

$likeRepo = new LikeRepository($conn);
$likeCount = $likeRepo->getCount($product['id']);
$hasLiked = isset($_SESSION['user_id']) ? $likeRepo->hasLiked($_SESSION['user_id'], $product['id']) : false;

$commentRepo = new CommentRepository($conn);
$comments = $commentRepo->getAllByProductId($product['id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3325;
            --primary-container: #2a5038;
            --primary-light: rgba(26, 51, 37, 0.05);
            --secondary: #9d7c39;
            --secondary-light: rgba(157, 124, 57, 0.1);
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
            --font-headline: 'Manrope', sans-serif;
            --font-body: 'Public Sans', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            margin: 0;
            padding: 0;
            background-color: var(--bg-body);
            color: var(--on-surface);
            line-height: 1.6;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 2rem 4rem;
        }

        @media (max-width: 768px) {
            .container { padding: 1rem 1rem 3rem; }
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.8rem;
            color: var(--on-surface-variant);
        }

        .breadcrumb a {
            color: var(--on-surface-variant);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb a:hover { color: var(--primary); }

        .breadcrumb .sep { opacity: 0.4; }

        /* Product Layout */
        .product-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 3rem;
            align-items: start;
        }

        @media (max-width: 992px) {
            .product-layout { grid-template-columns: 1fr; gap: 2rem; }
        }

        /* Gallery */
        .gallery-area {
            position: relative;
        }

        .main-image {
            width: 100%;
            aspect-ratio: 4 / 3;
            background: var(--surface);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .discount-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: var(--tertiary);
            color: #fff;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            z-index: 2;
        }

        .thumbnails {
            display: flex;
            gap: 8px;
            overflow-x: auto;
        }

        .thumb {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            cursor: pointer;
            border: 2px solid transparent;
            opacity: 0.5;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .thumb:hover, .thumb.active {
            border-color: var(--primary);
            opacity: 1;
        }

        /* Sidebar Panel */
        .info-panel {
            background: var(--surface);
            border: 1px solid var(--outline);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            position: sticky;
            top: 80px;
        }

        .product-title {
            font-family: var(--font-headline);
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--on-surface);
            margin: 0 0 0.75rem;
            line-height: 1.3;
        }

        .price-block {
            display: flex;
            align-items: baseline;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .current-price {
            font-family: var(--font-headline);
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
        }

        .original-price {
            font-size: 1rem;
            color: var(--on-surface-variant);
            text-decoration: line-through;
            opacity: 0.5;
        }

        /* Meta Info */
        .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 1.25rem;
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            background: var(--bg-body);
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--on-surface-variant);
        }

        .meta-chip svg { width: 12px; height: 12px; opacity: 0.4; }

        /* Description */
        .desc-block {
            margin-bottom: 1.25rem;
        }

        .desc-block .section-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--on-surface-variant);
            margin: 0 0 0.5rem;
        }

        .desc-text {
            font-size: 0.875rem;
            line-height: 1.65;
            color: var(--on-surface-variant);
            margin: 0;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: var(--outline);
            margin: 1.25rem 0;
        }

        /* Seller Block */
        .seller-block {
            padding: 14px;
            background: var(--bg-body);
            border-radius: var(--radius-sm);
        }

        .seller-block .seller-name {
            font-family: var(--font-headline);
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0 0 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--outline);
        }

        .seller-phones {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .phone-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            background: var(--surface);
            border-radius: 6px;
            font-size: 0.825rem;
            color: var(--on-surface);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .phone-link:hover { background: var(--primary-light); color: var(--primary); }
        .phone-link svg { width: 14px; height: 14px; flex-shrink: 0; opacity: 0.6; }

        /* Like Button */
        .like-block {
            margin-bottom: 1rem;
        }

        .like-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--on-surface-variant);
            transition: all 0.2s;
            padding: 8px 10px;
            border-radius: 6px;
        }

        .like-btn:hover { background: var(--primary-light); }
        .like-btn.liked { color: var(--tertiary); }
        .like-btn.liked svg { fill: var(--tertiary); }
        .like-btn svg { width: 20px; height: 20px; }

        /* Admin Badge */
        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            background: var(--secondary-light);
            border-radius: 4px;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .admin-badge svg { width: 12px; height: 12px; }

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

        /* Comments Section */
        .comments-area {
            margin-top: 3rem;
        }

        .comments-area h3 {
            font-family: var(--font-headline);
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 1.25rem;
            color: var(--on-surface);
        }

        .comment-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--outline);
        }

        .comment-item:last-of-type { border-bottom: none; }

        .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .comment-avatar svg { width: 14px; height: 14px; color: var(--primary); }

        .comment-body { flex-grow: 1; }

        .comment-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .comment-user {
            font-weight: 700;
            font-size: 0.825rem;
        }

        .comment-date {
            font-size: 0.7rem;
            color: var(--on-surface-variant);
        }

        .comment-text {
            margin: 0;
            font-size: 0.85rem;
            line-height: 1.6;
            color: var(--on-surface-variant);
        }

        .no-comments {
            text-align: center;
            padding: 2rem;
            color: var(--on-surface-variant);
            font-style: italic;
            font-size: 0.875rem;
        }

        .comment-form {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.25rem;
        }

        .comment-form textarea {
            flex-grow: 1;
            padding: 12px;
            background: var(--surface);
            border: 1.5px solid var(--outline);
            border-radius: var(--radius-sm);
            font-family: var(--font-body);
            font-size: 0.85rem;
            color: var(--on-surface);
            outline: none;
            resize: vertical;
            transition: border-color 0.2s;
        }

        .comment-form textarea:focus { border-color: var(--primary); }

        .comment-form button {
            padding: 12px 20px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.2s;
            align-self: flex-start;
        }

        .comment-form button:hover { background: var(--primary-container); }

        /* Mobile tweaks */
        @media (max-width: 768px) {
            .info-panel { position: static; }
            .main-image { aspect-ratio: 1 / 1; }
        }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="home.php">Home</a>
            <span class="sep">›</span>
            <a href="home.php?category_id=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
            <span class="sep">›</span>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <!-- Product Layout -->
        <div class="product-layout">
            <!-- Left: Gallery -->
            <div class="gallery-area">
                <?php
                $images = [];
                if (!empty($product['main_image'])) $images[] = $product['main_image'];
                for ($i = 1; $i <= 5; $i++) {
                    if (!empty($product['image' . $i])) $images[] = $product['image' . $i];
                }
                ?>

                <div class="main-image">
                    <img id="mainImage" src="../uploads/products/<?php echo htmlspecialchars(reset($images)); ?>" alt="Main Image">
                    <?php if ($product['discounts'] > 0): ?>
                        <?php
                        $originalPrice = $product['prices'] + $product['discounts'];
                        $discountPercent = round(($product['discounts'] / $originalPrice) * 100);
                        ?>
                        <div class="discount-badge">-<?php echo $discountPercent; ?>%</div>
                    <?php endif; ?>
                </div>

                <?php if (count($images) > 1): ?>
                    <div class="thumbnails">
                        <?php foreach ($images as $idx => $img): ?>
                            <img src="../uploads/products/<?php echo htmlspecialchars($img); ?>"
                                 class="thumb <?php echo $idx === 0 ? 'active' : ''; ?>"
                                 onclick="changeImage(this.src)"
                                 alt="Thumb">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Description (below gallery on desktop) -->
                <div class="desc-block" style="margin-top: 2rem;">
                    <h4 class="section-label">Description</h4>
                    <p class="desc-text"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <!-- Comments -->
                <div class="comments-area">
                    <h3>Comments (<?php echo count($comments); ?>)</h3>

                    <?php if (empty($comments)): ?>
                        <div class="no-comments">No comments yet. Be the first to share your thoughts!</div>
                    <?php else: ?>
                        <?php foreach ($comments as $cmt): ?>
                            <div class="comment-item">
                                <div class="comment-avatar">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div class="comment-body">
                                    <div class="comment-top">
                                        <span class="comment-user"><?php echo htmlspecialchars($cmt['user_name']); ?></span>
                                        <span class="comment-date"><?php echo date('d M Y, H:i', strtotime($cmt['created_at'])); ?></span>
                                    </div>
                                    <p class="comment-text"><?php echo nl2br(htmlspecialchars($cmt['comment'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="../controllers/comment.php" method="POST" class="comment-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <textarea name="comment" rows="3" placeholder="Write a comment..." required></textarea>
                            <button type="submit" name="add_comment">Post</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Info Panel -->
            <div class="info-panel">
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <div class="admin-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Admin
                        <?php if ($product['showed']): ?>
                            · Visible
                        <?php else: ?>
                            · Hidden
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="price-block">
                    <span class="current-price">$<?php echo number_format($product['prices'], 2); ?></span>
                    <?php if ($product['discounts'] > 0): ?>
                        <span class="original-price">$<?php echo number_format($product['prices'] + $product['discounts'], 2); ?></span>
                    <?php endif; ?>
                </div>

                <div class="meta-row">
                    <span class="meta-chip">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </span>
                    <span class="meta-chip">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <?php echo htmlspecialchars($product['location']); ?>
                    </span>
                    <span class="meta-chip">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <?php echo date('d M Y', strtotime($product['created_at'])); ?>
                    </span>
                </div>

                <div class="like-block">
                    <form action="../controllers/like.php" method="POST" style="margin:0;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="like-btn <?php echo $hasLiked ? 'liked' : ''; ?>">
                            <svg fill="<?php echo $hasLiked ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            <?php echo $likeCount; ?>
                        </button>
                    </form>
                </div>

                <div class="divider"></div>

                <!-- Seller Contact -->
                <h4 class="section-label">Seller Contact</h4>
                <div class="seller-block">
                    <p class="seller-name"><?php echo htmlspecialchars($product['owner_name']); ?></p>
                    <div class="seller-phones">
                        <a href="tel:<?php echo htmlspecialchars($product['phone1']); ?>" class="phone-link">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <?php echo htmlspecialchars($product['phone1']); ?>
                        </a>
                        <?php if (!empty($product['phone2'])): ?>
                            <a href="tel:<?php echo htmlspecialchars($product['phone2']); ?>" class="phone-link">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <?php echo htmlspecialchars($product['phone2']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="../controllers/product.php?action=toggle_visibility&id=<?php echo $product['id']; ?>&status=<?php echo $product['showed'] ? '0' : '1'; ?>&redirect=product_detail.php"
                       style="display: block; text-align: center; padding: 10px; background: <?php echo $product['showed'] ? 'var(--tertiary)' : 'var(--primary)'; ?>; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; margin-top: 1rem;">
                        <?php echo $product['showed'] ? 'Hide Product' : 'Show Product'; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
        <div class="toast <?php echo isset($_GET['success']) ? 'toast-success' : 'toast-error'; ?>">
            <?php echo htmlspecialchars($_GET['success'] ?? $_GET['error'] ?? ''); ?>
        </div>
    <?php endif; ?>

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumb').forEach(function(t) {
                t.classList.toggle('active', t.src === src);
            });
        }
    </script>
</body>
</html>
