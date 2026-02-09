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
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { padding: 20px; max-width: 1000px; margin: 0 auto; }
        
        .detail-card { background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-wrap: wrap; }
        
        .gallery { flex: 1; min-width: 300px; padding: 20px; }
        .main-img { width: 100%; height: 400px; object-fit: contain; border: 1px solid #eee; border-radius: 4px; margin-bottom: 10px; }
        .thumbnails { display: flex; gap: 10px; overflow-x: auto; }
        .thumbnails img { width: 60px; height: 60px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; opacity: 0.7; }
        .thumbnails img:hover { opacity: 1; border-color: #007bff; }

        .info { flex: 1; min-width: 300px; padding: 20px; border-left: 1px solid #eee; }
        .info h1 { margin-top: 0; color: #333; }
        .price { font-size: 1.5rem; color: #28a745; font-weight: bold; margin: 10px 0; }
        .discount { color: #dc3545; text-decoration: line-through; font-size: 1rem; margin-left: 10px; }
        .meta { color: #6c757d; font-size: 0.9rem; margin-bottom: 20px; }
        .meta span { margin-right: 15px; }
        
        .description { margin-top: 20px; line-height: 1.6; color: #555; }
        
        .seller-box { background-color: #f1f3f5; padding: 15px; border-radius: 8px; margin-top: 30px; }
        .seller-box h3 { margin-top: 0; font-size: 1.1rem; }
        .contact-btn { display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 10px; }

        .comments-section { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
        .comment-item { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .comment-user { font-weight: bold; font-size: 0.9rem; color: #333; }
        .comment-date { font-size: 0.8rem; color: #888; margin-left: 10px; }
    </style>
</head>
<body>
    <?php include './assets/topbar.php'; ?>

    <div class="container">
        <a href="javascript:history.back()" style="display: inline-block; margin-bottom: 15px; text-decoration: none; color: #6c757d;">&larr; Back</a>

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
                        <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 1.5rem; color: <?php echo $hasLiked ? '#e91e63' : '#ccc'; ?>;" title="<?php echo $hasLiked ? 'Unlike' : 'Like'; ?>">
                            <?php echo $hasLiked ? '♥' : '♡'; ?> <span style="font-size: 1rem; color: #333;"><?php echo $likeCount; ?></span>
                        </button>
                    </form>
                </div>

                <div class="price">
                    $<?php echo number_format($product['prices'], 2); ?>
                    <?php if($product['discounts'] > 0): ?>
                        <span class="discount">$<?php echo number_format($product['prices'] + $product['discounts'], 2); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="meta">
                    <span><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></span>
                    <span><strong>Location:</strong> <?php echo htmlspecialchars($product['location']); ?></span>
                    <span><strong>Posted:</strong> <?php echo date('d M Y', strtotime($product['created_at'])); ?></span>
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
                    <a href="tel:<?php echo htmlspecialchars($product['phone1']); ?>" class="contact-btn">Call Now</a>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <h3>Comments</h3>
                    
                    <?php if(empty($comments)): ?>
                        <p style="color: #666; font-style: italic;">No comments yet.</p>
                    <?php else: ?>
                        <?php foreach($comments as $cmt): ?>
                            <div class="comment-item">
                                <span class="comment-user"><?php echo htmlspecialchars($cmt['user_name']); ?></span>
                                <span class="comment-date"><?php echo date('d M Y H:i', strtotime($cmt['created_at'])); ?></span>
                                <p style="margin: 5px 0 0;"><?php echo nl2br(htmlspecialchars($cmt['comment'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="../controllers/comment.php" method="POST" style="margin-top: 20px;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <textarea name="comment" rows="3" placeholder="Write a comment..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;" required></textarea>
                            <button type="submit" name="add_comment" style="margin-top: 10px; background-color: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Post Comment</button>
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