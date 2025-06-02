<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Gunakan SMTP Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'menyalasenterku@gmail.com'; // Ganti dengan email kamu
    $mail->Password = 'prrj uypf hzdk prbg'; // Ganti dengan App Password 16 karakter dari Google
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Pengirim & Penerima
    $mail->setFrom('menyalasenterku@gmail.com', 'Web Kasir');
    $mail->addAddress('amelizahra108@gmail.com'); // Ganti dengan email tujuan

    // Konten Email
    $mail->Subject = 'Test Email PHPMailer';
    $mail->Body = 'Halo! Ini adalah email uji coba dari PHPMailer.';

    // Kirim Email
    $mail->send();
    echo "✅ Email berhasil dikirim!";
} catch (Exception $e) {
    echo "❌ Gagal kirim email. Error: {$mail->ErrorInfo}";
}
?>
