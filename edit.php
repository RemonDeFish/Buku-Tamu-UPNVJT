<?php
include 'config.php';

$aksi = $_GET['aksi'];

if ($aksi == 'tambah') {
    $nama = $_GET['nama'];
    $npm  = $_GET['npm'];
    $fakultas = $_GET['fakultas'];
    $prodi = $_GET['prodi'];
    
    mysqli_query($conn, "INSERT INTO mahasiswa (nama, npm, fakultas, prodi) VALUES ('$nama', '$npm', '$fakultas', '$prodi')");
} 

elseif ($aksi == 'edit') {
    $id   = $_GET['id'];
    $nama = $_GET['nama'];
    $npm  = $_GET['npm'];
    $fakultas = $_GET['fakultas'];
    $prodi = $_GET['prodi'];
    
    mysqli_query($conn, "UPDATE mahasiswa SET nama='$nama', npm='$npm', fakultas='$fakultas', prodi='$prodi' WHERE id=$id");
}

header("Location: index.php");
?>