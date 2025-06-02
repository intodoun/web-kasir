<?php
session_start();
include 'connection.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['admin_id']) && !empty($_POST['admin_id'])) {
        $admin_id = intval($_POST['admin_id']);

        // Cegah menghapus admin yang sedang login
        if ($admin_id == $_SESSION['admin_id']) {
            echo "Error: Anda tidak bisa menghapus akun yang sedang login!";
            exit;
        }

        // Eksekusi query hapus
        $sql = "DELETE FROM admin WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);

        if ($stmt->execute()) {
            header("Location: profil.php");
            exit();
        } else {
            echo "Error: Gagal menghapus admin.";
        }

        $stmt->close();
    } else {
        echo "Error: ID admin tidak valid!";
    }
} else {
    echo "Akses ditolak!";
}
?>
