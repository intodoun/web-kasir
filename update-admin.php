<?php
session_start();
include 'connection.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$loggedInId = $_SESSION['admin_id'];

// Ambil ID admin yang mau diedit
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM admin WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Admin tidak ditemukan.");
}
$row = $result->fetch_assoc();

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $updates = ["username='$username'"];

    // Kalau admin sedang login edit dirinya sendiri
    if ($id === $loggedInId) {
        $email = $conn->real_escape_string($_POST['email']);
        $updates[] = "email='$email'";

        // Gambar
        if (!empty($_FILES["image"]["name"])) {
            $targetDir = "uploads/";
            $imageName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . $imageName;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $updates[] = "image='$imageName'";
            }
        }

        // Password
        $password = $_POST['password'] ?? '';
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updates[] = "password='$hashedPassword'";
        }
    }

    $updateQuery = "UPDATE admin SET " . implode(", ", $updates) . " WHERE id=$id";
    if ($conn->query($updateQuery) === TRUE) {
        echo "<script>alert('Admin berhasil diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!-- ===== HTML Form ===== -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Admin</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f2f2f2;
        padding: 40px;
    }
    .form-box {
        max-width: 420px;
        margin: auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #444;
        margin-bottom: 25px;
    }
    label {
        display: block;
        color: #555;
        margin-bottom: 5px;
        font-weight: bold;
    }
    input[type="text"],
    input[type="email"],
    input[type="file"],
    input[type="password"] {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 6px;
        background-color: #f9f9f9;
        color: #333;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="file"]:focus,
    input[type="password"]:focus {
        border-color: #999;
        background-color: #fff;
        outline: none;
    }
    button {
        background: #666;
        color: white;
        padding: 12px;
        border: none;
        width: 100%;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }
    button:hover {
        background: #444;
    }
    img {
        display: block;
        margin-top: 10px;
        border-radius: 4px;
    }
</style>
</head>
<body>
<div class="form-box">
    <h2>Update Admin</h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if ($id === $loggedInId): ?>
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>
        <?php endif; ?>

        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>

        <?php if ($id === $loggedInId): ?>
            <label>Password (opsional):</label>
            <input type="password" name="password" placeholder="Isi jika ingin ubah password">

            <label>Gambar:</label>
            <input type="file" name="image">
            <?php if (!empty($row['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="80" style="margin-top: 10px;">
            <?php endif; ?>
        <?php endif; ?>

        <button type="submit">Update</button>
    </form>
</div>
</body>
</html>
