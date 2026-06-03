<?php
// =========================================================================
// --- INBOX.PHP (LIMIT 10 PER PAGE, DINAMIS KATEGORI, & FORMAT WAKTU REAL) ---
// =========================================================================
session_start();

date_default_timezone_set('Asia/Jakarta');

// --- DATA NOTIFIKASI SIDEBAR ---
$notifikasi = [
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Mas Amba mendaftarkan kunjungan.'],
    ['tipe' => 'pesan', 'judul' => 'Pesan Baru', 'deskripsi' => 'Keluhan sistem tiket dari Hilmi Fahrenheit.'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Amanda Putri mengajukan kunjungan Dinas.'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Christine Michelle mengirimkan bukti dokume..'],
    ['tipe' => 'pesan', 'judul' => 'Pesan Baru', 'deskripsi' => 'Tanya Jadwal - Tono Siregar (Universitas Airlan..'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'Randy AK-47 mengajukan Janji Temu Rektorat.'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Remon Chin mendaftarkan kunjungan Humas.']
];
$jumlah_notif = count($notifikasi);

// --- TAB HANDLING ---
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'inbox';

// --- DATA DUMMY INBOX (MENGGUNAKAN FORMAT DATETIME DATABASE) ---
$database_pesan = [
    ['id' => 1, 'nama' => 'Jullu Jalal', 'subjek' => 'Our Bachelor of Commerce program is ACBSP-accredited.', 'waktu' => '2026-06-03 08:38:00', 'starred' => false, 'bin' => false],
    ['id' => 2, 'nama' => 'Minerva Barnett', 'subjek' => 'Get Best Advertiser In Your Side Pocket', 'waktu' => '2026-06-03 08:13:00', 'starred' => false, 'bin' => false],
    ['id' => 3, 'nama' => 'Peter Lewis', 'subjek' => 'Vacation Home Rental Success', 'waktu' => '2026-06-02 19:52:00', 'starred' => false, 'bin' => false],
    ['id' => 4, 'nama' => 'Anthony Briggs', 'subjek' => 'Free Classifieds Using Them To Promote Your Stuff Online', 'waktu' => '2026-06-02 11:20:00', 'starred' => true, 'bin' => false],
    ['id' => 5, 'nama' => 'Clifford Morgan', 'subjek' => 'Enhance Your Brand Potential With Giant Advertising Blimps', 'waktu' => '2026-06-01 16:13:00', 'starred' => false, 'bin' => false],
    ['id' => 6, 'nama' => 'Cecilia Webster', 'subjek' => 'Always Look On The Bright Side Of Life', 'waktu' => '2026-05-30 15:52:00', 'starred' => false, 'bin' => false],
    ['id' => 7, 'nama' => 'Harvey Manning', 'subjek' => 'Curling Irons Are As Individual As The Women Who Use Them', 'waktu' => '2026-05-29 14:30:00', 'starred' => true, 'bin' => false],
    ['id' => 8, 'nama' => 'Willie Blake', 'subjek' => 'Our Bachelor of Commerce program is ACBSP-accredited.', 'waktu' => '2026-05-28 08:38:00', 'starred' => false, 'bin' => false],
    ['id' => 9, 'nama' => 'Minerva Barnett', 'subjek' => 'Get Best Advertiser In Your Side Pocket', 'waktu' => '2026-05-25 08:13:00', 'starred' => false, 'bin' => false],
    ['id' => 10, 'nama' => 'Fanny Weaver', 'subjek' => 'Free Classifieds Using Them To Promote Your Stuff Online', 'waktu' => '2026-05-24 19:52:00', 'starred' => true, 'bin' => false],
    ['id' => 11, 'nama' => 'Olga Hogan', 'subjek' => 'Enhance Your Brand Potential With Giant Advertising Blimps', 'waktu' => '2026-05-22 16:13:00', 'starred' => false, 'bin' => false],
    ['id' => 12, 'nama' => 'Lora Houston', 'subjek' => 'Vacation Home Rental Success', 'waktu' => '2026-05-20 19:52:00', 'starred' => false, 'bin' => false],
];

// --- FUNGSI FORMAT WAKTU AGAR SEPERTI GMAIL ---
function formatWaktuPesan($datetime_str) {
    $timestamp = strtotime($datetime_str);
    $tanggal_pesan = date('Y-m-d', $timestamp);
    $hari_ini = date('Y-m-d');
    $kemarin = date('Y-m-d', strtotime('-1 day'));

    if ($tanggal_pesan === $hari_ini) {
        return date('H:i', $timestamp); // Contoh: 08:38
    } elseif ($tanggal_pesan === $kemarin) {
        return 'Kemarin';
    } else {
        // Menggunakan nama bulan singkat bahasa Indonesia
        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'
        ];
        $tgl = date('j', $timestamp);
        $bln = $bulan[(int)date('n', $timestamp)];
        return $tgl . ' ' . $bln; // Contoh: 28 Mei
    }
}

