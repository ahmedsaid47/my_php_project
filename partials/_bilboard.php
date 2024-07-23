<?php
include 'db.php';

// Slayt verilerini çekme
$sql = "SELECT title, description, button_text, image_path FROM slides";
$result = $conn->query($sql);
?>

<section id="billboard" class="position-relative overflow-hidden bg-light-blue">
  <div class="swiper main-swiper">
    <div class="swiper-wrapper">
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="swiper-slide">
            <div class="container">
              <div class="row d-flex align-items-center">
                <div class="col-md-6">
                  <div class="banner-content">
                    <h1 class="display-2 text-uppercase text-dark pb-5"><?php echo $row["title"]; ?></h1>
                    <p class="pb-3"><?php echo $row["description"]; ?></p>
                    <a href="shop.php" class="btn btn-medium btn-dark text-uppercase btn-rounded-none"><?php echo $row["button_text"]; ?></a>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="image-holder">
                    <img src="<?php echo $row["image_path"]; ?>" alt="banner">
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Slayt bulunamadı.</p>
      <?php endif; ?>
    </div>
  </div>
  <div class="swiper-icon swiper-arrow swiper-arrow-prev">
    <svg class="chevron-left">
      <use xlink:href="#chevron-left" />
    </svg>
  </div>
  <div class="swiper-icon swiper-arrow swiper-arrow-next">
    <svg class="chevron-right">
      <use xlink:href="#chevron-right" />
    </svg>
  </div>
</section>

<?php
$conn->close();
?>
