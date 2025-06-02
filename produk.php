<?php

session_start();
include 'connection.php';
if (!isset($_SESSION['admin_id'])) {
    echo "Silakan login dulu!";
    exit;
}

$admin_id = $_SESSION['admin_id']; // Ambil ID admin setelah dipastikan ada

// Ambil gambar profil admin
$query = mysqli_query($conn, "SELECT image FROM admin WHERE id = '$admin_id'");
$data = mysqli_fetch_assoc($query);
$image = !empty($data['image']) ? $data['image'] : 'default.jpg'; // Pakai gambar default jika tidak ada


$cart = $_SESSION['cart'] ?? [];
$cartCount = count($cart);

// Ambil parameter kategori dari URL
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : ''; 

// Jika ada kategori, filter berdasarkan kategori tersebut
if ($kategori) {
    $kategori = mysqli_real_escape_string($conn, $kategori); // Sanitasi input untuk menghindari SQL Injection
    $query = "SELECT products.*, category AS category
              FROM products 
              JOIN category ON products.fid_category = category.id
              WHERE LOWER(category) = LOWER('$kategori')"; // Filter produk berdasarkan kategori
} else {
    // Jika tidak ada kategori yang dipilih, tampilkan semua produk
    $query = "SELECT products.*, category AS category
              FROM products 
              JOIN category ON products.fid_category = category.id";
}

$result = mysqli_query($conn, $query);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Cek apakah ada gambar, kalau tidak pakai default.jpg
    $row['gambar'] = !empty($row['image']) ? 'uploads/' . $row['image'] : 'default.jpg';

    $products[] = $row;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
               * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            min-height: 100vh;
            background-color: #f4f4f4; /* Light background for contrast */
        }
        .image{
    width: 40px;  /* Sesuaikan dengan ukuran icon cart */
    height: 40px;
    border-radius: 50%; /* Biar jadi lingkaran */
    object-fit: cover; /* Biar gak gepeng */
    margin-left: 15px; /* Jarak dari icon cart */
}
  /* Navbar kategori */
  .category-nav {
            display: flex;
            justify-content: flex-start;
            gap: 30px;
            margin-top: 100px;
            padding-left: 95px;
        }

        .category-btn {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            border-radius: 5px;
        }

        .category-btn:hover {
            background-color: #555;
        }

        /* Container produk */
       .product-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 kolom */
    gap: 20px;
    padding: 20px 50px;
}

        .product-box img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Pastikan gambar ter-crop dengan baik sesuai kotak */
    position: absolute; /* Memastikan gambar ada di bawah overlay */
    top: 0;
    left: 0;
}

        /* Produk */
        .product-box {
    width: 250px;
    height: 250px;
    position: relative;
    background: url("https://i.pinimg.com/564x/6f/5a/b1/6f5ab1b470beeeeaf285bb451c63ac8f.jpg");
    background-size: cover;
    background-position: center;
    cursor: pointer;
    box-shadow: 0 0 5px #000;
    text-align: center;
    transition: transform 0.3s ease-in-out;
    margin: 4px; /* lebih rapat */
    padding: 8px;
    display: inline-block;
    vertical-align: top;
}


.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(77, 77, 77, 0.9); /* Memastikan overlay menutupi seluruh area */
    color: #FEF5DF;
    opacity: 0;
    transition: opacity 0.5s;
    font-family: 'Playfair Display', serif;
    padding: 15px;
}