// --- HITUNG JUMLAH TOTAL REAL PER KATEGORI DARI ARRAY DUMMY ---
$count_inbox = 0;
$count_starred = 0;
$count_bin = 0;

foreach ($database_pesan as $p) {
    if (!$p['bin']) $count_inbox++;
    if ($p['starred'] && !$p['bin']) $count_starred++;
    if ($p['bin']) $count_bin++;
}

// --- LOGIKA FILTER BERDASARKAN TAB ---
$pesan_terfilter = [];
foreach ($database_pesan as $p) {
    if ($tab === 'starred' && $p['starred'] && !$p['bin']) {
        $pesan_terfilter[] = $p;
    } elseif ($tab === 'bin' && $p['bin']) {
        $pesan_terfilter[] = $p;
    } elseif ($tab === 'inbox' && !$p['bin']) {
        $pesan_terfilter[] = $p;
    }
}

// --- LOGIKA PAGINATION REAL (DI-SET 10 DATA PER HALAMAN) ---
$limit = 10; 
$total_data = count($pesan_terfilter);
$total_pages = ceil($total_data / $limit);
if ($total_pages < 1) $total_pages = 1;

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
if ($current_page > $total_pages) $current_page = $total_pages;

$offset = ($current_page - 1) * $limit;

$pesan_halaman_ini = array_slice($pesan_terfilter, $offset, $limit);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Inbox</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: '#6a5750', brandHover: '#574741' },
                    fontFamily: { montserrat: ['Montserrat', 'sans-serif'], roboto: ['Roboto', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    </style>
</head>
<body class="bg-[#f3f4f6] min-h-screen w-full flex flex-col font-montserrat antialiased overflow-x-hidden relative">

    <div class="absolute top-0 left-0 w-full h-full z-0 overflow-hidden pointer-events-none">
        <img class="w-full h-full object-cover opacity-[0.12]" src="image/batik.png" alt="Batik Background" />
    </div>

    <nav class="relative z-30 w-full h-[72px] bg-white shadow-sm flex justify-center items-center px-4 md:px-8 border-b border-gray-100">
        <div class="w-full max-w-[1400px] flex justify-between items-center">
            
            <div class="flex items-center gap-3.5 ">
                <img class="w-[39px] h-[37px] object-contain" src="image/Logo.png" alt="Logo" />
                <span class="font-bold text-black text-2xl tracking-[0.10px] select-none">SIPPK</span>
            </div>
            
            <div class="hidden md:flex items-center gap-10 font-normal text-black text-base ml-auto pr-24">
                <a href="dashboard.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm pb-1 transition">Dashboard</a>
                <a href="kelolakunjungan.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm pb-1 transition">Kelola Kunjungan</a>
                <a href="inbox.php" class="font-semibold text-[#6a5750] border-b-4 rounded-sm border-[#6a5750] pb-1">Inbox</a>
            </div>
            
            <div class="flex items-center gap-4 ">
                <div class="relative w-40 sm:w-52 flex items-center h-9">
                    <span class="absolute left-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" /></svg>
                    </span>
                    <input type="text" id="globalTableSearch" placeholder="Search keyword..." class="w-full h-full pl-9 pr-3 bg-[#f4f4f5] border-none rounded-full text-xs font-medium text-gray-700 outline-none focus:bg-gray-200/60 transition" />
                </div>

                <button id="btn-sidebar-trigger" class="relative p-1.5 bg-gray-50 hover:bg-gray-100 rounded-full transition flex items-center justify-center text-gray-700 focus:outline-none shrink-0">
                    <?php if ($jumlah_notif > 0): ?>
                        <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full text-[10px] font-bold text-white flex items-center justify-center ring-2 ring-white">
                            <?= $jumlah_notif ?>
                        </span>
                    <?php endif; ?>
                    <img src="image/lonceng.svg" alt="Notifikasi" class="w-5 h-5 object-contain" />
                </button>
                
                <a href="logout.php" class="bg-[#6a5750] hover:bg-[#574741] text-white text-xs font-semibold px-4 py-2 rounded transition shadow-sm block box-border shrink-0">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-10 w-full max-w-[1400px] mx-auto px-6 md:px-8 pt-8 md:pt-10 pb-24 flex flex-col gap-6 justify-start flex-grow">
        
        <div>
            <div class="text-xs font-medium text-gray-400 mb-1 font-roboto">Dashboard / Inbox</div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Inbox</h2>
        </div>

        <div class="w-full bg-white rounded-2xl p-6 shadow-sm border border-gray-100 overflow-hidden">
            
            <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                <div class="flex gap-8 text-xs font-semibold text-gray-400">
                    <a href="?tab=inbox" class="flex items-center gap-2 pb-2 transition <?= $tab === 'inbox' ? 'text-[#6a5750] border-b-2 border-[#6a5750]' : 'hover:text-gray-600' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                        Inbox <span class="text-[10px] font-normal text-gray-400"><?= $count_inbox ?></span>
                    </a>
                    <a href="?tab=starred" class="flex items-center gap-2 pb-2 transition <?= $tab === 'starred' ? 'text-[#6a5750] border-b-2 border-[#6a5750]' : 'hover:text-gray-600' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.173-.439.81-.439.98 0l2.115 5.433 5.764.536c.473.044.66.626.308.955l-4.385 4.1 1.218 5.719c.101.474-.414.848-.829.596l-4.997-2.678-4.997 2.678c-.415.252-.93-.122-.83-.596l1.218-5.719-4.385-4.1c-.352-.329-.165-.911.308-.955l5.764-.536 2.114-5.433Z" /></svg>
                        Starred <span class="text-[10px] font-normal text-gray-400"><?= $count_starred ?></span>
                    </a>
                    <a href="?tab=bin" class="flex items-center gap-2 pb-2 transition <?= $tab === 'bin' ? 'text-[#6a5750] border-b-2 border-[#6a5750]' : 'hover:text-gray-600' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        Bin <span class="text-[10px] font-normal text-gray-400"><?= $count_bin ?></span>
                    </a>
                </div>
                
                <button class="p-1 text-gray-400 hover:text-red-500 transition focus:outline-none flex items-center justify-center">
                    <img src="image/trash-icon.svg" class="w-4 h-4 opacity-75 hover:opacity-100 transition-opacity" alt="Delete Selected" />
                </button>
            </div>

            <div class="w-full overflow-x-auto custom-scroll">
                <table class="w-full border-collapse min-w-[800px] table-fixed" id="mainInboxTable">
                    <tbody class="divide-y divide-gray-100" id="tableBodyData">
                        <?php if (empty($pesan_halaman_ini)): ?>
                            <tr id="noDataRow">
                                <td class="py-10 text-center text-xs font-medium text-gray-400">Data tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pesan_halaman_ini as $row): ?>
                                <tr class="hover:bg-gray-50/50 transition table-row-item cursor-pointer" data-id="<?= $row['id'] ?>">
                                    <td class="w-[4%] py-1.5 pl-2 text-center">
                                        <input type="checkbox" class="w-4 h-4 accent-black rounded border-gray-300 focus:ring-0 checkbox-item" />
                                    </td>
                                    <td class="w-[4%] py-1.5 text-center">
                                        <button class="focus:outline-none flex items-center justify-center w-full btn-star-toggle">
                                            <?php if ($row['starred']): ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500 star-icon" data-status="filled"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>
                                            <?php else: ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-300 hover:text-gray-400 star-icon" data-status="empty"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.173-.439.81-.439.98 0l2.115 5.433 5.764.536c.473.044.66.626.308.955l-4.385 4.1 1.218 5.719c.101.474-.414.848-.829.596l-4.997-2.678-4.997 2.678c-.415.252-.93-.122-.83-.596l1.218-5.719-4.385-4.1c-.352-.329-.165-.911.308-.955l5.764-.536 2.114-5.433Z" /></svg>
                                            <?php endif; ?>
                                        </button>
                                    </td>
                                    <td class="w-[20%] py-1.5 text-xs font-bold text-gray-800 truncate px-2 search-target-nama">
                                        <?= htmlspecialchars($row['nama']) ?>
                                    </td>
                                    <td class="w-[60%] py-1.5 text-xs font-semibold text-gray-800 truncate px-2 search-target-subjek">
                                        <?= htmlspecialchars($row['subjek']) ?>
                                    </td>
                                    <td class="w-[12%] py-1.5 text-right pr-4 text-[11px] font-medium text-gray-400 font-roboto" title="<?= htmlspecialchars($row['waktu']) ?>">
                                        <?= formatWaktuPesan($row['waktu']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr id="noDataRow" class="hidden">
                                <td colspan="5" class="py-10 text-center text-xs font-medium text-gray-400">Data tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="paginationContainer" class="w-full flex justify-center items-center gap-2 mt-10 pt-5 border-t border-gray-200/80">
                <?php if ($current_page > 1): ?>
                    <a href="?tab=<?= $tab ?>&page=<?= $current_page - 1 ?>" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.75-7.5" /></svg>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?tab=<?= $tab ?>&page=<?= $i ?>" class="w-7 h-7 text-xs <?= $current_page === $i ? 'bg-[#6a5750] text-white font-bold shadow-sm' : 'text-gray-600 font-semibold border border-gray-200 hover:bg-gray-50' ?> rounded-lg flex items-center justify-center transition"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?tab=<?= $tab ?>&page=<?= $current_page + 1 ?>" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </a>
                <?php endif; ?>
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
    
    <div id="sidebar-notifikasi" class="hidden absolute top-[72px] right-0 w-[360px] sm:w-[390px] max-h-[calc(100vh-72px)] overflow-y-auto bg-white z-50 border-l border-gray-200/80 p-6 flex flex-col transition-all duration-200">
        <div class="flex items-center justify-between gap-5 py-4 mb-4">
            <div>
                <span class="text-sm font-medium text-gray-600 block">Hello,</span>
                <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight leading-tight">Admin!</h3>
                <p class="text-[11px] text-gray-400 font-medium mt-1.5">Cek reminder yang tersedia di sini.</p>
            </div>
            <div class="w-16 h-18 rounded-full overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm">
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
                    <?php immortality: ?>
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

        triggerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !triggerBtn.contains(e.target)) {
                sidebar.classList.add('hidden');
            }
        });

        const globalSearchInput = document.getElementById('globalTableSearch');
        const tableRows = document.querySelectorAll('.table-row-item');
        const noDataRow = document.getElementById('noDataRow');
        const paginationContainer = document.getElementById('paginationContainer');

        globalSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let matchedCount = 0;

            tableRows.forEach(row => {
                const namaText = row.querySelector('.search-target-nama').textContent.toLowerCase();
                const subjekText = row.querySelector('.search-target-subjek').textContent.toLowerCase();

                if (namaText.includes(query) || subjekText.includes(query)) {
                    row.style.display = '';
                    matchedCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (query !== '') {
                paginationContainer.classList.add('hidden');
                if (matchedCount === 0) {
                    if(noDataRow) noDataRow.classList.remove('hidden');
                } else {
                    if(noDataRow) noDataRow.classList.add('hidden');
                }
            } else {
                paginationContainer.classList.remove('hidden');
                if(noDataRow) noDataRow.classList.add('hidden');
            }
        });

        const starButtons = document.querySelectorAll('.btn-star-toggle');
        starButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const starSvg = this.querySelector('.star-icon');
                const isFilled = starSvg.getAttribute('data-status') === 'filled';

                if (isFilled) {
                    this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-300 hover:text-gray-400 star-icon" data-status="empty"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.173-.439.81-.439.98 0l2.115 5.433 5.764.536c.473.044.66.626.308.955l-4.385 4.1 1.218 5.719c.101.474-.414.848-.829.596l-4.997-2.678-4.997 2.678c-.415.252-.93-.122-.83-.596l1.218-5.719-4.385-4.1c-.352-.329-.165-.911.308-.955l5.764-.536 2.114-5.433Z" /></svg>`;
                } else {
                    this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-yellow-500 star-icon" data-status="filled"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" /></svg>`;
                }
            });
        });

        const checkboxes = document.querySelectorAll('.checkbox-item');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const parentRow = this.closest('.table-row-item');
                if (this.checked) {
                    parentRow.classList.add('bg-blue-50/70');
                } else {
                    parentRow.classList.remove('bg-blue-50/70');
                }
            });
            cb.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        tableRows.forEach(row => {
            row.addEventListener('click', function() {
                const messageId = this.getAttribute('data-id');
                if(messageId) {
                    window.location.href = `detailinbox.php?id=${messageId}`;
                }
            });
        });
    </script>
</body>
</html>