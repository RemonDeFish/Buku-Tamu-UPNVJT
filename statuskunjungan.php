<?php
require_once 'config.php';

// JANGAN DIUBAH UBAH -Raymond 3-06-2026
$jumlah_data_per_halaman = 10;

$halaman_aktif = isset($_GET['page'])
    ? (int) $_GET['page']
    : 1;

if ($halaman_aktif < 1) {
    $halaman_aktif = 1;
}

$total_data_query = $conn->query("
    SELECT COUNT(*) AS total
    FROM kunjungan
");

$total_data = $total_data_query
    ->fetch_assoc()['total'];

$total_halaman = ceil(
    $total_data /
    $jumlah_data_per_halaman
);

if ($total_halaman < 1) {
    $total_halaman = 1;
}

if ($halaman_aktif > $total_halaman) {
    $halaman_aktif = $total_halaman;
}

$offset =
    ($halaman_aktif - 1)
    *
    $jumlah_data_per_halaman;

$data_kunjungan = [];

$stmt = $conn->prepare("
    SELECT *
    FROM kunjungan
    ORDER BY id DESC
    LIMIT ?
    OFFSET ?
");

$stmt->bind_param(
    "ii",
    $jumlah_data_per_halaman,
    $offset
);

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $data_kunjungan[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Kunjungan Tamu</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link class="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        montserrat: ['Montserrat', 'sans-serif'],
                        roboto: ['Roboto', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom scrollbar untuk tabel responsif */
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    </style>
</head>
<body class="bg-white min-h-screen w-full flex flex-col font-montserrat antialiased overflow-x-hidden relative">

    <div class="absolute top-0 left-0 w-full h-full z-0 overflow-hidden pointer-events-none">
        <img class="w-full h-full object-cover opacity-[0.12]" src="image/batik.png" alt="Batik Background" />
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/40 to-white"></div>
    </div>

    <nav class="relative z-30 w-full h-[72px] bg-white shadow-sm flex justify-center items-center px-4 md:px-8">
        <div class="w-full max-w-[1400px] flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <img class="w-[39px] h-[37px] object-contain" src="image/Logo.png" alt="Logo" />
                <span class="font-bold text-black text-2xl tracking-[0.10px]">SIPPK</span>
            </div>
            <div class="flex items-center gap-6 md:gap-11">
                <div class="hidden md:flex items-center gap-11 font-normal text-black text-base">
                    <a href="index.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Home</a>
                    <a href="statuskunjungan.php" class="text-[#6a5750] font-semibold border-b-4 rounded-sm border-[#6a5750] pb-1">Status Kunjungan</a>
                    <a href="contact.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Contact Us</a>
                </div>
                <a href="adminlogin.php" class="inline-block bg-[#6a5750] hover:bg-[#574741] text-white font-normal text-base px-5 py-2 rounded transition shadow-sm">
                    Masuk Admin
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-20 w-full max-w-[1240px] mx-auto px-4 md:px-8 pt-12 md:pt-20 flex-grow flex items-center justify-center">
        
        <div class="w-full rounded-[40px] p-[1.5px] bg-gradient-to-tl from-[#6a5750]/70 via-[#6a5750]/35 to-transparent shadow-[0_20px_50px_rgba(106,87,80,0.12)] mb-24">
            
            <div class="w-full bg-white rounded-[39px] p-6 md:p-12 relative">
                
                <div class="w-full flex flex-col items-center mb-10 relative px-2 md:px-6">
                    <div class="text-center pb-3">
                        <h2 class="text-3xl font-bold text-gray-800 tracking-wide px-6">Status Kunjungan</h2>
                    </div>
                    <div class="w-72 h-[3px] bg-[#6a5750] rounded-full"></div>
                    <div class="w-full h-[1px] bg-gray-200/90 z-0"></div>
                </div>

                <div class="w-full flex flex-col justify-between pt-2 relative">
                    
                    <div class="w-full overflow-x-auto custom-scroll">
                        <table class="w-full text-left border-collapse min-w-[900px] table-fixed">
                            <thead>
                                <tr class="border-b-2 border-gray-200/80">
                                    <th class="w-[8%] pb-5 text-[11px] font-bold text-gray-400 uppercase">ID</th>
                                    <th class="w-[20%] pb-5 text-[11px] font-bold text-gray-400 uppercase">Nama Pengunjung</th>
                                    <th class="w-[15%] pb-5 text-[11px] font-bold text-gray-400 uppercase">No. Telepon</th>
                                    <th class="w-[15%] pb-5 text-[11px] font-bold text-gray-400 uppercase">Instansi</th>
                                    <th class="w-[18%] pb-5 text-[11px] font-bold text-gray-400 uppercase">Keperluan</th>
                                    <th class="w-[14%] pb-5 text-[11px] font-bold text-gray-400 uppercase">Tujuan</th>
                                    <th class="w-[10%] pb-5 text-[11px] font-bold text-gray-400 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php if (count($data_kunjungan) > 0): ?>

                                    <?php foreach ($data_kunjungan as $row): ?>

                                        <tr>
                                            <td><?= htmlspecialchars($row['id']) ?></td>

                                            <td><?= htmlspecialchars($row['nama_pengunjung']) ?></td>

                                            <td><?= htmlspecialchars($row['no_telp']) ?></td>

                                            <td><?= htmlspecialchars($row['instansi']) ?></td>

                                            <td><?= htmlspecialchars($row['keperluan']) ?></td>

                                            <td><?= htmlspecialchars($row['tujuan']) ?></td>

                                            <td>
                                            <?php if ($row['status'] === 'Disetujui'): ?>
                                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                    Disetujui
                                                </span>
                                            <?php elseif ($row['status'] === 'Ditolak'): ?>
                                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                                    Ditolak
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                                    Menunggu
                                                </span>
                                            <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align:center;">
                                            Data tidak ditemukan
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2 mt-10 pt-5 border-t border-gray-200/80">
                        
                        <?php if($halaman_aktif > 1): ?>
                            <a href="?page=<?= $halaman_aktif - 1; ?>" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.75-7.5" /></svg>
                            </a>
                        <?php endif; ?>

                        <?php for($i = 1; $i <= $total_halaman; $i++): ?>
                            <?php if($i == $halaman_aktif): ?>
                                <a href="?page=<?= $i; ?>" class="w-7 h-7 text-xs font-bold bg-[#6a5750] text-white rounded-lg shadow-sm flex items-center justify-center">
                                    <?= $i; ?>
                                </a>
                            <?php else: ?>
                                <a href="?page=<?= $i; ?>" class="w-7 h-7 text-xs font-semibold text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-lg transition flex items-center justify-center">
                                    <?= $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if($halaman_aktif < $total_halaman): ?>
                            <a href="?page=<?= $halaman_aktif + 1; ?>" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                            </a>
                        <?php endif; ?>

                    </div>

                </div>

            </div>
        </div>
    </main>

    <footer class="relative w-full min-h-[140px] mt-auto z-10 flex flex-col justify-end overflow-hidden">
        <img class="absolute bottom-0 left-0 w-full h-[115%] object-cover z-0 pointer-events-none transform translate-y-6" src="image/vector-1.svg" alt="Footer Wave" />
        <div class="relative z-10 w-full max-w-[1200px] mx-auto px-6 md:px-8 pb-6 flex flex-col gap-4">
            <div class="flex gap-3 justify-center md:justify-start">
                <a href="https://x.com/BillGates" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/twitter-icon.svg" alt="Twitter" /></a>
                <a href="https://www.linkedin.com/in/williamhgates" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/linkedin-icon.svg" alt="Linkedin" /></a>
                <a href="https://www.instagram.com/thisisbillgates" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/instagram-icon.svg" alt="Instagram" /></a>
                <a href="https://www.youtube.com/billgates" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/youtube-icon.svg" alt="Youtube" /></a>
            </div>
            <div class="w-full h-[1px] bg-white opacity-20"></div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-xs font-normal text-white">
                <p class="opacity-90">© 2026 SIPPK. All rights reserved.</p>
                <div class="flex gap-6 md:gap-12">
                    <a href="https://www.whatsapp.com/legal/privacy-policy?lang=id" class="text-gray-200 hover:text-white transition">Privacy Policy</a>
                    <a href="https://www.whatsapp.com/legal/terms-of-service" class="text-white hover:underline transition">Terms & Conditions</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
