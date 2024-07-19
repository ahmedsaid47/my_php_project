<?php
// Oturumu başlat
session_start();

include 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Abonelik işlemi için değişken tanımlaması
$subscription_message = "";

// Abonelik formu gönderildiğinde işlemi yap
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscribe"])) {
    $email = $conn->real_escape_string($_POST["EMAIL"]);
    // E-posta adresini veritabanına ekle
    $sql = "INSERT INTO subscriptions (email) VALUES ('$email')";
    if ($conn->query($sql) === TRUE) {
        $subscription_message = "You have successfully subscribed.";
    } else {
        $subscription_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}


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


// Add to cart işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    
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
    $sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $cart_id, $product_id);
    $stmt->execute();
}

// Veritabanından veri çekmek için fonksiyon
function fetch_data($conn, $sql) {
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

// Servisler, mobil ürünler, akıllı saatler, promosyonlar ve müşteri yorumlarını veritabanından çek
$services = fetch_data($conn, "SELECT title, description, icon FROM services");
$mobile_products = fetch_data($conn, "SELECT product_id, name, price, image_url FROM products WHERE category = 'Mobile'");
$smart_watches = fetch_data($conn, "SELECT product_id, name, price, image_url FROM products WHERE category = 'Smart Watches'");
$promotion = fetch_data($conn, "SELECT title, subtitle, description, image_url, link_url FROM promotions LIMIT 1")[0] ?? null;
$testimonials = fetch_data($conn, "SELECT author_name, content, rating FROM testimonials");
?>

<!DOCTYPE html>
<html>

  <?php include('partials/_header.php') ?>

  <body data-bs-spy="scroll" data-bs-target="#navbar" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" tabindex="0">

    <?php include('partials/_svg.php') ?>

    <!-- Arama açılır penceresi -->
    <div class="search-popup">
        <div class="search-popup-container">
          <form role="search" method="get" class="search-form" action="">
            <input type="search" id="search-form" class="search-field" placeholder="Type and press enter" value="" name="s" />
            <button type="submit" class="search-submit"><svg class="search"><use xlink:href="#search"></use></svg></button>
          </form>
          <h5 class="cat-list-title">Browse Categories</h5>
          <ul class="cat-list">
            <li class="cat-list-item"><a href="#" title="Mobile Phones">Mobile Phones</a></li>
            <li class="cat-list-item"><a href="#" title="Smart Watches">Smart Watches</a></li>
            <li class="cat-list-item"><a href="#" title="Headphones">Headphones</a></li>
            <li class="cat-list-item"><a href="#" title="Accessories">Accessories</a></li>
            <li class="cat-list-item"><a href="#" title="Monitors">Monitors</a></li>
            <li class="cat-list-item"><a href="#" title="Speakers">Speakers</a></li>
            <li class="cat-list-item"><a href="#" title="Memory Cards">Memory Cards</a></li>
          </ul>
        </div>
    </div>

    <?php include('partials/_navbar.php') ?>
    <?php include('partials/_bilboard.php') ?>

    <!-- Şirket hizmetleri bölümü -->
    <section id="company-services" class="padding-large">
      <div class="container">
        <div class="row">
          <?php foreach ($services as $service): ?>
            <div class="col-lg-3 col-md-6 pb-3">
              <div class="icon-box d-flex">
                <div class="icon-box-icon pe-3 pb-3">
                  <svg class="<?php echo $service['icon']; ?>">
                    <use xlink:href="#<?php echo $service['icon']; ?>" />
                  </svg>
                </div>
                <div class="icon-box-content">
                  <h3 class="card-title text-uppercase text-dark"><?php echo $service['title']; ?></h3>
                  <p><?php echo $service['description']; ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Mobil ürünler bölümü -->
    <section id="mobile-products" class="product-store position-relative padding-large no-padding-top">
      <div class="container">
        <div class="row">
          <div class="display-header d-flex justify-content-between pb-3">
            <h2 class="display-7 text-dark text-uppercase">Mobile Products</h2>
            <div class="btn-right">
              <a href="shop.php" class="btn btn-medium btn-normal text-uppercase">Go to Shop</a>
            </div>
          </div>
          <div class="swiper product-swiper">
            <div class="swiper-wrapper">
              <?php foreach ($mobile_products as $product): ?>
                <div class="swiper-slide">
                  <?php include('partials/_product-card.php'); ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="swiper-pagination position-absolute text-center"></div>
    </section>

    <!-- Akıllı saatler bölümü -->
    <section id="smart-watches" class="product-store padding-large position-relative">
      <div class="container">
        <div class="row">
          <div class="display-header d-flex justify-content-between pb-3">
            <h2 class="display-7 text-dark text-uppercase">Smart Watches</h2>
            <div class="btn-right">
              <a href="shop.php" class="btn btn-medium btn-normal text-uppercase">Go to Shop</a>
            </div>
          </div>
          <div class="swiper product-watch-swiper">
            <div class="swiper-wrapper">
              <?php foreach ($smart_watches as $watch): ?>
                <div class="swiper-slide">
                  <?php $product = $watch; include('partials/_product-card.php'); ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="swiper-pagination position-absolute text-center"></div>
    </section>

    <!-- indirim bölümü -->
    <?php if ($promotion): ?>
      <section id="yearly-sale" class="bg-light-blue overflow-hidden mt-5 padding-xlarge" style="background-image: url('<?php echo $promotion['image_url']; ?>');background-position: right; background-repeat: no-repeat;">
        <div class="row d-flex flex-wrap align-items-center">
          <div class="col-md-6 col-sm-12">
            <div class="text-content offset-4 padding-medium">
              <h3><?php echo $promotion['title']; ?></h3>
              <h2 class="display-2 pb-5 text-uppercase text-dark"><?php echo $promotion['subtitle']; ?></h2>
              <a href="<?php echo $promotion['link_url']; ?>" class="btn btn-medium btn-dark text-uppercase btn-rounded-none">Shop Sale</a>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- Müşteri yorumları bölümü -->
    <section id="testimonials" class="position-relative">
      <div class="container">
        <div class="row">
          <div class="review-content position-relative">
            <div class="swiper-icon swiper-arrow swiper-arrow-prev position-absolute d-flex align-items-center">
              <svg class="chevron-left">
                <use xlink:href="#chevron-left" />
              </svg>
            </div>
            <div class="swiper testimonial-swiper">
              <div class="quotation text-center">
                <svg class="quote">
                  <use xlink:href="#quote" />
                </svg>
              </div>
              <div class="swiper-wrapper">
                <?php foreach ($testimonials as $testimonial): ?>
                  <div class="swiper-slide text-center d-flex justify-content-center">
                    <div class="review-item col-md-10">
                      <i class="icon icon-review"></i>
                      <blockquote><?php echo $testimonial['content']; ?></blockquote>
                      <div class="rating">
                        <?php
                        // Yıldız değerlendirmesi
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < $testimonial['rating']) {
                                echo '<svg class="star star-fill"><use xlink:href="#star-fill"></use></svg>';
                            } else {
                                echo '<svg class="star star-empty"><use xlink:href="#star-empty"></use></svg>';
                            }
                        }
                        ?>
                      </div>
                      <div class="author-detail">
                        <div class="name text-dark text-uppercase pt-2"><?php echo $testimonial['author_name']; ?></div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="swiper-icon swiper-arrow swiper-arrow-next position-absolute d-flex align-items-center">
              <svg class="chevron-right">
                <use xlink:href="#chevron-right" />
              </svg>
            </div>
          </div>
        </div>
      </div>
      <div class="swiper-pagination"></div>
    </section>

    <!-- Abonelik formu bölümü -->
    <section id="subscribe" class="container-grid padding-large position-relative overflow-hidden">
      <div class="container">
        <div class="row">
          <div class="subscribe-content bg-dark d-flex flex-wrap justify-content-center align-items-center padding-medium">
            <div class="col-md-6 col-sm-12">
              <div class="display-header pe-3">
                <h2 class="display-7 text-uppercase text-light">Subscribe Us Now</h2>
                <p>Get latest news, updates and deals directly mailed to your inbox.</p>
                <?php if ($subscription_message): ?>
                  <p><?php echo $subscription_message; ?></p>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-md-5 col-sm-12">
              <form class="subscription-form validate" method="POST" action="">
                <div class="input-group flex-wrap">
                  <input class="form-control btn-rounded-none" type="email" name="EMAIL" placeholder="Your email address here" required="">
                  <button class="btn btn-medium btn-primary text-uppercase btn-rounded-none" type="submit" name="subscribe">Subscribe</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php include('partials/_footer.php') ?>

  </body>
</html>

<?php
// Veritabanı bağlantısını kapat
$conn->close();
?>
