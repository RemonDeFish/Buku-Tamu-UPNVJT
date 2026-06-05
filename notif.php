<?php

if (!isset($conn)) {
    require_once 'config.php';
}

$notifikasi = [];

/*
|--------------------------------------------------------------------------
| KUNJUNGAN MENUNGGU
|--------------------------------------------------------------------------
*/

$qKunjungan = $conn->query("
    SELECT
        id,
        nama_pengunjung,
        keperluan
    FROM kunjungan
    WHERE status = 'menunggu'
    ORDER BY id DESC
    LIMIT 5
");

while ($row = $qKunjungan->fetch_assoc()) {

    $notifikasi[] = [
        'tipe' => 'kunjungan',
        'judul' => 'Kunjungan Baru',
        'deskripsi' =>
            $row['nama_pengunjung'] .
            ' mengajukan ' .
            $row['keperluan'],
        'url' =>
            'detailkunjungan.php?id=' .
            $row['id']
    ];
}

/*
|--------------------------------------------------------------------------
| PESAN BELUM DIBACA
|--------------------------------------------------------------------------
*/

$qInbox = $conn->query("
    SELECT
        id,
        nama_lengkap,
        subjek
    FROM inbox
    WHERE status = 'Belum Dibaca'
    ORDER BY id DESC
    LIMIT 5
");

while ($row = $qInbox->fetch_assoc()) {

    $notifikasi[] = [
        'tipe' => 'pesan',
        'judul' => 'Pesan Baru',
        'deskripsi' =>
            $row['nama_lengkap'] .
            ' - ' .
            $row['subjek'],
        'url' =>
            'detailinbox.php?id=' .
            $row['id']
    ];
}

/*
|--------------------------------------------------------------------------
| JUMLAH BADGE
|--------------------------------------------------------------------------
*/

$jumlah_notif = 0;

$q1 = $conn->query("
    SELECT COUNT(*) AS total
    FROM kunjungan
    WHERE status = 'menunggu'
");

$jumlah_notif +=
    (int)$q1->fetch_assoc()['total'];

$q2 = $conn->query("
    SELECT COUNT(*) AS total
    FROM inbox
    WHERE status = 'Belum Dibaca'
");

$jumlah_notif +=
    (int)$q2->fetch_assoc()['total']/2;