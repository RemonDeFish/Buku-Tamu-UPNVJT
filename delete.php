<?php
include 'config.php';

$id = $_GET['id'];

$result = mysqli_query($conn, "DELETE FROM mahasiswa WHERE id='$id'");

if ($result) {
    header("Location: index.php");
} else {
    echo "Gagal menghapus data: " . mysqli_error($conn);
}
?>