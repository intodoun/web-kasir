<?php
include 'connection.php';
$result = $conn->query("SELECT id, product_name, barcode, selling_price FROM products");
$products = [];

while ($row = $result->fetch_assoc()) {
  $products[] = [
    'id' => (int)$row['id'],
    'name' => $row['product_name'],
    'barcode' => $row['barcode'],
    'price' => (int)$row['selling_price']
  ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Scan Barcode</title>
  <script src="https://unpkg.com/html5-qrcode"></script>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f0f0f0; /* Grey background */
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      color: #333;
    }

    h2 {
      margin-bottom: 20px;
      color: #444;
    }

    #reader {
      border: 2px solid #ccc;
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 10px;
    }

    #result {
      margin-top: 20px;
      font-size: 18px;
      color: #555;
      background-color: #e0e0e0;
      padding: 10px 20px;
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <h2>Scan Barcode Produk</h2>
  <div id="reader" style="width: 300px;"></div>
  <div id="result"></div>

  <script>
    const allProducts = <?= json_encode($products); ?>;

    function onScanSuccess(decodedText, decodedResult) {
      document.getElementById("result").innerText = `Barcode: ${decodedText}`;

      const product = allProducts.find(p => p.barcode === decodedText);

      if (!product) {
        alert("Produk tidak ditemukan!");
        return;
      }

      // Tambahkan ke localStorage (keranjang)
      let cart = JSON.parse(localStorage.getItem("cart")) || [];
      const index = cart.findIndex(item => item.id === product.id);

      if (index !== -1) {
        cart[index].qty += 1;
      } else {
        cart.push({
          id: product.id,
          name: product.name,
          price: product.price,
          qty: 1
        });
      }

      localStorage.setItem("cart", JSON.stringify(cart));
      alert("Produk ditambahkan ke keranjang!");

      // Opsional: redirect ke keranjang
      window.location.href = "keranjang.php";

      html5QrcodeScanner.clear(); // Hentikan scanner
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", {
      fps: 10,
      qrbox: 250
    });

    html5QrcodeScanner.render(onScanSuccess);
  </script>
</body>
</html>
