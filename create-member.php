<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $point = mysqli_real_escape_string($conn, $_POST['point']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);


    // Cek jika data sudah ada
    $query = mysqli_query($conn, "SELECT * FROM member WHERE name = '$name'");

    if ($query === false) {
        echo "Error: " . mysqli_error($conn);
    } else {
        if (mysqli_num_rows($query) > 0) {
            // Data sudah ada, arahkan ke halaman lain
            header("Location: member.php");
            exit;
        } else {
            // Data belum ada, lakukan insert
            $insert = mysqli_query($conn, "INSERT INTO member (name, phone, status) VALUES ( '$name', '$phone',  '$status')");
            if ($insert) {
                header("Location: member.php");
                exit;
            } else {
                echo "Gagal menambahkan data: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>create Member</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #E0E0E0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #F0F0F0;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 320px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-weight: 600;
            display: block;
            margin-top: 12px;
            color: #444;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #B0B0B0;
            border-radius: 6px;
            background: #FFF;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #555;
            color: white;
            border: none;
            border-radius: 6px;
            margin-top: 20px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        button:hover {
            background-color: #444;
        }
        </style>
</head>
<body>
<div class="container">
        <h2>Update Member</h2>
        <form action="create-member.php" method="POST">
            

            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" required>

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>

            <button type="submit">create</button>
        </form>
    </div>
</body>
</html>
