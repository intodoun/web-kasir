<?php
// Include koneksi database
include 'connection.php';

// Cek apakah ada session atau identitas pengguna
session_start();

if (!isset($_SESSION['cart'])) {
    echo json_encode([]);
    exit;
}

$cart = $_SESSION['cart']; // Ambil keranjang dari session
$productIds = array_map(function ($item) {
    return $item['id']; // Ambil ID produk yang ada di keranjang
}, $cart);

if (empty($productIds)) {
    echo json_encode([]);
    exit;
}

// Query untuk mengambil data produk berdasarkan ID produk yang ada di keranjang
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$stmt = $conn->prepare("SELECT id, product_name, selling_price, image FROM products WHERE id IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Menambahkan data produk ke dalam cart (qty, dll)
foreach ($cart as &$cartItem) {
    foreach ($products as &$product) {
        if ($cartItem['id'] == $product['id']) {
            $product['qty'] = $cartItem['qty']; // Tambahkan qty yang ada di session
            break;
        }
    }
}

// Kirim data produk dalam keranjang ke frontend
echo json_encode($products);
?>
