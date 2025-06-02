<?php 
include 'connection.php';

$id = $_GET['id'];
mysqli_query($conn, "delete from products where id=$id");

header("location:data-produk.php");
?>