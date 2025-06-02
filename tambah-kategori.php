<?php
include 'connection.php';

if (isset($_POST['submit'])) {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Cek apakah nama kategori sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM category WHERE category = '$category'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Nama kategori sudah digunakan, silakan gunakan nama lain.'); window.history.back();</script>";
        exit;
    }
    
    $image = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $folder = 'uploads/';

    if (move_uploaded_file($tmp_name, $folder . $image)) {
        $query = "INSERT INTO category (category, image) VALUES ('$category', '$image')";
        $insert = mysqli_query($conn, $query);

        if ($insert) {
            echo "<script>alert('Kategori berhasil ditambahkan!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan kategori.');</script>";
        }
    } else {
        echo "<script>alert('Gagal upload gambar.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
    <style>
        body {
            font-family: "DM Sans", sans-serif;
            line-height: 1.5;
            background-color: #f1f3fb;
            padding: 0 2rem;
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
            flex-direction: column;
            gap: 1.5rem;
        }

        .input-group {
            width: 100%;
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

        .input-label {
            position: absolute;
            top: 1.5rem;
            left: 0;
            font-size: 1rem;
            color: #aaa;
            pointer-events: none;
            transition: all 0.2s ease;
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
        <h2 class="card-heading">Tambah Kategori</h2>
        <form class="card-form" action="" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <div class="input">
                    <input type="text" name="category" class="input-field" required>
                    <label class="input-label">Nama Kategori</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="file" name="image" class="input-field" accept="image/*" required>
                    <label class="input-label">Upload Gambar</label>
                </div>
            </div>
            <div class="action">
                <button type="submit" name="submit" class="action-button">Simpan</button>
            </div>
        </form>
        <div class="card-info">
            <p>Pastikan nama kategori dan gambar sudah sesuai.</p>
        </div>
    </div>
</div>
</body>
</html>
