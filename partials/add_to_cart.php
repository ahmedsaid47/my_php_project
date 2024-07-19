<?php
session_start();
include 'db.php';

$response = ['status' => 'error'];

if (isset($_SESSION['user_id']) && isset($_POST['product_id'])) {
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

    // Sepetteki ürün sayısını güncelle
    $sql = "SELECT SUM(quantity) as cart_count FROM cart_items WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $_SESSION['cart_count'] = $row['cart_count'];
    $response = ['status' => 'success', 'cart_count' => $_SESSION['cart_count']];
}

echo json_encode($response);
?>
