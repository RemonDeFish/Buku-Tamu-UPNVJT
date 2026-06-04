<?php
// =========================================================================
// --- INBOX.PHP (LIMIT 10 PER PAGE, DINAMIS KATEGORI, & FORMAT WAKTU REAL) ---
// =========================================================================
session_start();

date_default_timezone_set('Asia/Jakarta');

require_once 'config.php';
// admin harus login baru bisa akses
if (
    !isset($_SESSION['admin_id']) ||
    !isset($_SESSION['otp_verified']) ||
    $_SESSION['otp_verified'] !== true
) {
    header("Location: adminlogin.php");
    exit();
}

$notifikasi = [];

$stmtNotif = $conn->prepare("
    SELECT
        nama_lengkap,
        subjek
    FROM inbox
    WHERE status = 'Belum Dibaca'
    ORDER BY created_at DESC
    LIMIT 5
");

$stmtNotif->execute();

$resultNotif = $stmtNotif->get_result();

while ($rowNotif = $resultNotif->fetch_assoc()) {

    $notifikasi[] = [
        'tipe' => 'pesan',
        'judul' => 'Pesan Baru',
        'deskripsi' =>
            $rowNotif['nama_lengkap'] .
            ' - ' .
            $rowNotif['subjek']
    ];
}

$jumlah_notif = count($notifikasi);

$database_pesan = [];

$query = $conn->query("
    SELECT
        id,
        nama_lengkap,
        subjek,
        created_at,
        status
    FROM inbox
    ORDER BY created_at DESC
");

while ($row = $query->fetch_assoc()) {

    $database_pesan[] = [
        'id'      => $row['id'],
        'nama'    => $row['nama_lengkap'],
        'subjek'  => $row['subjek'],
        'waktu'   => $row['created_at'],
        'starred' => false,
        'bin'     => false,
        'status'  => $row['status']
    ];
}

$jumlah_belum_dibaca = 0;

foreach ($database_pesan as $pesan) {
    if ($pesan['status'] === 'Belum Dibaca') {
        $jumlah_belum_dibaca++;
    }
}

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
$count_inbox = count($database_pesan);

foreach ($database_pesan as $p) {
    if (!$p['bin']) {
        $count_inbox++;
    }
}

// --- LOGIKA FILTER BERDASARKAN TAB ---
$pesan_terfilter = $database_pesan;

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
                    <a href="inbox.php" class="flex items-center gap-2 pb-2 text-[#6a5750] border-b-2 border-[#6a5750]">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="w-4 h-4">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        Inbox
                        <span class="text-[10px] font-normal text-gray-400">
                            <?= $count_inbox ?>
                        </span>
                        <?php if ($jumlah_belum_dibaca > 0): ?>
                            <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">
                                <?= $jumlah_belum_dibaca ?>
                            </span>
                        <?php endif; ?>
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
                                    <td class="w-[20%] py-1.5 text-xs font-bold text-gray-800 truncate px-2 search-target-nama">
                                        <?= htmlspecialchars($row['nama']) ?>
                                        <?php if ($row['status'] === 'Belum Dibaca'): ?>
                                            <span class="ml-2 px-2 py-0.5 text-[10px] bg-red-100 text-red-600 rounded">
                                                Baru
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="w-[60%] py-1.5 text-xs truncate px-2 search-target-subjek
                                        <?= $row['status'] === 'Belum Dibaca'
                                            ? 'font-bold text-black'
                                            : 'font-medium text-gray-600'
                                        ?>">
                                        <?= htmlspecialchars($row['subjek']) ?>
                                    </td>
                                    <td class="w-[10%] py-1.5 text-right pr-4 text-[11px] font-medium text-gray-400 font-roboto">
                                        <?= formatWaktuPesan($row['waktu']) ?>
                                    </td>

                                    <td class="w-[6%] py-1.5 text-center">
                                        <a
                                            href="detailinbox.php?id=<?= $row['id'] ?>"
                                            class="text-[#6a5750] hover:underline text-xs font-semibold">
                                            Detail
                                        </a>
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
                    <a href="?page=<?= $current_page - 1 ?>" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.75-7.5" /></svg>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="w-7 h-7 text-xs <?= $current_page === $i ? 'bg-[#6a5750] text-white font-bold shadow-sm' : 'text-gray-600 font-semibold border border-gray-200 hover:bg-gray-50' ?> rounded-lg flex items-center justify-center transition"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>" class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-gray-50 transition">
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