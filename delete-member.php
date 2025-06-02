<?php 
include 'connection.php';

$id = $_GET['id'];

// Ambil status member dulu
$result = mysqli_query($conn, "SELECT status FROM member WHERE id=$id");
$data = mysqli_fetch_assoc($result);

if ($data && $data['status'] == 'inactive') {
    // Jika member inactive, boleh dihapus
    mysqli_query($conn, "DELETE FROM member WHERE id=$id");
    header("Location: member.php?message=deleted");
    exit;
} else {
    // Jika active atau tidak ditemukan, tampilkan alert
    echo "<script>
        alert('Member masih aktif dan tidak bisa dihapus!');
        window.location.href = 'member.php';
    </script>";
    exit;
}
?>
