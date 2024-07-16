<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '123', 'my_php_project');

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $cart_id = $_POST['cart_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ('$cart_id', '$product_id', '$quantity')";
        $conn->query($sql);
    } elseif ($_POST['action'] == 'delete') {
        $cart_item_id = $_POST['cart_item_id'];
        $sql = "DELETE FROM cart_items WHERE cart_item_id=$cart_item_id";
        $conn->query($sql);
    } elseif ($_POST['action'] == 'update') {
        $cart_item_id = $_POST['cart_item_id'];
        $cart_id = $_POST['cart_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $sql = "UPDATE cart_items SET cart_id='$cart_id', product_id='$product_id', quantity='$quantity' WHERE cart_item_id=$cart_item_id";
        $conn->query($sql);
    }
}

$result = $conn->query("SELECT * FROM cart_items");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Items</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Cart Items</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Add Cart Item</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cart Item ID</th>
                    <th>Cart ID</th>
                    <th>Product ID</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['cart_item_id']; ?></td>
                        <td><?php echo $row['cart_id']; ?></td>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#updateModal<?php echo $row['cart_item_id']; ?>">Edit</button>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="cart_item_id" value="<?php echo $row['cart_item_id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModal<?php echo $row['cart_item_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Edit Cart Item</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="cart_item_id" value="<?php echo $row['cart_item_id']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <div class="form-group">
                                            <label for="cart_id">Cart ID</label>
                                            <input type="text" class="form-control" name="cart_id" value="<?php echo $row['cart_id']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="product_id">Product ID</label>
                                            <input type="text" class="form-control" name="product_id" value="<?php echo $row['product_id']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="quantity">Quantity</label>
                                            <input type="text" class="form-control" name="quantity" value="<?php echo $row['quantity']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Create Modal -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createModalLabel">Add Cart Item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="create">
                            <div class="form-group">
                                <label for="cart_id">Cart ID</label>
                                <input type="text" class="form-control" name="cart_id" required>
                            </div>
                            <div class="form-group">
                                <label for="product_id">Product ID</label>
                                <input type="text" class="form-control" name="product_id" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="text" class="form-control" name="quantity" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </div>
</body>
</html>
