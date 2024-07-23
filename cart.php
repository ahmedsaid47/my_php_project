<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
  
    // Kullanıcının sepet ID'sini al
    $sql = "SELECT cart_id FROM carts WHERE user_id = $user_id";
    $result = $conn->query($sql);
  
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cart_id = $row['cart_id'];
  
        // Sepetteki ürün sayısını al
        $sql = "SELECT SUM(quantity) as cart_count FROM cart_items WHERE cart_id = $cart_id";
        $result = $conn->query($sql);
  
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['cart_count'] = $row['cart_count'];
        } else {
            $_SESSION['cart_count'] = 0;
        }
    } else {
        $_SESSION['cart_count'] = 0;
    }
  } else {
    $_SESSION['cart_count'] = 0;
  }
  

$user_id = $_SESSION['user_id'];

// Kullanıcının sepetini al
$sql = "SELECT p.product_id, p.name, p.price, p.image_url, SUM(ci.quantity) as quantity 
        FROM carts c 
        JOIN cart_items ci ON c.cart_id = ci.cart_id 
        JOIN products p ON ci.product_id = p.product_id 
        WHERE c.user_id = ? 
        GROUP BY p.product_id, p.name, p.price, p.image_url";
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
    if ($quantity > 0) {
        $sql = "UPDATE cart_items SET quantity = ? WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = ?) AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iii', $quantity, $user_id, $product_id);
        $stmt->execute();
    }
    header("Location: cart.php");
    exit();
}

// Satın alma işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["checkout"])) {
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $country = $_POST['country'];
    $credit_card = $_POST['credit_card'];
    $expiration_date = $_POST['expiration_date'];
    $cvc = $_POST['cvc'];

    // Burada satın alma işlemini gerçekleştirebilirsiniz (örneğin, ödeme işlemi, sipariş kaydı vb.)

    // Sepeti boşaltma
    $sql = "DELETE FROM cart_items WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();

    echo "<script>alert('Siparişiniz gerçekleşti!'); window.location.href='cart.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include('partials/_header.php') ?>

<body>

    <?php include('partials/_svg.php') ?>

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
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" style="width: 60px; display: inline;">
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

                <h3>Checkout</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Enter your street address" required>
                        <small class="form-text text-muted">Please enter your street address.</small>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" placeholder="Enter your city" required>
                        <small class="form-text text-muted">Please enter your city.</small>
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state" placeholder="Enter your state" required>
                        <small class="form-text text-muted">Please enter your state.</small>
                    </div>
                    <div class="mb-3">
                        <label for="zip_code" class="form-label">Zip Code</label>
                        <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="Enter your zip code" required>
                        <small class="form-text text-muted">Please enter your zip code.</small>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="country" name="country" placeholder="Enter your country" required>
                        <small class="form-text text-muted">Please enter your country.</small>
                    </div>
                    <div class="mb-3">
                        <label for="credit_card" class="form-label">Credit Card Number</label>
                        <input type="text" class="form-control" id="credit_card" name="credit_card" placeholder="Enter your credit card number" required>
                        <small class="form-text text-muted">We'll never share your credit card information with anyone else.</small>
                    </div>
                    <div class="mb-3">
                        <label for="expiration_date" class="form-label">Expiration Date</label>
                        <input type="text" class="form-control" id="expiration_date" name="expiration_date" placeholder="MM/YY" required>
                        <small class="form-text text-muted">Please enter the expiration date in MM/YY format.</small>
                    </div>
                    <div class="mb-3">
                        <label for="cvc" class="form-label">CVC</label>
                        <input type="text" class="form-control" id="cvc" name="cvc" placeholder="Enter your CVC number" required>
                        <small class="form-text text-muted">The CVC number is the 3-digit code on the back of your credit card.</small>
                    </div>
                    <button type="submit" name="checkout" class="btn btn-success">Purchase</button>
                </form>

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
