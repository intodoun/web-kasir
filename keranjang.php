<?php
include 'connection.php';
$result = $conn->query("SELECT id, product_name, barcode, selling_price FROM products");
$products = [];

while ($row = $result->fetch_assoc()) {
  $products[] = [
    'id' => (int)$row['id'],
    'name' => $row['product_name'],
    'barcode' => $row['barcode'],
    'price' => (float)str_replace('.', '', $row['selling_price'])
  ];
}  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Keranjang</title>
  <style>
    .cart-container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 1rem;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-family: sans-serif;
    }

    .cart-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #ddd;
      padding: 1rem 0;
    }

    .cart-item h4 {
      margin: 0;
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .quantity-controls button {
      padding: 4px 10px;
    }

    .total {
      text-align: right;
      font-size: 1.2rem;
      margin-top: 1rem;
    }

    .btn {
      padding: 8px 16px;
      margin-top: 1rem;
      cursor: pointer;
    }

    .back-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 1rem;
      cursor: pointer;
    }

    .back-btn span {
      font-size: 1.2rem;
    }

    input[type="checkbox"] {
      transform: scale(1.3);
      margin-right: 10px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="cart-container">

    <div class="back-btn" onclick="window.location.href='produk.php'">
      ← <span>Kembali ke Halaman Produk</span>
    </div>

   
    <!-- Daftar Produk -->
    <div id="cart-items"></div>

    <!-- Total Harga -->
    <div class="total" id="total-price">Total: Rp 0</div>

    <!-- Tombol Checkout -->
    <form id="checkout-form" method="post" action="checkout.php">
      <input type="hidden" name="cartData" id="cartData" />
      <button type="submit" class="btn">Checkout</button>
    </form>

  </div>

  <script>
  const allProducts = <?= json_encode($products); ?>;

function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('total-price');
    container.innerHTML = '';
    let total = 0;

    cart.forEach((item, index) => {
      if (item.qty === undefined || item.qty === null) {
        item.qty = 1;
      }
      if (item.checked === undefined) {
        item.checked = true;
      }
      const subtotal = item.price * item.qty;
      if(item.checked) total += subtotal;

      container.innerHTML += `
        <div class="cart-item">
          <input type="checkbox" id="chk-${index}" ${item.checked ? 'checked' : ''} onchange="toggleCheck(${index})"/>
          <div>
            <h4>${item.name}</h4>
            <p>Rp ${item.price.toLocaleString('id-ID')}</p>
          </div>
          <div class="quantity-controls">
            <button onclick="updateQty(${index}, -1)">-</button>
            <span>${item.qty}</span>
            <button onclick="updateQty(${index}, 1)">+</button>
          </div>
          <div>Rp ${subtotal.toLocaleString('id-ID')}</div>
          <button onclick="removeItem(${index})">🗑️</button>
        </div>
      `;
    });

    totalEl.innerText = `Total: Rp ${total.toLocaleString('id-ID')}`;
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCartCount();  // update badge jumlah item
}


  function updateQty(index, change) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let newQty = cart[index].qty + change;
    if (newQty < 1) newQty = 1;
    if (newQty > 5) {
      alert('Maksimal quantity per produk adalah 5');
      newQty = 5;
    }
    cart[index].qty = newQty;
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
  }

  function removeItem(index) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
  }

  function toggleCheck(index) {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart[index].checked = !cart[index].checked;
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart();
  }

  function checkoutCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    // Kirim cuma produk yang diceklist
    const selectedItems = cart.filter(item => item.checked);
    if(selectedItems.length === 0) {
      alert('Pilih minimal satu produk untuk checkout!');
      return false;
    }
    document.getElementById('cartData').value = JSON.stringify(selectedItems);
    document.getElementById('checkout-form').submit();
  }

  document.getElementById('checkout-form').addEventListener('submit', function (e) {
    e.preventDefault();
    checkoutCart();
  });

  function handleScan(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const barcode = e.target.value.trim();
      if (!barcode) return;

      const product = allProducts.find(p => p.barcode === barcode);
      if (!product) {
        alert("Produk tidak ditemukan!");
        e.target.value = '';
        return;
      }

      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const index = cart.findIndex(item => item.id === product.id);

      if (index !== -1) {
        if(cart[index].qty < 5) {
          cart[index].qty += 1;
        } else {
          alert('Maksimal quantity per produk adalah 5');
        }
      } else {
        cart.push({ id: product.id, name: product.name, price: product.price, qty: 1, checked: true });
      }

      localStorage.setItem('cart', JSON.stringify(cart));
      loadCart();
      e.target.value = '';
    }
  }

 window.onload = () => {
  loadCart();
  loadCartCount(); // <- ini penting untuk update badge notif
};
  </script>
</body>
</html>