.product-box:hover .overlay {
    opacity: 1; /* Overlay muncul penuh ketika di-hover */
}

        /* Ukuran teks dalam overlay */
        .head {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .price {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .old {
            text-decoration: line-through;
            color: rgba(255, 255, 255, 0.5);
        }

        .cart {
            font-size: 12px;
            font-weight: bold;
            background:rgb(89, 70, 63);
            padding: 6px 12px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .cart:hover {
            background:rgb(158, 131, 122);
        }
       


        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background: #C0C0C0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            z-index: 99;
        }

        nav .navbar {
            height: 100%;
            max-width: 1250px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: auto;
            padding: 0 50px;
        }

        .navbar .logo a {
            font-size: 30px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        nav .navbar .nav-links {
            line-height: 70px;
            height: 100%;
        }

        nav .navbar .links {
            display: flex;
        }

        nav .navbar .links li {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            list-style: none;
            padding: 0 14px;
            transition: color 0.3s; /* Transition for hover effect */
        }

        nav .navbar .links li a {
            height: 100%;
            text-decoration: none;
            white-space: nowrap;
            color: #fff;
            font-size: 15px;
            font-weight: 500;
        }

        /* Hover effect for navbar links */
        nav .navbar .links li:hover a {
            color: #71797E; /* Change color on hover */
        }

        .links li:hover .htmlcss-arrow,
        .links li:hover .js-arrow {
            transform: rotate(180deg);
        }

        nav .navbar .links li .arrow {
            height: 100%;
            width: 22px;
            line-height: 70px;
            text-align: center;
            display: inline-block;
            color: #fff;
            transition: all 0.3s ease;
        }

        nav .navbar .links li .sub-menu {
            position: absolute;
            top: 70px;
            left: 0;
            line-height: 40px;
            background: #C0C0C0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            border-radius: 0 0 4px 4px;
            display: none;
            z-index: 2;
        }

        nav .navbar .links li:hover .htmlCss-sub-menu,
        nav .navbar .links li:hover .js-sub-menu {
            display: block;
        }

        .navbar .links li .sub-menu li {
            padding: 0 22px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar .links li .sub-menu a {
            color: #fff;
            font-size: 15px;
            font-weight: 500;
        }

        .navbar .links li .sub-menu .more-arrow {
            line-height: 40px;
        }

        .navbar .links li .htmlCss-more-sub-menu {
            /* line-height: 40px; */
        }

        .navbar .links li .sub-menu .more-sub-menu {
            position: absolute;
            top: 0;
            left: 100%;
            border-radius: 0 4px 4px 4px;
            z-index: 1;
            display: none;
        }

        .links li .sub-menu .more:hover .more-sub-menu {
            display: block;
        }

        

        .navbar .nav-links .sidebar-logo {
            display: none;
        }

        .navbar .bx-menu {
            display: none;
        }

        .icons {
            display: flex;
            align-items: center;
        }

        .icons i {
            font-size: 25px;
            color: #fff; /* Change cart icon color to white */
            margin-left: 15px;
            cursor: pointer;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 15px;
            cursor: pointer;
        }
        .profile-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.profile-container img {
    width: 40px; /* Sesuaikan ukuran gambar */
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
}

.dropdown-menu {
    display: none; /* Default tersembunyi */
    position: absolute;
    top: 50px; /* Beri jarak dari gambar */
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    min-width: 120px;
    z-index: 10;
    text-align: left;
}

.dropdown-menu a {
    display: block;
    padding: 10px;
    color: black;
    text-decoration: none;
    white-space: nowrap;
}

.dropdown-menu a:hover {
    background: #f0f0f0;
}

/* Tampilkan dropdown ketika .profile-container aktif */
.profile-container.active .dropdown-menu {
    display: block;
}

        @media (max-width: 920px) {
            nav .navbar {
                max-width: 100%;
                padding: 0 25px;
            }

            nav .navbar .logo a {
                font-size: 27px;
            }

            nav .navbar .links li {
                padding: 0 10px;
                white-space: nowrap;
            }

            nav .navbar .links li a {
                font-size: 15px;
            }
        }

        @media (max-width: 800px) {
            nav {
                /* position: relative; */
            }

            .navbar .bx-menu {
                display: block;
            }

            nav .navbar .nav-links {
                position: fixed;
                top: 0;
                left: -100%;
                display: block;
                max-width: 270px;
                width: 100%;
                background: #3E8DA8;
                line-height: 40px;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                transition: all 0.5s ease;
                z-index: 1000;
            }

            .navbar .nav-links .sidebar-logo {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .sidebar-logo .logo-name {
                font-size: 25px;
                color: #fff;
            }

            .sidebar-logo i,
            .navbar .bx-menu {
                font-size: 25px;
                color: #fff;
            }

            nav .navbar .links {
                display: block;
                margin-top: 20px;
            }

            nav .navbar .links li .arrow {
                line-height: 40px;
            }

            nav .navbar .links li {
                display: block;
            }

            nav .navbar .links li .sub-menu {
                position: relative;
                top: 0;
                box-shadow: none;
                display: none;
            }

            nav .navbar .links li .sub-menu li {
                border-bottom: none;
            }

            .navbar .links li .sub-menu .more-sub-menu {
                display: none;
                position: relative;
                left: 0;
            }

            .navbar .links li .sub-menu .more-sub-menu li {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .links li:hover .htmlcss-arrow,
            .links li:hover .js-arrow {
                transform: rotate(0deg);
            }

            .navbar .links li .sub-menu .more-sub-menu {
                display: none;
            }

            .navbar .links li .sub-menu .more span {
                display: flex;
                align-items: center;
            }

            .links li .sub-menu .more:hover .more-sub-menu {
                display: none;
            }

            nav .navbar .links li:hover .htmlCss-sub-menu,
            nav .navbar .links li:hover .js-sub-menu {
                display: none;
            }

            .navbar .nav-links.show1 .links .htmlCss-sub-menu,
            .navbar .nav-links.show3 .links .js-sub-menu,
            .navbar .nav-links.show2 .links .more .more-sub-menu {
                display: block;
            }

            .navbar .nav-links.show1 .links .htmlcss-arrow,
            .navbar .nav-links.show3 .links .js-arrow {
                transform: rotate(180deg);
            }

            .navbar .nav-links.show2 .links .more-arrow {
                transform: rotate(90deg);
            }
        

            @media (max-width: 370px) {
                nav .navbar .nav-links {
                    max-width: 100%;
                }
            }
          }
    </style>
</head>
<body>
<nav>
    <div class="navbar">
        <i class='bx bx-menu'></i>
        <div class="logo"><a href="#">BuyKasir</a></div>
        <div class="nav-links">
            <div class="sidebar-logo">
                <span class="logo-name">profil</span>
                <i class='bx bx-x'></i>
            </div>
            <ul class="links">
                <li><a href="index.php">BERANDA</a></li>
                <li><a href="produk.php">PRODUK</a></li>
                <li><a href="member.php">MEMBER</a></li>
                <li>
                    <a href="#">LAPORAN</a>
                    <i class='bx bxs-chevron-down htmlcss-arrow arrow'></i>
                    <ul class="htmlCss-sub-menu sub-menu">
                        <li><a href="transaksi.php">Transaksi</a></li>
                        <li><a href="#">Login Forms</a></li>
                        <li><a href="#">Card Design</a></li>
                        
                    </ul>
                </li>
            </ul>
        </div>
        <div class="icons">
        <div style="position: relative;">
  <a href="keranjang.php"><i class='bx bx-cart'></i></a>
  <span id="cart-count" style="
      position: absolute;
      top: -8px;
      right: -10px;
      background: red;
      color: white;
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 50%;
  "><?= $cartCount ?></span>
</div>
<div class="profile-container" onclick="toggleDropdown()">
        <img src="uploads/<?php echo $image; ?>" alt="image" class="image">
        <div class="dropdown-menu">
            <a href="profil.php">Profil</a>
            <a href="logout.php">Logout</a>
        </div>
        </div>
    </div>
</nav>
<div class="scan-barcode" style="text-align:center; margin-top: 10px;">
    <a href="produk.php?scan=1" class="category-btn" style="background-color:#1abc9c;">Scan Barcode</a>
</div>
<?php if (isset($_GET['scan'])): ?>
    <div style="text-align:center; margin: 20px;">
        <form method="get" action="produk.php">
            <input type="text" name="barcode" placeholder="Scan atau ketik barcode..." autofocus style="padding: 10px; width: 250px; font-size: 16px;">
            <button type="submit" style="padding: 10px;">Cari</button>
        </form>
    </div>
<?php endif; ?>
<a href="barcode.php" style="
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #4CAF50;
  color: white;
  padding: 15px 20px;
  border: none;
  border-radius: 50px;
  font-size: 16px;
  text-decoration: none;
  box-shadow: 0 4px 8px rgba(0,0,0,0.3);
  z-index: 1000;
">
  Scan Barcode
</a>


<!-- Navbar Kategori -->
<div class="category-nav">
    <a href="produk.php" class="category-btn">Semua</a>

    <?php
    // Ambil semua kategori dari tabel kategori
    $queryKategori = "SELECT category FROM category";
    $resultKategori = mysqli_query($conn, $queryKategori);

    while ($row = mysqli_fetch_assoc($resultKategori)) {
        $namaKategori = htmlspecialchars($row['category']);
        echo '<a href="produk.php?kategori=' . urlencode(strtolower($namaKategori)) . '" class="category-btn">' . ucfirst($namaKategori) . '</a>';
    }
    ?>
</div>

<!-- Container Produk -->
<div class="product-container">
<?php if (empty($products)): ?>
    <p>Produk tidak tersedia.</p>
<?php else: ?>
    
<?php endif; ?>
    <?php foreach ($products as $product): ?>
        <div class="product-box">
    <img src="<?= $product['image'] ?>" alt="Produk">
    <h3><?= $product['product_name'] ?></h3>
    <p>Rp<?= $product['selling_price'] ?></p>

    <div class="overlay">
        <p class="head"><?= $product['product_name']; ?></p>
        <p class="head">Stok: <?= $product['qty']; ?></p>
        <p class="price">Rp<?= $product['selling_price'] ?></p>
                <div class="cart" onclick="addToCart(<?= $product['id']; ?>, '<?= htmlspecialchars($product['product_name'], ENT_QUOTES); ?>', <?= $product['selling_price']; ?>)">
    
<i class="fa fa-shopping-cart"></i>
<span id="cart-count" class="cart-badge" style="display: none;">0</span>

</div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script>
document.addEventListener("DOMContentLoaded", function(){
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const count = cart.reduce((acc, item) => acc + item.qty, 0);
  const badge = document.getElementById('cart-count');
  if (count > 0) {
    badge.style.display = 'inline';
    badge.innerText = count;
  } else {
    badge.style.display = 'none';
  }
})

document.addEventListener("click", function(event) {
    let dropdown = document.querySelector(".profile-container");
    if (!dropdown.contains(event.target)) {
        dropdown.classList.remove("active");
    }
});

$(document).ready(function() {
    $("#profile-pic").click(function(e) {
        e.stopPropagation();
        $("#dropdown-menu").toggle();
    });

    $(document).click(function(event) {
        if (!$(event.target).closest("#profile-container").length) {
            $("#dropdown-menu").hide();
        }
    });
});

// Fungsi untuk menampilkan jumlah total item di keranjang
function loadCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const count = cart.reduce((total, item) => total + item.qty, 0);
    const badge = document.getElementById('cart-count');
    if (badge) {
        if (count > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = count;
        } else {
            badge.style.display = 'none';
        }
    }
}

// Fungsi untuk menambah produk ke keranjang
function addToCart(id, name, price) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const index = cart.findIndex(item => item.id === id);

    if (index !== -1) {
        if (cart[index].qty < 5) {
            cart[index].qty += 1;
        } else {
            alert('Maksimal quantity per produk adalah 5');
        }
    } else {
        cart.push({ id: id, name: name, price: price, qty: 1, checked: true });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    loadCartCount(); // Perbarui badge
    alert(`${name} ditambahkan ke keranjang!`);
}

// Jalankan saat halaman selesai dimuat
window.onload = function() {
    loadCartCount();
};
</script>

</body>
</html>