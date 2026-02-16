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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --bg-light: #f4f6f8;
            --text-dark: #343a40;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        }
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; background-color: var(--bg-light); color: var(--text-dark); }
        .container { padding: 40px 20px; max-width: 1100px; margin: 0 auto; }
        
        .back-link { display: inline-flex; align-items: center; margin-bottom: 20px; text-decoration: none; color: var(--text-muted); font-weight: 500; transition: color 0.2s; }
        .back-link:hover { color: var(--primary); }

        .detail-card { background: white; border-radius: 16px; box-shadow: var(--shadow-md); overflow: hidden; display: flex; flex-wrap: wrap; border: 1px solid var(--border-color); }
        
        .gallery { flex: 1; min-width: 350px; padding: 30px; background-color: #fff; }
        .main-img { width: 100%; height: 400px; object-fit: contain; border-radius: 12px; margin-bottom: 15px; background-color: #f8f9fa; }
        .thumbnails { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
        .thumbnails img { width: 70px; height: 70px; object-fit: cover; border: 2px solid transparent; border-radius: 8px; cursor: pointer; opacity: 0.7; transition: all 0.2s; }
        .thumbnails img:hover, .thumbnails img.active { opacity: 1; border-color: var(--primary); transform: translateY(-2px); }

        .info { flex: 1; min-width: 350px; padding: 40px; border-left: 1px solid var(--border-color); }
        .info h1 { margin-top: 0; font-size: 2rem; font-weight: 700; color: var(--text-dark); line-height: 1.2; }
        
        .price-tag { display: flex; align-items: center; margin: 15px 0; }
        .price { font-size: 2rem; color: var(--primary); font-weight: 700; }
        .discount { color: var(--danger); text-decoration: line-through; font-size: 1.1rem; margin-left: 12px; opacity: 0.8; }
        
        .meta { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); }
        .meta-item { display: flex; align-items: center; gap: 8px; color: var(--text-muted); font-size: 0.95rem; }
        .meta-item strong { color: var(--text-dark); }
        
        .description { margin-bottom: 30px; line-height: 1.7; color: #4a5568; }
        .description h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 10px; color: var(--text-dark); }
        
        .seller-box { background-color: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid var(--border-color); }
        .seller-box h3 { margin: 0 0 15px; font-size: 1.1rem; font-weight: 600; }

        .comments-section { margin-top: 40px; }
        .comments-section h3 { font-size: 1.3rem; font-weight: 700; margin-bottom: 20px; }
        .comment-item { background: white; padding: 20px; border-radius: 12px; margin-bottom: 15px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .comment-user { font-weight: 600; color: var(--text-dark); }
        .comment-date { font-size: 0.85rem; color: var(--text-muted); }
        
        textarea { width: 100%; padding: 15px; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; resize: vertical; transition: border-color 0.2s; }
        textarea:focus { outline: none; border-color: var(--primary); }
        .btn-submit { background-color: var(--primary); color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; margin-top: 10px; transition: background 0.2s; }
        .btn-submit:hover { background-color: var(--primary-dark); }
        
        .like-btn { background: none; border: none; cursor: pointer; font-size: 1.8rem; transition: transform 0.2s; padding: 0; }
        .like-btn:hover { transform: scale(1.1); }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
        <a href="javascript:history.back()" class="back-link">&larr; Back to Products</a>

        <div class="detail-card">
            <!-- Image Gallery -->
            <div class="gallery">
                <img id="mainImage" src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" class="main-img" alt="Main Image">
                <div class="thumbnails">
                    <img src="../uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" onclick="changeImage(this.src)">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <?php if(!empty($product['image'.$i])): ?>
                            <img src="../uploads/products/<?php echo htmlspecialchars($product['image'.$i]); ?>" onclick="changeImage(this.src)">
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="info">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <h1 style="margin-top: 0;"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <form action="../controllers/like.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="like-btn" style="color: <?php echo $hasLiked ? '#e91e63' : '#ccc'; ?>;" title="<?php echo $hasLiked ? 'Unlike' : 'Like'; ?>">
                            <?php echo $hasLiked ? '♥' : '♡'; ?> <span style="font-size: 1rem; color: #333;"><?php echo $likeCount; ?></span>
                        </button>
                    </form>
                </div>

                <div class="price-tag">
                    <span class="price">
                    $<?php echo number_format($product['prices'], 2); ?>
                    </span>
                    <?php if($product['discounts'] > 0): ?>
                        <span class="discount">$<?php echo number_format($product['prices'] + $product['discounts'], 2); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="meta">
                    <div class="meta-item"><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></div>
                    <div class="meta-item"><strong>Location:</strong> <?php echo htmlspecialchars($product['location']); ?></div>
                    <div class="meta-item"><strong>Posted:</strong> <?php echo date('d M Y', strtotime($product['created_at'])); ?></div>
                </div>

                <div class="description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="seller-box">
                    <h3>Seller Contact</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($product['owner_name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($product['phone1']); ?></p>
                    <?php if(!empty($product['phone2'])): ?>
                        <p><strong>Phone 2:</strong> <?php echo htmlspecialchars($product['phone2']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <h3>Comments</h3>
                    
                    <?php if(empty($comments)): ?>
                        <p style="color: #666; font-style: italic;">No comments yet.</p>
                    <?php else: ?>
                        <?php foreach($comments as $cmt): ?>
                            <div class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-user"><?php echo htmlspecialchars($cmt['user_name']); ?></span>
                                    <span class="comment-date"><?php echo date('d M Y H:i', strtotime($cmt['created_at'])); ?></span>
                                </div>
                                <p style="margin: 5px 0 0;"><?php echo nl2br(htmlspecialchars($cmt['comment'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="../controllers/comment.php" method="POST" style="margin-top: 20px;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <textarea name="comment" rows="3" placeholder="Write a comment..." required></textarea>
                            <button type="submit" name="add_comment" class="btn-submit">Post Comment</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>
</body>
</html>