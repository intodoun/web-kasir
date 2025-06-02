<?php
session_start();
include 'connection.php'; // Pastikan ada koneksi ke database

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    echo "Silakan login dulu!";
    exit;
}

$admin_id = $_SESSION['admin_id']; // Ambil ID admin setelah dipastikan ada

// Ambil gambar profil admin
$query = mysqli_query($conn, "SELECT image FROM admin WHERE id = '$admin_id'");
$data = mysqli_fetch_assoc($query);
$image = !empty($data['image']) ? $data['image'] : 'default.jpg'; // Pakai gambar default jika tidak ada
$query1 = mysqli_query($conn, "SELECT SUM(qty) AS total_stok FROM products");
$data1 = mysqli_fetch_assoc($query1);
$totalStok = $data1['total_stok'] ?? 0;

// Total margin
$query2 = mysqli_query($conn, "SELECT SUM(margin_total) AS total_margin FROM transactions");
$data2 = mysqli_fetch_assoc($query2);
$totalMargin = $data2['total_margin'] ?? 0;

// Total penjualan
$query3 = mysqli_query($conn, "SELECT SUM(total_price) AS total_penjualan FROM transactions");
$data3 = mysqli_fetch_assoc($query3);
$totalPenjualan = $data3['total_penjualan'] ?? 0;

// Ambil data penjualan berdasarkan filter(chart)
$filter = $_GET['filter'] ?? 'month';
$data = [];
$labels = [];

