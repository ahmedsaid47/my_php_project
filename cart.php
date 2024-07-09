<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '123', 'my_php_project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Kullanıcının sepetini al
$sql = "SELECT c.cart_id, p.name, p.price, p.image_url, ci.quantity 
        FROM carts c 
        JOIN cart_items ci ON c.cart_id = ci.cart_id 
        JOIN products p ON ci.product_id = p.product_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Ürün çıkarma işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_item"])) {
    $product_id = $_POST['product_id'];
    $sql = "DELETE FROM cart_items WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = ?) AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Ürün adedini artırma/azaltma işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_quantity"])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $sql = "UPDATE cart_items SET quantity = ? WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = ?) AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $quantity, $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include('partials/_header.php') ?>

<body>
    <?php include('partials/_navbar.php') ?>

    <div class="container mt-5">
        <h2 class="display-7 text-dark text-uppercase">Your Cart</h2>
        <div class="row">
            <?php if (count($cart_items) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $item['image_url']; ?>" alt="product" class="img-fluid" style="width: 50px;">
                                    <?php echo $item['name']; ?>
                                </td>
                                <td>$<?php echo $item['price']; ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                        <button type="submit" name="update_quantity" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                                <td>$<?php echo $item['price'] * $item['quantity']; ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include('partials/_footer.php') ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
