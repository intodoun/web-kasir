<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include 'connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Anda harus login sebagai admin terlebih dahulu!'); window.location.href='login.php';</script>";
    exit();
}

$admin_id = $_SESSION['admin_id'];
$member = null;
$discount = 0;
$total = 0;
$cart = [];
$finalTotal = 0;
$change = 0;
$payment = 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart = json_decode($_POST['cartData'], true);
    $total = 0;

    foreach ($cart as $item) {
        $qty = intval($item['qty'] ?? 1);
        $price = intval($item['price'] ?? 0);
        $total += $qty * $price;
    }

    $discount = 0;
    $finalTotal = $total;

    // Verifikasi nomor telepon
    if (!empty($_POST['phone'])) {
        $phone = $conn->real_escape_string($_POST['phone']);
        $result = $conn->query("SELECT * FROM member WHERE phone = '$phone' AND status = 'active'");
        if ($result && $result->num_rows > 0) {
            $member = $result->fetch_assoc();
            $point = intval($member['point']);
            $discountPercent = floor($point / 10); // 1% per 10 poin
            $discount = ($discountPercent / 100) * $total;
            if ($discount > $total) $discount = $total;
        }
    }

    $finalTotal = $total - $discount;

    if (isset($_POST['bayar'])) {
        $payment = intval($_POST['payment']);
        if ($payment < $finalTotal) {
            $message = "Uang tidak cukup untuk melakukan pembayaran!";
        } else {
            $date = date('Y-m-d H:i:s');
            $margin_total = 0;
            $details_array = [];

            foreach ($cart as $item) {
                $fid_product = intval($item['id']);
                $qty = intval($item['qty'] ?? 1);
                $price = intval($item['price']);
                $subtotal = $qty * $price;

                // Ambil margin dari database
                $getProduct = $conn->query("SELECT margin FROM products WHERE id = $fid_product");
                $productData = $getProduct->fetch_assoc();
                $marginPerItem = intval($productData['margin'] ?? 0);
                $margin = $marginPerItem * $qty;
                $margin_total += $margin;

                $product_name = $conn->real_escape_string($item['name']);
                $details_array[] = "$product_name ($qty)";

                // Simpan transaksi
                $conn->query("
                    INSERT INTO transactions (date, fid_admin, fid_member, fid_product, detail, total_price, margin_total)
                    VALUES ('$date', $admin_id, " . ($member ? $member['id'] : "NULL") . ", $fid_product, '$product_name ($qty)', $subtotal, $margin)
                ");

                // Update stok
                $conn->query("UPDATE products SET qty = qty - $qty WHERE id = $fid_product");
            }

            // Tambah poin ke member
          if ($member) {
    $point_didapat = floor($finalTotal / 10000) * 10;
    
    // Kurangi poin yang digunakan (untuk diskon)
    $poin_digunakan = floor($member['point'] / 10) * 10;
    $new_point = $member['point'] - $poin_digunakan + $point_didapat;

    $conn->query("UPDATE member SET point = $new_point WHERE id = {$member['id']}");
}
            // Simpan invoice ke session
            $_SESSION['last_invoice'] = [
                'cart' => $cart,
                'total' => $total,
                'discount' => $discount,
                'finalTotal' => $finalTotal,
                'payment' => $payment,
                'change' => $payment - $finalTotal,
                'member' => $member,
                'date' => $date,
            ];

            // Hapus keranjang dari session
            unset($_SESSION['keranjang']);

            // Redirect ke invoice
            header("Location: invoice.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #e0e0e0; padding: 20px; }
    .checkout-box { background-color: #f5f5f5; border: 1px solid #ccc; max-width: 600px; margin: auto; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2, h3 { text-align: center; color: #333; }
    .cart-item { margin-bottom: 10px; padding: 10px; background-color: #ddd; border-radius: 4px; }
    label { font-weight: bold; display: block; margin-top: 10px; }
    input[type="text"], input[type="number"] { width: 100%; padding: 8px; border: 1px solid #aaa; border-radius: 4px; margin-top: 4px; background-color: #fff; }
    button { margin-top: 20px; padding: 10px; width: 100%; background-color: #666; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
    button:hover { background-color: #555; }
    .highlight { color: green; font-weight: bold; }
    .message { margin-top: 15px; padding: 10px; background-color: #d0f0d0; border-left: 5px solid green; border-radius: 4px; }
    .error { background-color: #f8d7da; border-left: 5px solid #dc3545; }
  </style>
</head>
<body>
<h2>Checkout</h2>
<div class="checkout-box">
  <?php if (!empty($message)) {
    echo "<div class='message error'>" . htmlspecialchars($message) . "</div>";
  } ?>

  <?php if (!empty($_POST['cartData'])):
      $cart = json_decode($_POST['cartData'], true); ?>

    <h3>Detail Produk:</h3>
    <?php foreach ($cart as $item): ?>
      <div class="cart-item">
        <?php
          $name = htmlspecialchars($item['name'] ?? 'Produk');
          $price = intval($item['price'] ?? 0);
          $qty = intval($item['qty'] ?? 1);
          $subtotal = $price * $qty;
        ?>
        <?= $name ?> (<?= $qty ?> x Rp <?= number_format($price) ?>) = Rp <?= number_format($subtotal) ?>
      </div>
    <?php endforeach; ?>

    <hr>
    <p>Total Sebelum Diskon: <strong>Rp <?= number_format($total) ?></strong></p>

    <?php if ($member): ?>
      <p>Member: <strong><?= htmlspecialchars($member['name']) ?></strong> (Poin: <?= $member['point'] ?>)</p>
      <p>Diskon (<?= floor($member['point'] / 10) ?>%): <span class="highlight">Rp <?= number_format($discount) ?></span></p>
    <?php else: ?>
      <p><em>Member tidak ditemukan atau tidak diisi. Tidak ada diskon yang diterapkan.</em></p>
    <?php endif; ?>

    <p>Total Bayar: <strong>Rp <?= number_format($finalTotal) ?></strong></p>

    <form method="post">
      <input type="hidden" name="cartData" value='<?= htmlspecialchars($_POST['cartData']) ?>'>

      <label for="phone">Nomor Telepon Member (opsional):</label>
      <input type="text" name="phone" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">

      <label for="payment">Uang Diberikan (Rp):</label>
      <input type="number" name="payment" min="0" required>

      <?php if (!isset($_POST['verifikasi'])): ?>
        <button type="submit" name="verifikasi">Hitung Total / Verifikasi</button>
      <?php else: ?>
        <button type="submit" name="bayar">Lanjut ke Invoice</button>
      <?php endif; ?>
    </form>

  <?php else: ?>
    <p>Keranjang kosong atau belum diproses.</p>
  <?php endif; ?>
</div>
</body>
</html>
