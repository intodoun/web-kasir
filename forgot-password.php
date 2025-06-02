<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Koneksi ke database
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "kasir";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send_reset'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $result = $conn->query("SELECT * FROM admin WHERE email = '$email'");

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(32));
            date_default_timezone_set('Asia/Jakarta');
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            $conn->query("UPDATE admin SET reset_token = '$token', token_expiry = '$expiry' WHERE email = '$email'");

            $reset_link = "http://localhost/web-kasir/forgot-password.php?token=$token";
            if (send_reset_email($email, $reset_link)) {
                echo "<script>alert('Link reset password telah dikirim ke email Anda.');</script>";
            } else {
                echo "<script>alert('Gagal mengirim email.');</script>";
            }
        } else {
            echo "<script>alert('Email tidak ditemukan!');</script>";
        }
    } elseif (isset($_POST['reset_password'])) {
        $token = $_POST['token'];
        $password = $_POST['password'];

        $result = $conn->query("SELECT * FROM admin WHERE reset_token = '$token' AND token_expiry >= NOW()");
        if ($result->num_rows > 0) {
            $conn->query("UPDATE admin SET password = '$password', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'");
            echo "<script>alert('Password berhasil direset!'); window.location.href = 'login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Token tidak valid atau kadaluarsa!');</script>";
        }
    }
}

// Fungsi kirim email
function send_reset_email($email, $reset_link) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'menyalasenterku@gmail.com';
        $mail->Password = 'prrj uypf hzdk prbg';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('menyalasenterku@gmail.com', 'Web Kasir');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password';
        $mail->Body = "Klik link berikut untuk mereset password Anda: <a href='$reset_link'>Click here</a>. Link berlaku selama 15 menit.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 360px;
            text-align: center;
            position: relative;
        }
        h2 {
            margin-bottom: 20px;
            color: #444;
            font-size: 22px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background: #f9f9f9;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #6c757d;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #5a6268;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .back-arrow {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 20px;
            color: #6c757d;
            text-decoration: none;
            transition: 0.3s;
        }
        .back-arrow:hover {
            color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="login.php" class="back-arrow">⬅</a> <!-- Tambahin ikon panah balik -->
    
    <?php if (!isset($_GET['token'])) { ?>
        <h2>Lupa Password</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Masukkan Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" name="send_reset">Kirim Link Reset</button>
        </form>
    <?php } else { ?>
        <h2>Reset Password</h2>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <div class="form-group">
                <label for="password">Masukkan Password Baru</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php } ?>
</div>

</body>
</html>
