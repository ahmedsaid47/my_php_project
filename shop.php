<?php
session_start();

$conn = new mysqli('localhost', 'root', '123', 'my_php_project');

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Sayfalamayı ayarla
$limit = 10; // Sayfa başına ürün sayısı
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtreleme parametreleri
$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Filtreleme ve sıralama sorgusunu 
$where = ($category !== 'All') ? "WHERE category = '$category'" : '';
$order_by = ($sort === 'name_asc') ? 'name ASC' : 'name DESC';

// Toplam ürün sayısını 
$sql_count = "SELECT COUNT(*) as total FROM products $where";
$result_count = $conn->query($sql_count);
$total_products = $result_count->fetch_assoc()['total'];

// Ürünleri al
$sql = "SELECT name, price, image_url FROM products $where ORDER BY $order_by LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shop - Ministore</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include('partials/_header.php') ?>
    
    <div class="container mt-5">
        <h2 class="display-7 text-dark text-uppercase">Shop</h2>

        <!-- Filtreleme Formu -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="All">All Categories</option>
                        <option value="Mobile" <?php if ($category == 'Mobile') echo 'selected'; ?>>Mobile</option>
                        <option value="Smart Watches" <?php if ($category == 'Smart Watches') echo 'selected'; ?>>Smart Watches</option>
                        <!-- Diğer kategoriler eklenebilir -->
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="sort" class="form-select">
                        <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name Ascending</option>
                        <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name Descending</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- Ürün Listesi -->
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="product-card">
                            <div class="image-holder">
                                <img src="<?php echo $row['image_url']; ?>" alt="product" class="img-fluid">
                            </div>
                            <div class="cart-concern">
                                <button class="btn btn-black">Add to Cart</button>
                            </div>
                            <div class="card-detail">
                                <h3 class="card-title"><?php echo $row['name']; ?></h3>
                                <span class="item-price">$<?php echo $row['price']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>

        <!-- Sayfalama -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php for ($i = 1; $i <= ceil($total_products / $limit); $i++): ?>
                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <?php include('partials/_footer.php') ?>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
