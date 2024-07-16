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
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'];
        $link_url = $_POST['link_url'];
        $sql = "INSERT INTO promotions (title, subtitle, description, image_url, link_url) VALUES ('$title', '$subtitle', '$description', '$image_url', '$link_url')";
        $conn->query($sql);
    } elseif ($_POST['action'] == 'delete') {
        $promotion_id = $_POST['promotion_id'];
        $sql = "DELETE FROM promotions WHERE promotion_id=$promotion_id";
        $conn->query($sql);
    } elseif ($_POST['action'] == 'update') {
        $promotion_id = $_POST['promotion_id'];
        $title = $_POST['title'];
        $subtitle = $_POST['subtitle'];
        $description = $_POST['description'];
        $image_url = $_POST['image_url'];
        $link_url = $_POST['link_url'];
        $sql = "UPDATE promotions SET title='$title', subtitle='$subtitle', description='$description', image_url='$image_url', link_url='$link_url' WHERE promotion_id=$promotion_id";
        $conn->query($sql);
    }
}

$result = $conn->query("SELECT * FROM promotions");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Promotions</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Add Promotion</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Promotion ID</th>
                    <th>Title</th>
                    <th>Subtitle</th>
                    <th>Description</th>
                    <th>Image URL</th>
                    <th>Link URL</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['promotion_id']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['subtitle']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['image_url']; ?></td>
                        <td><?php echo $row['link_url']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#updateModal<?php echo $row['promotion_id']; ?>">Edit</button>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="promotion_id" value="<?php echo $row['promotion_id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModal<?php echo $row['promotion_id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Edit Promotion</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="promotion_id" value="<?php echo $row['promotion_id']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" name="title" value="<?php echo $row['title']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="subtitle">Subtitle</label>
                                            <input type="text" class="form-control" name="subtitle" value="<?php echo $row['subtitle']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" name="description" required><?php echo $row['description']; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="image_url">Image URL</label>
                                            <input type="text" class="form-control" name="image_url" value="<?php echo $row['image_url']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="link_url">Link URL</label>
                                            <input type="text" class="form-control" name="link_url" value="<?php echo $row['link_url']; ?>" required>
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
                            <h5 class="modal-title" id="createModalLabel">Add Promotion</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="create">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="subtitle">Subtitle</label>
                                <input type="text" class="form-control" name="subtitle" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image_url">Image URL</label>
                                <input type="text" class="form-control" name="image_url" required>
                            </div>
                            <div class="form-group">
                                <label for="link_url">Link URL</label>
                                <input type="text" class="form-control" name="link_url" required>
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
