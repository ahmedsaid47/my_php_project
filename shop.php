<?php
session_start();

$conn = new mysqli('localhost', 'root', '123', 'my_php_project');

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Sepet sayısını kontrol et ve başlat
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

// Add to cart işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    $_SESSION['cart_count'] += 1;
}

// Sayfalamayı ayarla
$limit = 10; // Sayfa başına ürün sayısı
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtreleme parametreleri
$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Filtreleme ve sıralama sorgusunu oluştur
$where = ($category !== 'All') ? "WHERE category = ?" : '';
$where .= (!empty($search)) ? (($where !== '') ? ' AND' : 'WHERE') . " name LIKE ?" : '';
$order_by = ($sort === 'name_asc') ? 'name ASC' : ($sort === 'name_desc' ? 'name DESC' : ($sort === 'price_asc' ? 'price ASC' : 'price DESC'));

// Toplam ürün sayısını al
$sql_count = "SELECT COUNT(*) as total FROM products $where";
$stmt_count = $conn->prepare($sql_count);

if ($category !== 'All' && !empty($search)) {
    $search_param = "%$search%";
    $stmt_count->bind_param('ss', $category, $search_param);
} elseif ($category !== 'All') {
    $stmt_count->bind_param('s', $category);
} elseif (!empty($search)) {
    $search_param = "%$search%";
    $stmt_count->bind_param('s', $search_param);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_products = $result_count->fetch_assoc()['total'];

// Ürünleri al
$sql = "SELECT name, price, image_url FROM products $where ORDER BY $order_by LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

if ($category !== 'All' && !empty($search)) {
    $stmt->bind_param('ssii', $category, $search_param, $limit, $offset);
} elseif ($category !== 'All') {
    $stmt->bind_param('sii', $category, $limit, $offset);
} elseif (!empty($search)) {
    $stmt->bind_param('sii', $search_param, $limit, $offset);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

   <?php include('partials/_header.php') ?>

<body data-bs-spy="scroll" data-bs-target="#navbar" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" tabindex="0">
    
   <?php include('partials/_svg.php') ?>

   <?php include('partials/_navbar.php') ?>

    
    <div class="container mt-5">
        <h2 class="display-7 text-dark text-uppercase">Shop</h2>

        <!-- Filtreleme ve Arama Formu -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="All">All Categories</option>
                        <option value="Mobile" <?php if ($category == 'Mobile') echo 'selected'; ?>>Mobile</option>
                        <option value="Smart Watches" <?php if ($category == 'Smart Watches') echo 'selected'; ?>>Smart Watches</option>
                        <!-- Diğer kategoriler eklenebilir -->
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="name_asc" <?php if ($sort == 'name_asc') echo 'selected'; ?>>Name Ascending</option>
                        <option value="name_desc" <?php if ($sort == 'name_desc') echo 'selected'; ?>>Name Descending</option>
                        <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Price Ascending</option>
                        <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Price Descending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>

        <!-- Ürün Listesi -->
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4  ">

                        <div class="product-card position-relative">

                            <div class="image-holder">
                                <img src="<?php echo $row['image_url']; ?>" alt="product-item" class="img-fluid">
                            </div>


                            <div class="cart-concern position-absolute">
                            <div class="cart-button d-flex">
                                <form method="POST">
                                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-black">Add to Cart</button>
                                </form>                      
                            </div>
                            </div>


                            <div class="card-detail d-flex justify-content-between align-items-baseline pt-3">
                                <h3 class="card-title text-uppercase"><?php echo $row['name']; ?></h3>
                                <span class="item-price text-primary">$<?php echo $row['price']; ?></span>
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
                        <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&search=<?php echo htmlspecialchars($search); ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
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
