<?php
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Product Details</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '_navbar.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="product-card">
                <div class="image-holder">
                    <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h1 class="card-title text-uppercase"><?php echo $product['name']; ?></h1>
            <p class="text-muted"><?php echo nl2br($product['description']); ?></p>
            <span class="item-price text-primary h2">$<?php echo $product['price']; ?></span>
            <form method="POST" action="cart.php" class="mt-4">
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
            <!-- Yorumları buraya çekebilirsiniz -->
            <div class="review">
                <p>No reviews yet. Be the first to review this product!</p>
            </div>
        </div>
    </div>
</div>

<!-- Optional JavaScript; choose one of the two! -->
<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
