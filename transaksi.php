<?php
session_start();
include 'connection.php';

$filter_type = $_GET['filter_type'] ?? '';
$params = [];
$param_types = "";
$where_clause = "";
$judul = "Laporan Transaksi";
$total_penjualan = 0;
$total_keuntungan = 0;
$total_modal = 0;
$rows = [];
 $queryString = http_build_query($_GET);

// Filter logic
$whereClause = "";

if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];

    if ($filter === 'date' && !empty($_GET['date'])) {
        $selectedDate = $_GET['date'];
        $whereClause = "WHERE DATE(t.date) = '$selectedDate'";

    } elseif ($filter === 'day') {
        $today = date('Y-m-d');
        $whereClause = "WHERE DATE(t.date) = '$today'";

    } elseif ($filter === 'week') {
        $monday = date('Y-m-d', strtotime('monday this week'));
        $sunday = date('Y-m-d', strtotime('sunday this week'));
        $whereClause = "WHERE DATE(t.date) BETWEEN '$monday' AND '$sunday'";

    } elseif ($filter === 'month') {
        $month = date('m');
        $year = date('Y');
        $whereClause = "WHERE MONTH(t.date) = '$month' AND YEAR(t.date) = '$year'";
    }
}


// QUERY: ambil semua data transaksi + join
$query = "SELECT t.*, m.name AS member_name, a.username AS admin_username
          FROM transactions t
          LEFT JOIN member m ON t.fid_member = m.id
          LEFT JOIN admin a ON t.fid_admin = a.id
          $whereClause
          ORDER BY t.date DESC";


$stmt = $conn->prepare($query);

if ($param_types !== "") {
  $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $row['total_price'] = (float)($row['total_price'] ?? 0);
  $row['margin_total'] = (float)($row['margin_total'] ?? 0);
  $row['modal'] = $row['total_price'] - $row['margin_total'];

  $total_penjualan += $row['total_price'];
  $total_keuntungan += $row['margin_total'];
  $total_modal += $row['modal'];

  $rows[] = $row;
}
$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* Agar tabel tidak ketutupan navbar */
        .content {
            margin-top: 100px;
            width: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #C0C0C0;
            color: white;
        }

        td a {
            text-decoration: none;
            color: #007bff;
            margin: 0 5px;
        }

        td a:hover {
            text-decoration: underline;
        }

        .empty-message {
            text-align: center;
            padding: 20px;
            font-size: 16px;
            color: #888;
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
            <i class='bx bx-cart'></i>
            <img src="profile.jpg" alt="Profile" class="profile-pic">
        </div>
    </div>
</nav>
<div class="content">
    <h2><?= $judul ?></h2>

    <form method="GET" style="margin-top: 20px; display: flex; gap: 10px; align-items: center;">
        <select name="filter" onchange="this.form.submit()">
            <option value="">-- Pilih Filter --</option>
            <option value="day" <?= ($_GET['filter'] ?? '') == 'day' ? 'selected' : '' ?>>Hari Ini</option>
            <option value="week" <?= ($_GET['filter'] ?? '') == 'week' ? 'selected' : '' ?>>Minggu Ini</option>
            <option value="month" <?= ($_GET['filter'] ?? '') == 'month' ? 'selected' : '' ?>>Bulan Ini</option>
            <option value="date" <?= ($_GET['filter'] ?? '') == 'date' ? 'selected' : '' ?>>Pilih Tanggal</option>
        </select>

        <?php if (($_GET['filter'] ?? '') == 'date'): ?>
            <input type="date" name="date" value="<?= $_GET['date'] ?? '' ?>" onchange="this.form.submit()">
        <?php endif; ?>
    </form>
<form method="GET" action="download_transaksi.php" target="_blank" style="display:inline;">
    <input type="hidden" name="filter" value="<?= $_GET['filter'] ?? '' ?>">
    <?php if (isset($_GET['date'])): ?>
        <input type="hidden" name="date" value="<?= $_GET['date'] ?>">
    <?php endif; ?>
    <button type="submit">Download PDF</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Admin</th>
            <th>Member</th>
             <th>Detail</th>
            <th>Total Penjualan</th>
            <th>Keuntungan</th>
            <th>Modal</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($rows) === 0): ?>
            <tr><td colspan="8" style="text-align:center;">Data transaksi kosong</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['date']); ?></td>
                    <td><?= htmlspecialchars($row['admin_username']); ?></td>
                    <td><?= htmlspecialchars($row['member_name'] ?? '-'); ?></td>
                    <td><?= nl2br(htmlspecialchars($row['detail'])); ?></td>
                    <td>Rp<?= number_format($row['total_price'], 0, ',', '.'); ?></td>
                    <td>Rp<?= number_format($row['margin_total'], 0, ',', '.'); ?></td>
                    <td>Rp<?= number_format($row['modal'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" style="text-align:right;">Total:</th>
            <th>Rp<?= number_format($total_penjualan, 0, ',', '.'); ?></th>
            <th>Rp<?= number_format($total_keuntungan, 0, ',', '.'); ?></th>
            <th>Rp<?= number_format($total_modal, 0, ',', '.'); ?></th>
        </tr>
    </tfoot>
</table>
</div>
<script>
  function toggleDateInput() {
    const filter = document.getElementById("filter").value;
    const dateInput = document.getElementById("dateInput");
    dateInput.style.display = (filter === "date") ? "inline-block" : "none";
  }

  // Panggil saat halaman dimuat agar sesuai filter terpilih
  window.onload = toggleDateInput;
function toggleFilter() {
    const filter = document.getElementById('filter').value;
    document.getElementById('tanggal').parentElement.style.display = filter === 'date' ? 'block' : 'none';
    document.getElementById('bulan').parentElement.style.display = filter === 'month' ? 'block' : 'none';
    document.getElementById('tahun').parentElement.style.display = (filter === 'month' || filter === 'year') ? 'block' : 'none';
}

window.onload = toggleFilter;
</script>

</body>
</html>

