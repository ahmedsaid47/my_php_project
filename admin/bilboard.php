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
    $title = $_POST['title'];
    $description = $_POST['description'];
    $button_text = $_POST['button_text'];
    $image_path = $_POST['image_path'];

    if ($_POST['action'] == 'create') {
        $sql = "INSERT INTO slides (title, description, button_text, image_path) VALUES ('$title', '$description', '$button_text', '$image_path')";
    } elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $sql = "DELETE FROM slides WHERE id=$id";
    } elseif ($_POST['action'] == 'update') {
        $id = $_POST['id'];
        $sql = "UPDATE slides SET title='$title', description='$description', button_text='$button_text', image_path='$image_path' WHERE id=$id";
    }
    $conn->query($sql);
}

$result = $conn->query("SELECT * FROM slides");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilboard Slides</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Bilboard Slides</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Add Slide</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Button Text</th>
                    <th>Image Path</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['button_text']; ?></td>
                        <td><?php echo $row['image_path']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#updateModal<?php echo $row['id']; ?>">Edit</button>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Update Modal -->
                    <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel">Edit Slide</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" name="title" value="<?php echo $row['title']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" name="description" required><?php echo $row['description']; ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="button_text">Button Text</label>
                                            <input type="text" class="form-control" name="button_text" value="<?php echo $row['button_text']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="image_path">Image Path</label>
                                            <input type="text" class="form-control" name="image_path" value="<?php echo $row['image_path']; ?>" required>
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
                            <h5 class="modal-title" id="createModalLabel">Add Slide</h5>
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
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="button_text">Button Text</label>
                                <input type="text" class="form-control" name="button_text" required>
                            </div>
                            <div class="form-group">
                                <label for="image_path">Image Path</label>
                                <input type="text" class="form-control" name="image_path" required>
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

<?php
$conn->close();
?>
