<?php
include 'connection.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$updatedCart = [];

foreach ($data as $item) {
    $barcode = $item['barcode'];

    // Ambil data produk berdasarkan barcode
    $query = $conn->prepare("SELECT product_name, selling_price FROM products WHERE barcode = ?");
    $query->bind_param("s", $barcode);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $updatedCart[] = [
            'barcode' => $barcode,
            'name' => $product['product_name'],
            'price' => (int)$product['selling_price'],
            'qty' => (int)$item['qty']
        ];
    }
    // Jika produk sudah dihapus, jangan dimasukkan ke cart baru
}

echo json_encode($updatedCart);
