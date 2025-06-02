<?php
session_start();
include 'connection.php';

$member_id = $_GET['id'];

// Ambil data member berdasarkan ID
$sql = "SELECT * FROM member WHERE id = '$member_id'";
$result = mysqli_query($conn, $sql);
$member = mysqli_fetch_assoc($result);

// Jika form di-submit
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $point = $_POST['point'];
    $status = $_POST['status'];

    $sql = "UPDATE member SET
                name = '$name',
                phone = '$phone',
                point = '$point',
                status = '$status'
            WHERE id = '$member_id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
                alert('Data member berhasil diperbarui!');
                window.location.href='member.php';
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
    <title>Edit Member</title>
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
        <h2 class="card-heading">Edit Member</h2>
        <form class="card-form" action="" method="POST">
            <div class="input-group">
                <div class="input">
                    <input type="text" name="name" class="input-field" value="<?= $member['name'] ?>" required>
                    <label class="input-label">Nama Member</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="text" name="phone" class="input-field" value="<?= $member['phone'] ?>" required>
                    <label class="input-label">No. Telepon</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <input type="number" name="point" class="input-field" value="<?= $member['point'] ?>" required>
                    <label class="input-label">Poin</label>
                </div>
            </div>
            <div class="input-group">
                <div class="input">
                    <select name="status" class="input-field" required>
                        <option value="" disabled>Status</option>
                        <option value="active" <?= $member['status'] == 'active' ? 'selected' : '' ?>>active</option>
                        <option value="inactive" <?= $member['status'] == 'inactive' ? 'selected' : '' ?>>inactive</option>
                    </select>
                    <label class="input-label">Status</label>
                </div>
            </div>
            <div class="action">
                <button type="submit" name="submit" class="action-button">Perbarui Member</button>
            </div>
        </form>
        <div class="card-info">
            <p>Pastikan data member sudah benar sebelum diperbarui</p>
        </div>
    </div>
</div>
</body>
</html>
