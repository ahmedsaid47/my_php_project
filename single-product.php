<?php
session_start();
include 'db.php';

// Ürün ID'sini al
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Ürün bilgilerini al
$product_query = "SELECT * FROM products WHERE product_id = $product_id";
$product_result = $conn->query($product_query);
$product = $product_result->fetch_assoc();

if (!$product) {
    echo "Product not found!";
    exit;
}

// Add to cart işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Kullanıcının sepetini kontrol et veya oluştur
    $sql = "SELECT cart_id FROM carts WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Sepet yoksa yeni bir sepet oluştur
        $sql = "INSERT INTO carts (user_id) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $cart_id = $stmt->insert_id;
    } else {
        // Mevcut sepeti al
        $cart = $result->fetch_assoc();
        $cart_id = $cart['cart_id'];
    }
    
    // Ürünü sepete ekle
    $sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $cart_id, $product_id, $quantity);
    $stmt->execute();
    $_SESSION['cart_count'] += $quantity;

    header("Location: single-product.php?product_id=$product_id");
    exit;
}

// Kullanıcı yorumu ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_review"])) {
    $user_id = $_SESSION['user_id'];
    $review_text = $_POST['review_text'];
    $rating = $_POST['rating'];
    
    $sql = "INSERT INTO product_reviews (product_id, user_id, review_text, rating) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iisi', $product_id, $user_id, $review_text, $rating);
    $stmt->execute();

    header("Location: single-product.php?product_id=$product_id");
    exit;
}

// Ürün yorumlarını al
$reviews_query = "SELECT pr.*, u.username FROM product_reviews pr JOIN users u ON pr.user_id = u.id WHERE pr.product_id = $product_id ORDER BY pr.created_at DESC";
$reviews_result = $conn->query($reviews_query);
$reviews = $reviews_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<?php include('partials/_header.php') ?>

<body data-bs-spy="scroll" data-bs-target="#navbar" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" tabindex="0">

<?php include('partials/_svg.php') ?>

<?php include 'partials/_navbar.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="product-card position-relative">
                <div class="image-holder">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid">
                </div>
                <div class="cart-concern">
                    <button class="btn btn-black">Add to Cart</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h1 class="card-title text-uppercase"><?php echo $product['name']; ?></h1>
            <p class="text-muted"><?php echo nl2br($product['description']); ?></p>
            <span class="item-price text-primary h2">$<?php echo $product['price']; ?></span>
            <form method="POST" class="mt-4">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control w-25">
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-black mt-2">Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Ekstra Ürün Bilgileri ve İncelemeler -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-uppercase">Product Details</h3>
            <table class="table table-striped mt-3">
                <tbody>
                    <tr>
                        <th scope="row">Category</th>
                        <td><?php echo $product['category']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Created At</th>
                        <td><?php echo $product['created_at']; ?></td>
                    </tr>
                    <!-- Daha fazla ürün detayı ekleyebilirsiniz -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Yorumlar -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-uppercase">Customer Reviews</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" class="mb-4">
                    <div class="form-group">
                        <label for="review_text">Write your review:</label>
                        <textarea id="review_text" name="review_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating:</label>
                        <select id="rating" name="rating" class="form-control w-25">
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-black mt-2">Submit Review</button>
                </form>
            <?php else: ?>
                <p>Please <a href="login.php">login</a> to write a review.</p>
            <?php endif; ?>

            <!-- Yorumları Göster -->
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review mb-3 p-3 bg-light">
                        <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                        <span class="text-muted"> - <?php echo $review['created_at']; ?></span>
                        <div><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></div>
                        <div class="text-warning">Rating: <?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to review this product!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Optional JavaScript; choose one of the two! -->
<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
