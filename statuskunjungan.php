<?php
require_once 'config.php';

$data_kunjungan = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama']);
    $no_telp = trim($_POST['no_telp']);

    $stmt = $conn->prepare("
        SELECT *
        FROM kunjungan
        WHERE nama_pengunjung = ?
        AND no_telp = ?
        ORDER BY created_at DESC
    ");

    $stmt->bind_param(
        "ss",
        $nama,
        $no_telp
    );

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data_kunjungan[] = $row;
    }
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
                    <a href="tamulogin.php" class="text-[#6a5750] font-semibold border-b-4 rounded-sm border-[#6a5750] pb-1">Status Kunjungan</a>
                    <a href="contact.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Contact Us?</a>
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
                                    <th class="w-[20%] pb-5 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Nomor Antrian</th>
                                    <th class="w-[25%] pb-5 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Informasi Keperluan</th>
                                    <th class="w-[15%] pb-5 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-roboto text-left">Status</th>
                                    <th class="w-[25%] pb-5 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Tanggal dan Waktu Pendaftaran</th>
                                    <th class="w-[15%] pb-5 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-roboto text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/70">
                                <?php foreach ($dummy_kunjungan as $row): 
                                    $status = strtolower($row['status']);
                                    if ($status == 'in progress') {
                                        $badge_style = 'bg-purple-50 text-purple-500';
                                    } elseif ($status == 'complete') {
                                        $badge_style = 'bg-green-50 text-green-500';
                                    } elseif ($status == 'pending') {
                                        $badge_style = 'bg-blue-50 text-blue-500';
                                    } elseif ($status == 'canceled') {
                                        $badge_style = 'bg-amber-50 text-amber-500';
                                    } else {
                                        $badge_style = 'bg-gray-100 text-gray-500';
                                    }
                                ?>
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="py-6 text-xs font-bold text-gray-700 tracking-tight font-roboto break-words pr-2"><?= $row['nomor_antrian']; ?></td>
                                        <td class="py-6 text-xs font-semibold text-gray-600 break-words pr-4"><?= $row['keperluan']; ?></td>
                                        <td class="py-6 text-left">
                                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold <?= $badge_style; ?>">
                                                <?= $row['status']; ?>
                                            </span>
                                        </td>
                                        <td class="py-6 text-[11px] font-medium text-gray-500 font-roboto break-words pr-4"><?= $row['tanggal_waktu']; ?></td>
                                        <td class="py-6 text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="tiket.php?id=<?= $row['id']; ?>&from=history" class="inline-block text-[11px] font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200/90 px-2.5 py-1 rounded-full transition shadow-sm">
                                                    View Tiket
                                                </a>
                                                
                                                <?php if($status != 'complete' && $status != 'canceled' && $status != 'rejected'): ?>
                                                    <a href="proses_batal.php?id=<?= $row['id']; ?>" 
                                                       onclick="return confirm('Apakah Anda yakin ingin membatalkan kunjungan dengan nomor antrian <?= $row['nomor_antrian']; ?>?');" 
                                                       class="text-[11px] font-bold text-red-500 hover:text-red-700 px-1 py-1 transition">
                                                        Batalkan
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-[11px] font-medium text-gray-300 px-1 py-1 cursor-not-allowed select-none">
                                                        -
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
                <a href="#" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/twitter-icon.svg" alt="Twitter" /></a>
                <a href="#" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/linkedin-icon.svg" alt="Linkedin" /></a>
                <a href="#" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/instagram-icon.svg" alt="Instagram" /></a>
                <a href="#" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/youtube-icon.svg" alt="Youtube" /></a>
            </div>
            <div class="w-full h-[1px] bg-white opacity-20"></div>
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-xs font-normal text-white">
                <p class="opacity-90">© 2026 SIPPK. All rights reserved.</p>
                <div class="flex gap-6 md:gap-12">
                    <a href="#" class="text-gray-200 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-white hover:underline transition">Terms & Conditions</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>