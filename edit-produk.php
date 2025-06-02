<?php
session_start();
include 'connection.php'; // Pastikan file koneksi benar

// Ambil ID produk dari URL
$product_id = $_GET['id'];

// Ambil data produk berdasarkan ID
$sql = "SELECT * FROM products WHERE id = '$product_id'";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

// Jika form di-submit
if (isset($_POST['submit'])) {
    $product_name = $_POST['product_name'];
    $barcode = $_POST['barcode'];
    $qty = $_POST['qty'];
    $starting_price = $_POST['starting_price'];
    $selling_price = $_POST['selling_price'];
    $margin = $_POST['margin'];
    $fid_category = $_POST['fid_category'];
    $description = $_POST['description'];
 // Tambah stok baru ke stok lama
    $qty = $product['qty'] + $_POST['qty'];

    $starting_price = $_POST['starting_price'];
    $selling_price = $_POST['selling_price'];
    $margin = $_POST['margin'];
    $fid_category = $_POST['fid_category'];
    $description = $_POST['description'];
    // Cek apakah gambar baru di-upload
    $image = $product['image']; // Gunakan gambar lama jika tidak ada gambar baru
    if ($_FILES['image']['error'] !== 4) {
        $image = uploadProduk(); // Upload gambar baru
        if (!$image) {
            return false;
        }
    }
    if (empty($barcode)) {
        echo "<script>alert('Barcode tidak boleh kosong!'); window.history.back();</script>";
        exit;
    }
    
    // Update data produk
    $sql = "UPDATE products SET
                product_name = '$product_name',
                barcode = '$barcode',
                qty = '$qty',
                starting_price = '$starting_price',
                selling_price = '$selling_price',
                margin = '$margin',
                fid_category = '$fid_category',
                description = '$description',
                image = '$image'
            WHERE id = '$product_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Produk berhasil diperbarui!');
                window.location.href='data-produk.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan: " . mysqli_error($conn) . "');
              </script>";
    }
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
    <title>Edit Produk</title>
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
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input:focus, select:focus {
            border-color: #555;
            outline: none;
        }

        input[type="file"] {
            border: 1px solid #ccc;
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

        .card-heading {
            font-size: 1.75rem;
            font-weight: 700;
            color: #666;
            margin-bottom: 2rem;
            text-align: center;
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
        <h2 class="card-heading">Edit Produk</h2>
        <form class="card-form" action="" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <div class="input">
                    <input type="text" name="product_name" class="input-field" value="<?= $product['product_name'] ?>" required>
                    <label class="input-label">Nama Produk</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                <input type="hidden" name="barcode" value="<?= $product['barcode'] ?>">


                    <label class="input-label">Barcode</label>
                </div>
            </div>  
            <div class="input-group">
                <div class="input">
                    <input type="number" name="qty" class="input-field" value="0" required>

                    <label class="input-label">Tambah Stok (+)</label>
                    <p>Stok saat ini: <?= $product['qty'] ?></p>


                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="number" name="starting_price" class="input-field" value="<?= $product['starting_price'] ?>" required>
                    <label class="input-label">Harga Beli</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="number" name="selling_price" class="input-field" value="<?= $product['selling_price'] ?>" required>
                    <label class="input-label">Harga Jual</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="text" name="margin" class="input-field" value="<?= $product['margin'] ?>" required>
                    <label class="input-label">Margin</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <select name="fid_category" class="input-field" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <?php foreach ($kategoriList as $kategori): ?>
                            <option value="<?= $kategori['id'] ?>" <?= $kategori['id'] == $product['fid_category'] ? 'selected' : '' ?>>
    <?= htmlspecialchars($kategori['category']) ?>
</option>

                        <?php endforeach; ?>
                    </select>
                    <label class="input-label">Kategori</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="file" name="image" class="input-field" accept="image/*">
                    <label class="input-label">Gambar (Kosongkan jika tidak ingin mengganti)</label>
                </div>
            </div>
            <div class="input-group" style="flex: 1 1 100%;">
                <div class="input">
                    <input type="text" name="description" class="input-field" value="<?= $product['description'] ?>" required>
                    <label class="input-label">Deskripsi</label>
                </div>
            </div>
            <div class="action">
                <button type="submit" name="submit" class="action-button">Perbarui Produk</button>
            </div>
        </form>
        <div class="card-info">
            <p>Pastikan data sudah benar sebelum diperbarui</p>
        </div>
    </div>
</div>
</body>
</html>
