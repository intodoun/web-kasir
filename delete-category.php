<?php 
include 'connection.php';

$id = intval($_GET['id']); // pastikan aman

// Cek apakah kategori masih dipakai di tabel produk
$query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM products WHERE fid_category = $id");
$data = mysqli_fetch_assoc($query);

if ($data['count'] > 0) {
    // Ada produk yang menggunakan kategori ini, tidak bisa hapus
    echo "<script>
        alert('Kategori tidak bisa dihapus karena masih ada produk yang menggunakan kategori ini.');
        window.location.href = 'index.php';
    </script>";
    exit();
}

// Kalau tidak ada produk, hapus kategori
mysqli_query($conn, "DELETE FROM category WHERE id = $id");

header("Location: index.php");
exit();
?>
