<?php
// =========================================================================
// --- KELOLAKUNJUNGAN.PHP (KODE UTUH FIX LAYOUT NAVBAR & FILTER RAPAT) ---
// =========================================================================
session_start();

// JANGAN DIUBAH UBAH -Raymond 3-06-2026
date_default_timezone_set('Asia/Jakarta');

require_once 'config.php';

// --- DATA NOTIFIKASI SIDEBAR ---
$notifikasi = [
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Mas Amba mendaftarkan kunjungan.'],
    ['tipe' => 'pesan', 'judul' => 'Pesan Baru', 'deskripsi' => 'Keluhan sistem tiket dari Hilmi Fahrenheit.'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Amanda Putri mengajukan kunjungan Dinas.'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Christine Michelle mengirimkan bukti dokume..'],
    ['tipe' => 'pesan', 'judul' => 'Pesan Baru', 'deskripsi' => 'Tanya Jadwal - Tono Siregar (Universitas Airlan..'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Randy AK-47 mengajukan Janji Temu Rektorat.'],
    ['tipe' => 'kunjungan', 'judul' => 'Kunjungan Baru Terdeteksi', 'deskripsi' => 'Remon Chin mendaftarkan kunjungan Humas.']
];
$sqlCount = "
    SELECT COUNT(*) AS total
    FROM kunjungan
";

$resultCount = $conn->query($sqlCount);

$total_data =
    $resultCount
    ->fetch_assoc()['total'];

// --- LOGIKA FILTER & PAGINATION TABEL ---
$jumlah_data_per_halaman = 9;
$halaman_aktif = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
if ($halaman_aktif < 1) { $halaman_aktif = 1; }

$jumlah_notif = count($notifikasi);
$filter_id_tamu   = isset($_GET['filter_id'])         ? trim($_GET['filter_id'])         : '';
$filter_tanggal   = isset($_GET['filter_tanggal'])   ? trim($_GET['filter_tanggal'])   : '';
$filter_keperluan = isset($_GET['filter_keperluan']) ? trim($_GET['filter_keperluan']) : '';

$where = [];
$params = [];
$types = "";

if ($filter_keperluan !== '') {

    $where[] = "keperluan = ?";
    $params[] = $filter_keperluan;
    $types .= "s";
}

if ($filter_tanggal !== '') {

    $where[] = "tanggal = ?";
    $params[] = $filter_tanggal;
    $types .= "s";
}
if ($filter_id_tamu !== '') {

    $where[] = "id = ?";
    $params[] = $filter_id_tamu;
    $types .= "i";
}
$sqlWhere = "";

if (!empty($where)) {

    $sqlWhere =
        " WHERE " .
        implode(" AND ", $where);
}
$sqlCount =
    "SELECT COUNT(*) AS total
     FROM kunjungan"
    . $sqlWhere;

$stmtCount =
    $conn->prepare($sqlCount);

if (!empty($params)) {

    $stmtCount->bind_param(
        $types,
        ...$params
    );
}

$stmtCount->execute();

$total_data =
    $stmtCount
    ->get_result()
    ->fetch_assoc()['total'];

$total_halaman =
    ceil(
        $total_data /
        $jumlah_data_per_halaman
    );

if ($total_halaman < 1) {
    $total_halaman = 1;
}
$offset =
    ($halaman_aktif - 1)
    *
    $jumlah_data_per_halaman;

$sqlData =
    "SELECT *
     FROM kunjungan"
    . $sqlWhere .
    "
     ORDER BY id DESC
     LIMIT ?
     OFFSET ?
";

$stmt =
    $conn->prepare($sqlData);

$paramsData = $params;
$paramsData[] = $jumlah_data_per_halaman;
$paramsData[] = $offset;

$typesData =
    $types . "ii";

$stmt->bind_param(
    $typesData,
    ...$paramsData
);

$stmt->execute();

$result =
    $stmt->get_result();

$data_kunjungan = [];

while (
    $row =
    $result->fetch_assoc()
) {

    $data_kunjungan[] =
        $row;
}

if (!empty($where)) {

    $sqlWhere =
        " WHERE " .
        implode(" AND ", $where);
}

if (!empty($where)) {

    $sqlWhere =
        " WHERE " .
        implode(" AND ", $where);
}

function build_page_url($page, $filter_id, $filter_tanggal, $filter_keperluan) {
    $params = ['page' => $page];
    if ($filter_id        !== '') $params['filter_id']        = $filter_id;
    if ($filter_tanggal   !== '') $params['filter_tanggal']   = $filter_tanggal;
    if ($filter_keperluan !== '') $params['filter_keperluan'] = $filter_keperluan;
    return '?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Kelola Kunjungan</title>
    
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
            
            <div class="hidden md:flex items-center gap-10 font-normal text-black text-base ml-auto pr-20">
                <a href="dashboard.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm pb-1 transition">Dashboard</a>
                <a href="kelolakunjungan.php" class="font-semibold text-[#6a5750] border-b-4 rounded-sm border-[#6a5750] pb-1">Kelola Kunjungan</a>
                <a href="inbox.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm pb-1 transition">Inbox</a>
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
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Kelola Kunjungan</h2>
        </div>

        <div class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 w-fit h-12 overflow-visible">
            <form method="GET" action="kelolakunjungan.php" id="filterForm" class="h-full flex items-center justify-start">
                <input type="hidden" name="page" value="1" />
                
                <div class="flex items-center h-full">
                    <div class="h-full w-12 flex items-center justify-center border-r border-gray-200 shrink-0 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-[18px] h-[18px]"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" /></svg>
                    </div>
                    
                    <div class="h-full px-4 flex items-center border-r border-gray-200 shrink-0 bg-white">
                        <span class="text-xs font-bold text-gray-800 font-montserrat whitespace-nowrap">Filter By</span>
                    </div>

                    <div class="relative h-full border-r border-gray-200 bg-white min-w-[145px]" id="dropdown-id-tamu">
                        <button type="button" id="idTamuTrigger" class="w-full h-full justify-between px-4 text-xs font-semibold <?= $filter_id_tamu !== '' ? 'text-[#6a5750] font-bold' : 'text-gray-700' ?> hover:bg-gray-50/70 transition flex items-center gap-1.5 select-none">
                            <span id="idTamuSelectedText" class="truncate"><?= $filter_id_tamu !== '' ? htmlspecialchars($filter_id_tamu) : 'ID Tamu' ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-gray-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <input type="hidden" name="filter_id" id="idTamuValue" value="<?= htmlspecialchars($filter_id_tamu) ?>">

                        <div id="idTamuMenu" class="absolute top-[calc(100%+6px)] left-0 bg-white border border-gray-200 rounded-xl shadow-xl p-2.5 z-50 min-w-[220px] cursor-default hidden">
                            <div class="relative flex items-center mb-1.5">
                                <span class="absolute left-2.5 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" /></svg>
                                </span>
                                <input type="text" id="idTamuSearch" placeholder="Cari ID Tamu..." class="w-full h-[32px] pl-8 pr-3 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:border-brand transition" />
                            </div>
                            <ul id="idTamuList" class="max-h-[160px] overflow-y-auto custom-scroll flex flex-col gap-0.5">
                                <li class="item-id-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="">Semua ID</li>
                                <?php foreach($opsi_id_tamu as $id_opt): ?>
                                    <li class="item-id-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="<?= $id_opt ?>"><?= $id_opt ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="relative h-full border-r border-gray-200 bg-white min-w-[145px]" id="dropdown-kalender">
                        <button type="button" id="calendaTrigger" class="w-full h-full justify-between px-4 text-xs font-semibold hover:bg-gray-50/80 transition flex items-center select-none text-left">
                            <span id="calendarSelectedText" class="truncate <?= $filter_tanggal !== '' ? 'text-gray-900 font-bold' : 'text-gray-700' ?>">
                                <?= $filter_tanggal !== '' ? date('d M Y', strtotime($filter_tanggal)) : 'Tanggal' ?>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-gray-400 shrink-0 ml-1"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <input type="hidden" name="filter_tanggal" id="calendarValue" value="<?= htmlspecialchars($filter_tanggal) ?>">

                        <div id="calendarMenu" class="absolute top-[calc(100%+6px)] left-0 bg-white rounded-2xl shadow-xl p-5 z-50 min-w-[280px] cursor-default font-montserrat hidden border border-gray-100">
                            <div class="flex items-center justify-between mb-6 px-1">
                                <span id="calendarMonthYear" class="text-base font-bold text-[#1e293b]">Mei 2026</span>
                                <div class="flex items-center gap-4">
                                    <button type="button" id="prevMonth" class="text-gray-400 hover:text-gray-700 transition p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                                    </button>
                                    <button type="button" id="nextMonth" class="text-gray-400 hover:text-gray-700 transition p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-y-2 text-center text-xs font-semibold text-gray-400 mb-3">
                                <div>S</div><div>M</div><div>T</div><div>W</div><div>T</div><div>F</div><div>S</div>
                            </div>
                            <div id="calendarDaysGrid" class="grid grid-cols-7 gap-x-1 gap-y-2 text-center text-xs"></div>
                        </div>
                    </div>

                    <div class="relative h-full bg-white min-w-[155px]" id="dropdown-keperluan">
                        <button type="button" id="dropdownTrigger" class="w-full h-full justify-between px-4 text-xs font-semibold <?= $filter_keperluan !== '' ? 'text-[#6a5750] font-bold' : 'text-gray-700' ?> hover:bg-gray-50/80 transition flex items-center gap-1.5 select-none">
                            <span id="dropdownSelectedText" class="truncate"><?= $filter_keperluan !== '' ? htmlspecialchars($filter_keperluan) : 'Keperluan' ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-gray-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                        </button>
                        <input type="hidden" name="filter_keperluan" id="keperluanValue" value="<?= htmlspecialchars($filter_keperluan) ?>">

                        <div id="dropdownMenu" class="absolute top-[calc(100%+6px)] left-0 bg-white border border-gray-200 rounded-xl shadow-xl p-2.5 z-50 min-w-[220px] cursor-default hidden">
                            <div class="relative flex items-center mb-1.5">
                                <span class="absolute left-2.5 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" /></svg>
                                </span>
                                <input type="text" id="dropdownSearch" placeholder="Cari keperluan..." class="w-full h-[32px] pl-8 pr-3 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:border-brand transition" />
                            </div>
                            <ul id="dropdownList" class="max-h-[160px] overflow-y-auto custom-scroll flex flex-col gap-0.5">
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="">Semua Keperluan</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="Mahasiswa">Mahasiswa</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="Kunjungan Perpustakaan">Kunjungan Perpustakaan</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="Janji Temu">Janji Temu</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="Kunjungan Dinas">Kunjungan Dinas</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="Menghadiri Acara">Menghadiri Acara</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="UPT Perpustakaan">UPT Perpustakaan</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="Tata Usaha">Tata Usaha</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="Humas (Hubungan Masyarakat)">Humas (Hubungan Masyarakat)</li>
                                <li class="item-opt px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 rounded-md cursor-pointer font-medium transition" data-value="HIMA (Himpunan Mahasiswa)">HIMA (Himpunan Mahasiswa)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="h-full flex items-center pr-2 border-l border-gray-200 shrink-0 bg-white rounded-r-xl">
                    <button type="button" onclick="window.location.href='kelolakunjungan.php'" class="h-full flex items-center gap-2 px-5 text-[#e12d4d] hover:bg-red-50/50 text-xs font-bold font-montserrat transition-colors group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.8" stroke="currentColor" class="w-3.5 h-3.5 transform group-hover:-rotate-45 transition-transform"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"</svg>
                        <span>Reset Filter</span>
                    </button>
                </div>

            </form>
        </div>

        <div class="w-full bg-white rounded-2xl p-6 shadow-sm border border-gray-100 overflow-hidden">
            <div class="w-full overflow-x-auto custom-scroll">
                <table class="w-full text-left border-collapse min-w-[1000px] table-fixed" id="mainKunjunganTable">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="w-[14%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto">ID</th>
                            <th class="w-[18%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Nama Lengkap</th>
                            <th class="w-[18%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Tanggal dan Waktu</th>
                            <th class="w-[15%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Keperluan</th>
                            <th class="w-[15%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Asal atau Instansi</th>
                            <th class="w-[10%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto">Kontak</th>
                            <th class="w-[10%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto text-center">STATUS</th>
                            <th class="w-[8%] pb-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider font-roboto text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBodyData">
                        <?php foreach ($data_kunjungan as $row):
                            $status = strtolower($row['status']);
                            if ($status === 'in progress') { $badge_style = 'bg-purple-50 text-purple-500'; } 
                            elseif ($status === 'complete' || $status === 'completed') { $badge_style = 'bg-green-50 text-green-500'; } 
                            elseif ($status === 'pending') { $badge_style = 'bg-amber-50 text-amber-500'; } 
                            elseif ($status === 'canceled') { $badge_style = 'bg-red-50 text-red-500'; } 
                            elseif ($status === 'approved') { $badge_style = 'bg-blue-50 text-blue-500'; } 
                            else { $badge_style = 'bg-gray-100 text-gray-500'; }
                        ?>
                            <tr class="hover:bg-gray-50/50 transition table-row-item">
                                <td class="py-5 text-[11px] font-bold text-gray-700 font-roboto break-words pr-2">
                                    #KJ-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="py-5 text-xs font-semibold text-gray-800 break-words pr-2 search-target-nama"><?= htmlspecialchars($row['nama_pengunjung']) ?></td>
                                <td class="py-5 text-[11px] font-medium text-gray-500 font-roboto break-words pr-2"><?= date('d M Y', strtotime($row['tanggal'])) ?>
                                <?= htmlspecialchars($row['waktu_mulai']) ?>
                                -
                                <?= htmlspecialchars($row['waktu_selesai']) ?></td>
                                <td class="py-5 text-[11px] font-semibold text-gray-600 break-words pr-2"><?= htmlspecialchars($row['keperluan']) ?></td>
                                <td class="py-5 text-[11px] font-medium text-gray-400 font-roboto break-words pr-2 search-target-instansi"><?= htmlspecialchars($row['instansi']) ?></td>
                                <td class="py-5 text-[11px] font-medium text-gray-600 font-roboto break-words pr-2 search-target-kontak"><?= htmlspecialchars($row['no_telp']) ?></td>
                                <td class="py-5 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold <?= $badge_style ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td class="py-5 text-center">
                                    <a href="detailkunjungan.php?id=<?= $row['id'] ?>" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 text-[10px] font-bold px-3 py-1 rounded-full shadow-sm transition">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr id="noDataRow" class="hidden">
                            <td colspan="8" class="py-10 text-center text-xs font-medium text-gray-400">Data tidak ditemukan.</td>
                        </tr>
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
        const filterForm = document.getElementById('filterForm');

        // Logic Open/Close Panel Sidebar Notifikasi
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

        // Live Real-Time Searchbar Global pada Navbar
        const globalSearchInput = document.getElementById('globalTableSearch');
        const tableRows = document.querySelectorAll('.table-row-item');
        const noDataRow = document.getElementById('noDataRow');
        const paginationContainer = document.getElementById('paginationContainer');

        globalSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let matchedCount = 0;

            tableRows.forEach(row => {
                const namaText = row.querySelector('.search-target-nama').textContent.toLowerCase();
                const instansiText = row.querySelector('.search-target-instansi').textContent.toLowerCase();
                const kontakText = row.querySelector('.search-target-kontak').textContent.toLowerCase();

                if (namaText.includes(query) || instansiText.includes(query) || kontakText.includes(query)) {
                    row.style.display = '';
                    matchedCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (query !== '') {
                paginationContainer.classList.add('hidden');
                if (matchedCount === 0) noDataRow.classList.remove('hidden'); else noDataRow.classList.add('hidden');
            } else {
                paginationContainer.classList.remove('hidden');
                noDataRow.classList.add('hidden');
            }
        });

        // --- DROPDOWN LOGIC: ID TAMU ---
        const idTrigger = document.getElementById('idTamuTrigger');
        const idMenu = document.getElementById('idTamuMenu');
        const idSearch = document.getElementById('idTamuSearch');
        const idItems = document.querySelectorAll('.item-id-opt');
        const hiddenId = document.getElementById('idTamuValue');
        const idTextDisplay = document.getElementById('idTamuSelectedText');

        idTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.add('hidden');
            document.getElementById('calendarMenu').classList.add('hidden');
            idMenu.classList.toggle('hidden');
            if (!idMenu.classList.contains('hidden')) idSearch.focus();
        });

        idSearch.addEventListener('click', (e) => e.stopPropagation());
        idSearch.addEventListener('input', function() {
            const searchVal = this.value.toLowerCase();
            idItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchVal) ? 'block' : 'none';
            });
        });

        idItems.forEach(item => {
            item.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                hiddenId.value = val;
                idTextDisplay.textContent = val !== '' ? val : 'ID Tamu';
                idMenu.classList.add('hidden');
                filterForm.submit();
            });
        });

        // --- DROPDOWN LOGIC: KEPERLUAN ---
        const dTrigger = document.getElementById('dropdownTrigger');
        const dMenu = document.getElementById('dropdownMenu');
        const dSearch = document.getElementById('dropdownSearch');
        const dItems = document.querySelectorAll('.item-opt');
        const hiddenKeperluan = document.getElementById('keperluanValue');
        const dTextDisplay = document.getElementById('dropdownSelectedText');

        dTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            idMenu.classList.add('hidden');
            document.getElementById('calendarMenu').classList.add('hidden');
            dMenu.classList.toggle('hidden');
            if (!dMenu.classList.contains('hidden')) dSearch.focus();
        });

        dSearch.addEventListener('click', (e) => e.stopPropagation());
        dSearch.addEventListener('input', function() {
            const searchVal = this.value.toLowerCase();
            dItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchVal) ? 'block' : 'none';
            });
        });

        dItems.forEach(item => {
            item.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                hiddenKeperluan.value = val;
                dTextDisplay.textContent = val !== '' ? val : 'Keperluan';
                dMenu.classList.add('hidden');
                filterForm.submit();
            });
        });

        // --- INTERAKTIF CUSTOM KALENDER LOGIC ---
        const calTrigger = document.getElementById('calendaTrigger');
        const calMenu = document.getElementById('calendarMenu');
        const calMonthYearLabel = document.getElementById('calendarMonthYear');
        const calDaysGrid = document.getElementById('calendarDaysGrid');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        const hiddenCalendar = document.getElementById('calendarValue');
        const calTextDisplay = document.getElementById('calendarSelectedText');

        let currentDate = hiddenCalendar.value ? new Date(hiddenCalendar.value) : new Date();
        const monthsName = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        function renderCustomCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            calMonthYearLabel.textContent = `${monthsName[month]} ${year}`;

            const firstDayIndex = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();

            calDaysGrid.innerHTML = '';
            for (let i = 0; i < firstDayIndex; i++) {
                calDaysGrid.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= totalDays; day++) {
                const dayWrapper = document.createElement('div');
                dayWrapper.className = "w-full aspect-square flex items-center justify-center";

                const dayBtn = document.createElement('button');
                dayBtn.type = 'button';
                dayBtn.textContent = day;

                const meshMonth = String(month + 1).padStart(2, '0');
                const meshDay = String(day).padStart(2, '0');
                const dateStr = `${year}-${meshMonth}-${meshDay}`;

                if (hiddenCalendar.value === dateStr) {
                    dayBtn.className = 'w-8 h-8 flex items-center justify-center rounded-full bg-[#6a5750] text-white font-semibold text-xs shadow-sm';
                } else {
                    dayBtn.className = 'w-8 h-8 flex items-center justify-center rounded-full text-[#1e293b] hover:bg-gray-100 font-medium text-xs transition';
                }

                dayBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    hiddenCalendar.value = dateStr;
                    const shortMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    calTextDisplay.textContent = `${meshDay} ${shortMonths[month]} ${year}`;
                    calMenu.classList.add('hidden');
                    filterForm.submit();
                });

                dayWrapper.appendChild(dayBtn);
                calDaysGrid.appendChild(dayWrapper);
            }
        }

        calTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            idMenu.classList.add('hidden');
            dMenu.classList.add('hidden');
            if(!calMenu.classList.toggle('hidden')) renderCustomCalendar();
        });

        prevMonthBtn.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() - 1); renderCustomCalendar(); });
        nextMonthBtn.addEventListener('click', (e) => { e.stopPropagation(); currentDate.setMonth(currentDate.getMonth() + 1); renderCustomCalendar(); });

        document.addEventListener('click', () => {
            idMenu.classList.add('hidden');
            calMenu.classList.add('hidden');
            dMenu.classList.add('hidden');
        });
    </script>
</body>
</html>