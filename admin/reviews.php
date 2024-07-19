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
        $product_id = $_POST['product_id'];
        $user_id = $_POST['user_id'];
        $review_text = $_POST['review_text'];
        $rating = $_POST['rating'];
        $sql = "INSERT INTO product_reviews (product_id, user_id, review_text, rating) VALUES ('$product_id', '$user_id', '$review_text', '$rating')";
        $conn->query($sql);
    } elseif ($_POST['action'] == 'delete') {
        $review_id = $_POST['review_id'];
        $sql = "DELETE FROM product_reviews WHERE review_id=$review_id";
        $conn->query($sql);
    } elseif ($_POST['action'] == 'update') {
        $review_id = $_POST['review_id'];
        $product_id = $_POST['product_id'];
        $user_id = $_POST['user_id'];
        $review_text = $_POST['review_text'];
        $rating = $_POST['rating'];
        $sql = "UPDATE product_reviews SET product_id='$product_id', user_id='$user_id', review_text='$review_text', rating='$rating' WHERE review_id=$review_id";
        $conn->query($sql);
    }
}

$result = $conn->query("SELECT * FROM product_reviews");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Product Reviews</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Add Review</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Review ID</th>
                    <th>Product ID</th>
                    <th>User ID</th>
                    <th>Review Text</th>
                    <th>Rating</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['review_id']; ?></td>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['review_text']; ?></td>
                        <td><?php echo $row['rating']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#updateModal<?php echo $row['review_id']; ?>">Edit</button>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="review_id" value="<?php echo $row['review_id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModal<?php echo $row['review_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Edit Review</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="review_id" value="<?php echo $row['review_id']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <div class="form-group">
                                            <label for="product_id">Product ID</label>
                                            <input type="text" class="form-control" name="product_id" value="<?php echo $row['product_id']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="user_id">User ID</label>
                                            <input type="text" class="form-control" name="user_id" value="<?php echo $row['user_id']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="review_text">Review Text</label>
                                            <textarea class="form-control" name="review_text" required><?php echo $row['review_text']; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="rating">Rating</label>
                                            <input type="number" class="form-control" name="rating" value="<?php echo $row['rating']; ?>" required>
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
                            <h5 class="modal-title" id="createModalLabel">Add Review</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="create">
                            <div class="form-group">
                                <label for="product_id">Product ID</label>
                                <input type="text" class="form-control" name="product_id" required>
                            </div>
                            <div class="form-group">
                                <label for="user_id">User ID</label>
                                <input type="text" class="form-control" name="user_id" required>
                            </div>
                            <div class="form-group">
                                <label for="review_text">Review Text</label>
                                <textarea class="form-control" name="review_text" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <input type="number" class="form-control" name="rating" required>
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
