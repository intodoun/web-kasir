<?php
session_start();
include 'connection.php';

$category_id = $_GET['id'];
$sql = "SELECT * FROM category WHERE id = '$category_id'";
$result = mysqli_query($conn, $sql);
$category = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $name = $_POST['category'];
    $image = $category['image']; // default image lama

    // Cek jika ada file baru diupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        // Upload file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName; // update nama file ke database
        }
    }

    $sql = "UPDATE category SET
                category = '$name',
                image = '$image'
            WHERE id = '$category_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Kategori berhasil diperbarui!');
                window.location.href='index.php';
              </script>";
    } else {
        echo "<script>
                alert('Terjadi kesalahan: " . mysqli_error($conn) . "');
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
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
    flex-direction: column; /* Biar form ke bawah */
    gap: 1.5rem;
}

.input-group {
    width: 100%; /* Biar full-width per input */
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
        <h2 class="card-heading">Edit Kategori</h2>
        <form class="card-form" action="" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <div class="input">
                    <input type="text" name="category" class="input-field" value="<?= $category['category'] ?>" required>
                    <label class="input-label">Nama Kategori</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="file" name="image" class="input-field">
                    <label class="input-label">Upload Gambar (kosongkan jika tidak diubah)</label>
                </div>
            </div>
            <div class="action">
                <button type="submit" name="submit" class="action-button">Perbarui Kategori</button>
            </div>
        </form>
        <div class="card-info">
            <p>Gambar sekarang: <strong><?= $category['image'] ?></strong></p>
            <p>Pastikan file gambar diunggah dengan ukuran wajar.</p>
        </div>
    </div>
</div>
</body>
</html>