if ($filter == 'day') {
    $query = "SELECT DATE(date) as label, SUM(total_price) as total 
              FROM transactions 
              GROUP BY DATE(date) 
              ORDER BY DATE(date) ASC";
} elseif ($filter == 'week') {
    $query = "SELECT CONCAT(YEAR(date), '-W', WEEK(date, 1)) as label, SUM(total_price) as total 
              FROM transactions 
              GROUP BY YEAR(date), WEEK(date, 1) 
              ORDER BY YEAR(date), WEEK(date, 1) ASC";
} else { // default: month
    $query = "SELECT DATE_FORMAT(date, '%M %Y') as label, SUM(total_price) as total 
              FROM transactions 
              GROUP BY YEAR(date), MONTH(date) 
              ORDER BY YEAR(date), MONTH(date) ASC";
}

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['label'];
    $data[] = (float)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Beranda-BuyKasir</title>
    <link rel="stylesheet" href="style.css">
    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    
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
        .box-container {
            display: flex;
            justify-content: space-around;
            margin-top: 80px;
            padding: 20px;
        }
        .box {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 30%;
            text-align: center;
        }
        .box h3 {
    font-size: 2em;
    font-weight: bold;
    color: #2c3e50;
}
        .box p {
            color: #666;
        }
        .chart-container {
            width: 80%;
            margin: 40px auto;
            text-align: center;
        }
        select, button {
            margin: 10px;
            padding: 6px 12px;
            font-size: 14px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            text-align: center;
            overflow-x: auto;
            white-space: nowrap;
        }
        .category-container {
            background: linear-gradient(to right, #1f2937, #374151);
            padding: 20px;
            border-radius: 8px;
            color: white;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .plus-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: white;
        }
        .product-grid {
            display: flex;
            gap: 16px;
            margin-top: 20px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .product-box {
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            min-width: 200px;
        }
        .product-box img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .product-box h2 {
            font-size: 1.2em;
            font-weight: bold;
        }
        .product-box p {
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 5px;
        }
        .product-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
        }
        .product-actions button {
            background: red;
            color: white;
            border: none;
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
        }
        .product-actions .edit {
            background: blue;
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
                        <li><a href="data-produk.php">Data Produk</a></li>
                        <li><a href="#">Card Design</a></li>
                        <li><a href="data-admin.php">Admin</a></li>
                        
                    </ul>
                </li>
            </ul>
        </div>
        <div class="icons">
    <i class='bx bx-cart'></i>
    <div class="profile-container" onclick="toggleDropdown()">
        <img src="uploads/<?php echo $image; ?>" alt="image" class="image">
        <div class="dropdown-menu">
            <a href="profil.php">Profil</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>
    

</nav>
<div class="box-container">
    <div class="box">
        <h3><?= number_format($totalStok) ?></h3>
        <p>Total Stok Produk</p>
    </div>
    <div class="box">
        <h3>Rp <?= number_format($totalMargin, 0, ',', '.') ?></h3>
        <p>Total Margin</p>
    </div>
    <div class="box">
        <h3>Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></h3>
        <p>Total Penjualan</p>
    </div>
</div>

<div class="chart-container">
    <h3>Grafik Penjualan</h3>
    <form method="get">
        <select name="filter" onchange="this.form.submit()">
            <option value="day" <?= $filter == 'day' ? 'selected' : '' ?>>Harian</option>
            <option value="week" <?= $filter == 'week' ? 'selected' : '' ?>>Mingguan</option>
            <option value="month" <?= $filter == 'month' ? 'selected' : '' ?>>Bulanan</option>
        </select>
    </form>

    <canvas id="myChart"></canvas>

    <button onclick="changePointStyle('circle')">Circle</button>
    <button onclick="changePointStyle('cross')">Cross</button>
    <button onclick="changePointStyle('star')">Star</button>
</div>
    <div class="container">
    <div class="category-container">
        <h1>KATEGORI</h1>
        <a href=tambah-kategori.php><span class="plus-icon">+</span></a>
        <div class="product-grid">
<?php
include 'connection.php';

$query = mysqli_query($conn, "SELECT * FROM category");
while ($row = mysqli_fetch_assoc($query)) :

    $cat_id = $row['id'];
    $check_product = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE fid_category = $cat_id");
    $product_data = mysqli_fetch_assoc($check_product);
    $has_product = $product_data['total'] > 0;
?>
    <div class="product-box">
        <div class="product-actions">
            <a href="edit-kategori.php?id=<?= $cat_id ?>"><button class="edit">✏</button></a>

            <?php if ($has_product): ?>
                <button class="delete" onclick="alert('Kategori tidak bisa dihapus karena masih digunakan oleh produk.')" style="opacity: 0.5; cursor: not-allowed;">🗑</button>
            <?php else: ?>
                <a href="delete-category.php?id=<?= $cat_id ?>" onclick="return confirm('Yakin mau hapus kategori ini?')"><button class="delete">🗑</button></a>
            <?php endif; ?>
        </div>
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['category']) ?>">
        <h2 style="color: black;"><?= htmlspecialchars($row['category']) ?></h2>
        <p style="color: black;">ID: <?= $cat_id ?></p>
    </div>
<?php endwhile; ?>
</div>
    </div>
</div>

    
    <script>
        function toggleDropdown() {
    document.querySelector(".profile-container").classList.toggle("active");
}

// Tutup dropdown jika klik di luar
document.addEventListener("click", function(event) {
    let dropdown = document.querySelector(".profile-container");
    if (!dropdown.contains(event.target)) {
        dropdown.classList.remove("active");
    }
});
const labels = <?= json_encode($labels); ?>;
    const data = <?= json_encode($data); ?>;

    const ctx = document.getElementById('myChart').getContext('2d');

    const chartData = {
        labels: labels,
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: data,
            backgroundColor: 'rgba(75, 192, 192, 0.4)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointStyle: 'circle',
            pointRadius: 8,
            pointHoverRadius: 12
        }]
    };

    const myChart = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function changePointStyle(style) {
        myChart.data.datasets.forEach(ds => ds.pointStyle = style);
        myChart.update();
    }
        $(document).ready(function() {
            $("#profile-pic").click(function() {
                $("#dropdown-menu").toggle();
            });

            $(document).click(function(event) {
                if (!$(event.target).closest("#profile-container").length) {
                    $("#dropdown-menu").hide();
                }
            });
        });
    </script>
</body>
</html>