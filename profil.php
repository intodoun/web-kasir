<?php
session_start();
include 'connection.php'; // Koneksi ke database
$admin_id = $_SESSION['admin_id']; // Ambil ID admin setelah dipastikan ada

// Ambil gambar profil admin
$query = mysqli_query($conn, "SELECT image FROM admin WHERE id = '$admin_id'");
$data = mysqli_fetch_assoc($query);
$image = !empty($data['image']) ? $data['image'] : 'default.jpg'; // Pakai gambar default jika tidak ada

// Ambil data admin dari database
$sql = "SELECT * FROM admin";
$result = $conn->query($sql);

$admin_id = $_SESSION['admin_id']; // Ambil ID admin setelah dipastikan ada

// Ambil gambar profil admin
$query = mysqli_query($conn, "SELECT image FROM admin WHERE id = '$admin_id'");
$data = mysqli_fetch_assoc($query);
$image = !empty($data['image']) ? $data['image'] : 'default.jpg'; // Pakai gambar default jika tidak ada

// Ambil data admin yang sedang login
$loggedInAdminId = $_SESSION['admin_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="utf-8">
      <title>Profil Admin</title>
      <link rel="stylesheet" href="style.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
      <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
      <style>
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
body{
    margin-top:20px;
    background: #f6f9fc;
}
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            margin-top: 20px;
            background: #f6f9fc;
        }
        .image{
    width: 40px;  /* Sesuaikan dengan ukuran icon cart */
    height: 40px;
    border-radius: 50%; /* Biar jadi lingkaran */
    object-fit: cover; /* Biar gak gepeng */
    margin-left: 15px; /* Jarak dari icon cart */
}
        .container {
    max-width: 1200px;
    margin: 80px auto 20px; /* Tambahkan margin atas agar tidak tertutup navbar */
    text-align: left;
    padding-top: 80px;
}
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .admin-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .admin-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            width: 270px;
            text-align: center;
        }
        .admin-card img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
        }
        .admin-card .logged-in {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn {
            padding: 7px 12px;
            cursor: pointer;
            border-radius: 5px;
            margin: 5px;
        }
        .btn-danger { background-color: red; color: white; border: none; }
        .btn-danger:hover { background-color: darkred; }
        .btn-edit { background-color: blue; color: white; border: none; }
        .btn-edit:hover { background-color: darkblue; }
        .btn-create {
            background-color: #99a3a4 ;
            color: white;
            border: none;
            padding: 6px 14px;
            font-size: 12px;
            margin-right: 80px;
        }
        .btn-create:hover { background-color: #616a6b; }

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
    <div class="profile-container" onclick="toggleDropdown()">
        <img src="uploads/<?php echo $image; ?>" alt="image" class="image">
        <div class="dropdown-menu">
            <a href="profil.php">Profil</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

</nav>
<div class="container">
        <div class="header">
            <h2>Profil Admin</h2>
            <a href="create-admin.php" class="btn btn-create">Tambah Admin</a>
        </div>
        <div class="admin-list">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="admin-card">
                    <img src="uploads/<?php echo !empty($row['image']) ? $row['image'] : 'default.jpg'; ?>" alt="image">
                    <h5><?= $row['username'] ?></h5>
                    <p><?= $row['email'] ?></p>
                    <?php if ($row['id'] == $loggedInAdminId) { ?>
                        <div class="logged-in">Sedang Login</div>
                    <?php } ?>
                    <a href="update-admin.php?id=<?= $row['id'] ?>" class="btn btn-edit">Edit</a>
                    <?php if ($row['id'] != $loggedInAdminId) { ?>
                        <form method="POST" action="delete-admin.php" style="display:inline;" onsubmit="return confirmDelete(event, '<?= $row['username'] ?>')">
                            <input type="hidden" name="admin_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function confirmDelete(event, username) {
    event.preventDefault(); // Mencegah form terkirim langsung
    if (confirm("Apakah Anda yakin ingin menghapus admin '" + username + "'?")) {
        event.target.submit(); // Kirim form jika user menekan "OK"
    }
}
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

        </script>
</body>
</html>
