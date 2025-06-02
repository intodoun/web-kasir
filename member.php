<?php
session_start();
include 'connection.php';
$admin_id = $_SESSION['admin_id']; // Ambil ID admin setelah dipastikan ada
$current_page = basename($_SERVER['PHP_SELF']); // contoh: 'produk.php'
// Ambil gambar profil admin
$query = mysqli_query($conn, "SELECT image FROM admin WHERE id = '$admin_id'");
$data = mysqli_fetch_assoc($query);
$image = !empty($data['image']) ? $data['image'] : 'default.jpg'; // Pakai gambar default jika tidak ada

// Ambil data members
$sql = "SELECT * FROM member";
$result = $conn->query($sql);
$today = date('Y-m-d');
$inactive_threshold = date('Y-m-d H:i:s', strtotime('-10 months'));

$query = "UPDATE member 
          SET status = 'inactive' 
          WHERE id IN (
              SELECT m.id FROM member m
              LEFT JOIN (
                  SELECT fid_member, MAX(date) AS last_transaction
                  FROM transactions
                  GROUP BY fid_member
              ) t ON m.id = t.fid_member
              WHERE (t.last_transaction IS NULL OR t.last_transaction < ?)
                AND m.status != 'inactive'
          )";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $inactive_threshold);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member List</title>
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
        .add-button {
            padding: 8px 18px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            border-radius: 5px;
            margin-left: 880px;
           font-size: 10px;;
        }

        .add-button:hover {
            background-color:rgb(54, 58, 55);
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
        }  nav {
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

<!-- Navbar -->
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
                <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'text-gray-500' : 'text-black hover:text-gray-600' ?>">BERANDA</a></li>
                <li><a href="produk.php">PRODUK</a></li>
                <li><a href="member.php" class="<?= $current_page == 'member.php' ? 'text-gray-500' : 'text-black hover:text-gray-600' ?>">MEMBER</a></li>
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

<!-- Konten -->
<div class="content">
    <h2>Daftar Member</h2>
    <a href="create-member.php" class="add-button">Create Member</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Phone</th>
            <th>Point</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['phone'] ?></td>
                    <td><?= $row['point'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a href="edit-member.php?id=<?= $row['id'] ?>">Edit</i></a> | 
                        <a href="delete-member.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus member ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="empty-message">Tidak ada data member.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>