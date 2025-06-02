<?php
session_start();
include 'connection.php'; // Pastikan file koneksi benar
require_once 'vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_POST['submit'])) {
    $product_name = $_POST['product_name'];
    $qty = $_POST['qty'];
    $starting_price = $_POST['starting_price'];
    $selling_price = $_POST['selling_price'];
    $margin = $selling_price - $starting_price;

    $fid_category = $_POST['fid_category'];
    $description = $_POST['description'];

    // Generate Barcode
    $barcode = uniqid(); // Bisa diganti dengan metode lain
    $generator = new BarcodeGeneratorPNG();

    // Menambahkan ukuran dan margin barcode untuk kualitas lebih baik
    $barcodeImage = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 3, 50); // Ukuran lebih besar dan lebih tinggi

    // Menggunakan folder uploads/ untuk menyimpan barcode
    $barcodeFolder = __DIR__ . '/uploads/';  // Ganti ke folder uploads
    if (!file_exists($barcodeFolder)) {
        mkdir($barcodeFolder, 0777, true);
    }

    // Simpan barcode dalam folder uploads/
    $barcodePath = $barcodeFolder . $barcode . '.png';
    file_put_contents($barcodePath, $barcodeImage);

    // Upload gambar produk
    $image = uploadProduk();
    if (!$image) {
        return false;
    }

    // Menggunakan prepared statement untuk keamanan query
    $stmt = $conn->prepare("INSERT INTO products (product_name, barcode, qty, starting_price, selling_price, margin, fid_category, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiissss", $product_name, $barcode, $qty, $starting_price, $selling_price, $margin, $fid_category, $description, $image);

    if ($stmt->execute()) {
        echo "<script>
                alert('Produk berhasil ditambahkan!');
                window.location.href='data-produk.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan: " . $stmt->error . "');
              </script>";
    }

    $stmt->close();
}

// Fungsi Upload
function uploadProduk() {
    $namaFile = $_FILES['image']['name'];
    $ukuranFile = $_FILES['image']['size'];
    $error = $_FILES['image']['error'];  
    $tmpName = $_FILES['image']['tmp_name'];

    if ($error === 4) {
        echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
        return false;
    }

    if ($ukuranFile > 2000000) {
        echo "<script>alert('Ukuran gambar terlalu besar! (Maks 2MB)');</script>";
        return false;
    }

    $fileExt = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($fileExt, $allowedExt)) {
        echo "<script>alert('Format file tidak didukung!');</script>";
        return false;
    }

    $newFileName = uniqid() . '.' . $fileExt;
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($tmpName, $uploadPath)) {
        return 'uploads/' . $newFileName;
    } else {
        echo "<script>alert('Gagal mengupload file!');</script>";
        return false;
    }
}

$kategoriList = [];
$queryKategori = mysqli_query($conn, "SELECT id, category FROM category");
while ($row = mysqli_fetch_assoc($queryKategori)) {
    $kategoriList[] = $row;
}
?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <style>
        body {
            font-family: "DM Sans", sans-serif;
            line-height: 1.5;
            background-color: #f1f3fb;
            padding: 0 2rem;
        }
        input, select {
    width: 100%;
    padding: 10px 12px;
    margin-top: 2px;
    border: 1px solid #ccc; /* Border lebih halus */
    border-radius: 5px;
}

input:focus, select:focus {
    border-color: #555; /* Ubah warna border saat focus */
    outline: none;
}

input[type="file"] {
    border: 1px solid #ccc; /* Sama dengan input lainnya */
    padding: 10px 12px;
    margin-top: 2px;
    border-radius: 5px;
}

        .card {
            margin: 2rem auto;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 900px;
            background-color: #FFF;
            border-radius: 10px;
            box-shadow: 0 10px 20px 0 rgba(0, 0, 0, .1);
            padding: .75rem;
        }

        .card-image {
            border-radius: 8px;
            overflow: hidden;
            padding-bottom: 35%;
            background-image: url('https://assets.codepen.io/285131/coffee_1.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card-heading {
            font-size: 1.75rem;
            font-weight: 700;
            color: #666;
            margin-bottom: 2rem;
            text-align: center;
        }

        .card-heading small {
            display: block;
            font-size: .75em;
            font-weight: 400;
            margin-top: .25em;
        }

        .card-form {
            padding: 2rem 1rem 0;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .input-group {
            flex: 1 1 45%;
        }

        .input {
            display: flex;
            flex-direction: column-reverse;
            position: relative;
            padding-top: 1.5rem;
        }

        .input-field {
            border: 0;
            z-index: 1;
            background-color: transparent;
            border-bottom: 2px solid #eee;
            font: inherit;
            font-size: 1.125rem;
            padding: .25rem 0;
        }

        .input-field {
            border: 0;
            z-index: 1;
            background-color: transparent;
            border-bottom: 2px solid #eee;
            font: inherit;
            font-size: 1.125rem;
            padding: .25rem 0;
        }

         .input-field:focus,
        .input-field:valid {
            outline: 0;
            border-bottom-color: #999;
        }

        .input-field:focus + .input-label,
        .input-field:valid + .input-label {
            color: #999;
            transform: translateY(-1.5rem);
        }
        .action {
            width: 100%;
            margin-top: 2rem;
        }

        .action-button {
            font: inherit;
            font-size: 1.25rem;
            padding: 1em;
            width: 100%;
            font-weight: 500;
            background-color: #999;
            border-radius: 6px;
            color: #FFF;
            border: 0;
        }

       .card-info {
            padding: 1rem 1rem;
            text-align: center;
            font-size: .875rem;
            color: #8597a3;
        }

        
    </style>
</head>
<body>
<div class="container">
   <div class="card">
     
        <h2 class="card-heading">Tambah Produk</h2>
        <form class="card-form" action="" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <div class="input">
                    <input type="text" name="product_name" class="input-field" required>
                    <label class="input-label">Nama Produk</label>
                </div>
                  
            <div class="input-group">
                <div class="input">
                    <input type="number" name="qty" class="input-field" required>
                    <label class="input-label">Stok</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="text" name="starting_price" class="input-field" required>
                    <label class="input-label">Harga Beli</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="text" name="selling_price" class="input-field" required>
                    <label class="input-label">Harga Jual</label>
                </div>
            </div>
        
            </div>
            <div class="input-group">
                <div class="input">
                    <select name="fid_category" class="input-field" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <?php foreach ($kategoriList as $kategori): ?>
                            <option value="<?= $kategori['id'] ?>"><?= htmlspecialchars($kategori['category']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="input-label">Kategori</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="file" name="image" class="input-field" accept="image/*" required>
                    <label class="input-label">Gambar</label>
                </div>
            </div>
            <div class="input-group" style="flex: 1 1 100%;">
                <div class="input">
                    <input type="text" name="description" class="input-field" required>
                    <label class="input-label">Deskripsi</label>
                </div>
            </div>
            <div class="action">
                <button type="submit" name="submit" class="action-button">Tambah Produk</button>
            </div>
        </form>
        <div class="card-info">
            <p>Pastikan data sudah benar sebelum disimpan</p>
        </div>
    </div>
</div>
</body>
</html>