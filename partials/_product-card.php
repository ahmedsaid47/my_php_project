<div class="product-card position-relative">
    <div class="image-holder">
        <img src="<?php echo $product['image_url']; ?>" alt="product-item" class="img-fluid">
    </div>
    <div class="cart-concern position-absolute">
        <div class="cart-button d-flex">
            <form method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <button type="submit" name="add_to_cart" class="btn btn-black">Add to Cart</button>
            </form>                      
        </div>
    </div>
    <div class="card-detail d-flex justify-content-between align-items-baseline pt-3">
        <h3 class="card-title text-uppercase"><?php echo $product['name']; ?></h3>
        <span class="item-price text-primary">$<?php echo $product['price']; ?></span>
    </div>
</div>
