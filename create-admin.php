<?php
include 'connection.php'; // Pastikan file connection.php ada dan benar

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']); // Hash password untuk keamanan

    // Cek apakah email atau username sudah digunakan
    $checkQuery = "SELECT * FROM admin WHERE email = '$email'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email atau Username sudah terdaftar!'); window.location='create-admin.php';</script>";
    } else {
        // Handle Upload Gambar
        $imageName = "";
        if (!empty($_FILES["image"]["name"])) {
            $targetDir = "uploads/";
            $imageName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . $imageName;
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath);
        }

        // Masukkan data ke database
        $sql = "INSERT INTO admin (email, username, password, image) VALUES ('$email', '$username', '$password', '$imageName')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Admin berhasil ditambahkan!'); window.location='profil.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin</title>
    <style>
     body {
            font-family: "DM Sans", sans-serif;
            line-height: 1.5;
            background-color: #f1f3fb;
            padding: 0 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        input:focus, select:focus, textarea:focus {
    outline: none;
    box-shadow: none;
}

        input[type="text"],
input[type="email"],
input[type="password"],
input[type="file"],
select,
textarea {
    outline: none !important;
    box-shadow: none !important;
    border-color: #999 !important; /* Warna abu-abu sesuai tema kamu */
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
input[type="file"]:focus,
select:focus,
textarea:focus {
    border-bottom: 2px solid #999 !important;
    box-shadow: none !important;
}

        .card {
            background-color: #FFF;
            border-radius: 10px;
            box-shadow: 0 10px 20px 0 rgba(0, 0, 0, .1);
            padding: 2rem;
            max-width: 425px;
            width: 100%;
        }

        .card-heading {
            font-size: 1.75rem;
            font-weight: 700;
            color: #666;
            margin-bottom: 2rem;
            text-align: center;
        }

        .input {
            display: flex;
            flex-direction: column-reverse;
            position: relative;
            padding-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .input-label {
          
            position: absolute;
            top: 1.5rem;
            transition: .25s ease;
        }

        .card-heading {
            font-size: 1.75rem;
            font-weight: 700;
            color: #666;
            margin-bottom: 2rem;
            text-align: center;
        }
        .input-field {
    border: 0;
    z-index: 1;
    background-color: transparent;
    border-bottom: 2px solid #eee;
    font: inherit;
    font-size: 1.125rem;
    padding: .25rem 0;
    color: #333; /* Warna teks */
}

.input-field:focus,
.input-field:valid {
    outline: none; /* Ini penting buat hilangin garis biru */
    border-bottom-color: #999; /* Warna abu-abu */
    box-shadow: none; /* Pastikan tidak ada efek highlight biru */
}

.input-field:focus + .input-label,
.input-field:valid + .input-label {
    color: #999;
    transform: translateY(-1.5rem);
}


        .action {
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
            cursor: pointer;
        }

        .card-info {
            padding-top: 1rem;
            text-align: center;
            font-size: .875rem;
            color: #8597a3;
        }
    </style>
</head>
<body>
<div class="card">
    <h2 class="card-heading">Tambah Admin</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="input">
            <input type="email" name="email" class="input-field" required>
            <label class="input-label">Email</label>
        </div>
        <div class="input">
            <input type="text" name="username" class="input-field" required>
            <label class="input-label">Username</label>
        </div>
        <div class="input">
            <input type="password" name="password" class="input-field" required>
            <label class="input-label">Password</label>
        </div>
        <div class="input">
            <input type="file" name="image" class="input-field" accept="image/*">
            <label class="input-label">Gambar</label>
        </div>
        <div class="action">
            <button type="submit" class="action-button">Tambah Admin</button>
        </div>
    </form>
    <div class="card-info">
        <p>Pastikan data admin sudah benar sebelum disimpan.</p>
    </div>
</div>
</body>
</html>
