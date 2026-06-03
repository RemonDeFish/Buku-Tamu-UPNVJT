<?php
// ==========================================
// --- DASHBOARD.PHP (HALAMAN UTAMA ADMIN) ---
// ==========================================
session_start();

date_default_timezone_set('Asia/Jakarta');

require_once 'config.php';

$hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
$bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

$tanggal_display = $hari[date('w')] . ", " . date('j') . " " . $bulan[date('n')] . " " . date('Y');

$stats = [
    'total' => 0,
    'pending' => 0,
    'today' => 0,
    'approved' => 0
];

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM kunjungan
");

$stats['total'] =
    $result
    ->fetch_assoc()['total'];

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM kunjungan
    WHERE status = 'menunggu'
");

$stats['pending'] =
    $result
    ->fetch_assoc()['total'];

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM kunjungan
    WHERE status = 'disetujui'
");

$stats['approved'] =
    $result
    ->fetch_assoc()['total'];

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM kunjungan
    WHERE status = 'ditolak'
");

$stats['rejected'] =
    $result
    ->fetch_assoc()['total'];

$result = $conn->query("
    SELECT COUNT(*) AS total
    FROM kunjungan
    WHERE status = 'selesai'
");

$stats['completed'] =
    $result
    ->fetch_assoc()['total'];

$today = date('Y-m-d');

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM kunjungan
    WHERE tanggal = ?
");

$stmt->bind_param(
    "s",
    $today
);

$stmt->execute();

$stats['today'] =
    $stmt
    ->get_result()
    ->fetch_assoc()['total'];

$kunjungan_mendatang = [];

$result = $conn->query("
    SELECT *
    FROM kunjungan
    ORDER BY tanggal DESC,
             waktu_mulai DESC
    LIMIT 6
");

while ($row = $result->fetch_assoc()) {

    $kunjungan_mendatang[] = [
        'id' => $row['id'],
        'nama' => $row['nama_pengunjung'],
        'keperluan' => $row['keperluan'],
        'tanggal' => date(
            'd M Y',
            strtotime($row['tanggal'])
        ),
        'waktu' =>
            $row['waktu_mulai']
            . ' - '
            . $row['waktu_selesai']
    ];
}

$notifikasi = [];

$result = $conn->query("
    SELECT *
    FROM kunjungan
    WHERE status = 'menunggu'
    ORDER BY id DESC
    LIMIT 10
");

while ($row = $result->fetch_assoc()) {

    $notifikasi[] = [
        'tipe' => 'kunjungan',
        'judul' => 'Kunjungan Baru Terdeteksi',
        'deskripsi' =>
            $row['nama_pengunjung']
            . ' mengajukan kunjungan.'
    ];
}

$jumlah_notif = count($notifikasi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Admin Dashboard</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
</head>
<body class="bg-[#f3f4f6] min-h-screen w-full flex flex-col font-montserrat antialiased overflow-x-hidden relative">

    <div class="absolute top-0 left-0 w-full h-full z-0 overflow-hidden pointer-events-none">
        <img class="w-full h-full object-cover opacity-[0.12]" src="image/batik.png" alt="Batik Background" />
    </div>

    <!-- HEADER NAVIGASI -->
    <nav class="relative z-30 w-full h-[72px] bg-white shadow-sm flex justify-center items-center px-4 md:px-8">
        <div class="w-full max-w-[1400px] flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <img class="w-[39px] h-[37px] object-contain" src="image/Logo.png" alt="Logo" />
                <span class="font-bold text-black text-2xl tracking-[0.10px]">SIPPK</span>
            </div>
            
            <!-- Tambahan menu Inbox di sini -->
            <div class="hidden md:flex items-center gap-11 font-normal text-black text-base mx-auto pl-12">
                <a href="dashboard.php" class="font-semibold text-[#6a5750] border-b-4 rounded-sm border-[#6a5750] pb-1">Dashboard</a>
                <a href="kelolakunjungan.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Kelola Kunjungan</a>
                <a href="inbox.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Inbox</a>
            </div>
            
            <div class="flex items-center gap-4">
                <button id="btn-sidebar-trigger" class="relative p-1.5 bg-gray-50 hover:bg-gray-100 rounded-full transition flex items-center justify-center">
                    <?php if ($jumlah_notif > 0): ?>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full text-[10px] font-bold text-white flex items-center justify-center ring-2 ring-white">
                            <?= $jumlah_notif ?>
                        </span>
                    <?php endif; ?>
                    <img src="image/lonceng.svg" alt="Notifikasi" class="w-5 h-5 object-contain" />
                </button>
                
                <a href="logout.php" class="bg-[#6a5750] hover:bg-[#574741] text-white text-xs font-semibold px-4 py-2 rounded transition shadow-sm">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="relative z-10 w-full h-[160px] md:h-[230px] overflow-hidden shadow-sm flex items-center justify-center text-center px-6">
        <img class="absolute inset-0 w-full h-full object-cover z-0" src="image/hero-dashboard.svg" alt="Background Hero" />
        <div class="relative z-10 max-w-5xl">
            <h1 class="font-bold text-white text-xl sm:text-2xl md:text-[32px] tracking-wide leading-tight drop-shadow-md font-montserrat">
                Sistem Informasi Pengunjung dan Pertemuan Campus<br class="hidden md:inline"/> (SIPPK)
            </h1>
        </div>
    </div>

    <main class="relative z-10 w-full max-w-[1200px] mx-auto px-6 md:px-8 pt-8 md:pt-10 pb-16 flex flex-col gap-8 justify-start">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard</h2>
            <p class="text-sm font-medium text-gray-600 mt-0.5">Welcome Back!</p>
        </div>

        <div class="flex flex-col gap-2">
            <h3 class="text-xs font-bold text-gray-700 tracking-wider uppercase font-roboto">Pengecekan Statistik</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[130px]">
                    <span class="text-xs font-semibold text-gray-400 font-roboto">Total Kunjungan</span>
                    <span class="text-4xl font-bold text-gray-900 leading-none"><?= $stats['total'] ?></span>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[130px]">
                    <span class="text-xs font-semibold text-gray-400 font-roboto">Menunggu Persetujuan</span>
                    <span class="text-4xl font-bold text-red-500 leading-none"><?= $stats['pending'] ?></span>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[130px]">
                    <span class="text-xs font-semibold text-gray-400 font-roboto">Kunjungan Hari Ini</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-bold text-green-600 leading-none"><?= $stats['today'] ?></span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[130px]">
                    <span class="text-xs font-semibold text-gray-400 font-roboto">Kunjungan Disetujui</span>
                    <span class="text-4xl font-bold text-gray-900 leading-none"><?= $stats['approved'] ?></span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <?php
            // --- LOGIKA DATA TREN WAKTU KUNJUNGAN ---
            $row_tren = [
                'blok_1' => 0,
                'blok_2' => 0,
                'blok_3' => 0,
                'blok_4' => 0,
                'blok_5' => 0
            ];

            $result = $conn->query("
                SELECT waktu_mulai
                FROM kunjungan
            ");

            while ($row = $result->fetch_assoc()) {

                $jam =
                    (int) substr(
                        $row['waktu_mulai'],
                        0,
                        2
                    );

                if ($jam >= 5 && $jam < 10) {

                    $row_tren['blok_1']++;

                } elseif ($jam >= 10 && $jam < 15) {

                    $row_tren['blok_2']++;

                } elseif ($jam >= 15 && $jam < 20) {

                    $row_tren['blok_3']++;

                } elseif ($jam >= 20 || $jam < 1) {

                    $row_tren['blok_4']++;

                } else {

                    $row_tren['blok_5']++;
                }
            }

            $max_value = max($row_tren['blok_1'], $row_tren['blok_2'], $row_tren['blok_3'], $row_tren['blok_4'], $row_tren['blok_5']);
            if ($max_value == 0) $max_value = 1; 

            $height_1 = ($row_tren['blok_1'] / $max_value) * 160;
            $height_2 = ($row_tren['blok_2'] / $max_value) * 160;
            $height_3 = ($row_tren['blok_3'] / $max_value) * 160;
            $height_4 = ($row_tren['blok_4'] / $max_value) * 160;
            $height_5 = ($row_tren['blok_5'] / $max_value) * 160;
            ?>

            <div class="lg:col-span-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h4 class="text-sm font-bold text-gray-800 mb-6 tracking-tight">Tren Waktu Kunjungan</h4>
                <div class="w-full flex-grow flex items-end gap-4 h-[180px] border-b border-gray-100 pb-2 relative">
                    
                    <div class="absolute inset-x-0 bottom-2 top-0 flex flex-col justify-between pointer-events-none text-[10px] font-medium text-gray-300 font-roboto">
                        <div class="border-b border-gray-50 w-full text-left pl-1"><?= $max_value ?></div>
                        <div class="border-b border-gray-50 w-full text-left pl-1"><?= round($max_value * 0.6) ?></div>
                        <div class="border-b border-gray-50 w-full text-left pl-1"><?= round($max_value * 0.3) ?></div>
                        <div class="w-full text-left pl-1 bottom-0">0</div>
                    </div>
                    
                    <div class="flex-grow flex items-end justify-around z-10 pl-8">
                        <div class="flex flex-col items-center w-8 group relative">
                            <span class="absolute -top-7 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition duration-200 pointer-events-none z-20 shadow-sm"><?= $row_tren['blok_1'] ?></span>
                            <div class="w-full bg-[#82a3a1] rounded-t-xl transition-all duration-300 group-hover:opacity-80 shadow-sm" style="height: <?= $height_1 ?>px;"></div>
                        </div>
                        
                        <div class="flex flex-col items-center w-8 group relative">
                            <span class="absolute -top-7 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition duration-200 pointer-events-none z-20 shadow-sm"><?= $row_tren['blok_2'] ?></span>
                            <div class="w-full bg-[#df8a6b] rounded-t-xl transition-all duration-300 group-hover:opacity-80 shadow-sm" style="height: <?= $height_2 ?>px;"></div>
                        </div>
                        
                        <div class="flex flex-col items-center w-8 group relative">
                            <span class="absolute -top-7 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition duration-200 pointer-events-none z-20 shadow-sm"><?= $row_tren['blok_3'] ?></span>
                            <div class="w-full bg-[#d5c3b9] rounded-t-xl transition-all duration-300 group-hover:opacity-80 shadow-sm" style="height: <?= $height_3 ?>px;"></div>
                        </div>
                        
                        <div class="flex flex-col items-center w-8 group relative">
                            <span class="absolute -top-7 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition duration-200 pointer-events-none z-20 shadow-sm"><?= $row_tren['blok_4'] ?></span>
                            <div class="w-full bg-[#463834] rounded-t-xl transition-all duration-300 group-hover:opacity-80 shadow-sm" style="height: <?= $height_4 ?>px;"></div>
                        </div>
                        
                        <div class="flex flex-col items-center w-8 group relative">
                            <span class="absolute -top-7 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition duration-200 pointer-events-none z-20 shadow-sm"><?= $row_tren['blok_5'] ?></span>
                            <div class="w-full bg-[#e3dedb] rounded-t-xl transition-all duration-300 group-hover:opacity-80 shadow-sm" style="height: <?= $height_5 ?>px;"></div>
                        </div>
                    </div>
                </div>
                
                <div class="w-full flex justify-between pl-10 pr-2 pt-2 text-[10px] font-medium text-gray-400 font-roboto">
                    <span class="w-14 text-center">05:00-10:00</span>
                    <span class="w-14 text-center">10:00-15:00</span>
                    <span class="w-14 text-center">15:00-20:00</span>
                    <span class="w-14 text-center">20:00-01:00</span>
                    <span class="w-14 text-center">01:00-05:00</span>
                </div>
            </div>

            <?php
            $data_keperluan = [];
            $result = $conn->query("
                SELECT
                    keperluan,
                    COUNT(*) AS total
                FROM kunjungan
                GROUP BY keperluan
                ORDER BY total DESC
            ");

            $total_semua = 0;
            $temp_data = [];

            while ($row = $result->fetch_assoc()) {
                $total_semua += $row['total'];
                $temp_data[] = $row;
            }
            $warna_chart = [
                '#463834',
                '#82a3a1',
                '#df8a6b',
                '#d5c3b9',
                '#7c6f68',
                '#bfa89e'
            ];
            $index = 0;
            foreach ($temp_data as $row) {
                $persen =
                    $total_semua > 0 ? round(($row['total'] / $total_semua) * 100,1): 0;
                $data_keperluan[] = [
                    'label' => $row['keperluan'],
                    'persen' => $persen,
                    'warna' =>
                        $warna_chart[
                            $index %
                            count($warna_chart)
                        ]
                ];
                $index++;
            }
            ?>
            <div class="lg:col-span-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <h4 class="text-sm font-bold text-gray-800 mb-4 tracking-tight">Tren Keperluan</h4>
                <div class="flex flex-col sm:flex-row items-center justify-around gap-6 h-full pb-2">
                    
                    <div class="relative w-36 h-36 flex items-center justify-center">
                        <canvas id="keperluanDonutChart"></canvas>
                    </div>

                    <div class="flex flex-col gap-3 text-xs font-semibold text-gray-700 font-roboto w-full sm:w-auto min-w-[220px]">
                        <?php foreach ($data_keperluan as $item): ?>
                        <div class="flex items-center justify-between gap-4 py-0.5">
                            <span class="flex items-center gap-2.5 font-medium text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: <?= $item['warna'] ?>;"></span> 
                                <?= $item['label'] ?>
                            </span>
                            <span class="text-gray-900 font-bold"><?= $item['persen'] ?>%</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <div class="w-full flex justify-between items-center">
                <h3 class="text-md font-bold rounded-sm border-[#444444] border-b-2 text-gray-800 tracking-tight">Kunjungan Mendatang</h3>
                <a href="kelolakunjungan.php" class="text-xs font-bold text-gray-900 hover:underline flex items-center gap-1.5 transition">
                    <span>View All</span>
                    <img src="image/arrow-right.svg" alt="Arrow Right" class="w-3 h-3 object-contain" />
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($kunjungan_mendatang as $item): ?>
                    <div class="bg-white p-0 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between min-h-[240px] overflow-hidden group hover:border-gray-200 transition">
                        <div class="p-5 pt-6 pb-4">
                            <h4 class="font-bold text-gray-900 text-sm tracking-wide"><?= $item['nama'] ?></h4>
                            <span class="block text-[11px] font-semibold text-gray-400 font-roboto mt-4 uppercase tracking-wider">Keperluan</span>
                            <span class="block text-xs font-medium text-gray-600 mt-1"><?= $item['keperluan'] ?></span>
                        </div>
                        <div>
                            <div class="px-5 pb-5">
                                <div class="flex flex-row gap-16 items-start">
                                    <div>
                                        <span class="block text-[10px] font-bold text-gray-400 font-roboto uppercase tracking-wider">Tanggal</span>
                                        <span class="block text-[11px] font-medium text-gray-600 mt-1 whitespace-nowrap"><?= $item['tanggal'] ?></span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-gray-400 font-roboto uppercase tracking-wider">Waktu</span>
                                        <span class="block text-[11px] font-medium text-gray-600 mt-1 whitespace-nowrap"><?= $item['waktu'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 mx-5"></div>
                            <div class="px-5 py-3.5 text-left">
                                <a href="detailkunjungan.php?id=<?= $item['id'] ?>" class="text-[12px] font-bold text-[#6a5750] hover:underline transition-all">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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
    
    <div id="sidebar-notifikasi" class="hidden absolute top-[72px] right-0 w-[360px] sm:w-[390px] max-h-[calc(100vh-72px)] overflow-y-auto bg-white z-20 border-l border-gray-200/80 p-6 flex flex-col transition-all duration-200">
        
        <div class="flex items-center justify-between gap-5 py-4 mb-4">
            <div>
                <span class="text-sm font-medium text-gray-600 block">Hello,</span>
                <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight">Admin!</h3>
                <p class="text-[11px] text-gray-400 font-medium mt-1.5">Cek reminder yang tersedia di sini.</p>
            </div>
            
            <div class="w-18 h-18 rounded-full overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm">
                <img src="image/profil.svg" alt="Admin Profile" class="w-full h-full object-cover" />
            </div>
        </div>

        <hr class="border-[#6a5750] opacity-40 mb-6" />

        <h4 class="text-sm font-bold text-gray-900 mb-4 tracking-tight">Pengingat</h4>

        <div class="flex flex-col gap-4">
            <?php foreach($notifikasi as $notif): ?>
                <div class="flex items-center gap-4 p-2 rounded-xl hover:bg-gray-50 transition text-left">

                    <?php if($notif['tipe'] === 'kunjungan'): ?>
                        <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                            <img src="image/tanda-seru.svg" alt="Alert" class="w-4 h-4 object-contain" />
                        </div>
                    <?php else: ?>
                        <div class="w-9 h-9 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <img src="image/pesan.svg" alt="Message" class="w-4 h-4 object-contain" />
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-col min-w-0 font-roboto">
                        <span class="text-xs font-bold text-gray-800 truncate"><?= $notif['judul'] ?></span>
                        <span class="text-[11px] text-gray-400 font-medium mt-0.5 truncate"><?= $notif['deskripsi'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const triggerBtn = document.getElementById('btn-sidebar-trigger');
        const sidebar = document.getElementById('sidebar-notifikasi');

        function toggleSidebar(e) {
            e.stopPropagation();
            sidebar.classList.toggle('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('hidden');
        }

        triggerBtn.addEventListener('click', toggleSidebar);

        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !triggerBtn.contains(e.target)) {
                closeSidebar();
            }
        });

        const ctx = document.getElementById('keperluanDonutChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php foreach($data_keperluan as $item) echo '"' . $item['label'] . '",'; ?>
                ],
                datasets: [{
                    data: [
                        <?php foreach($data_keperluan as $item) echo $item['persen'] . ','; ?>
                    ],
                    backgroundColor: [
                        <?php foreach($data_keperluan as $item) echo '"' . $item['warna'] . '",'; ?>
                    ],
                    borderWidth: 4,               
                    borderColor: '#ffffff',       
                    borderRadius: 6,              
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false 
                    },
                    tooltip: {
                        padding: 6,                 
                        bodySpacing: 3,             
                        titleFont: {
                            size: 10,               
                            family: 'Montserrat'
                        },
                        bodyFont: {
                            size: 8,               
                            family: 'Roboto'
                        },
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                },
                cutout: '68%' 
            }
        });
    </script>
</body>
</html>