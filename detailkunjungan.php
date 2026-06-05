<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

require_once 'config.php';
require_once 'notif.php';
// Admin harus login br bisa masuk sini.
if (
    !isset($_SESSION['admin_id']) ||
    !isset($_SESSION['otp_verified']) ||
    $_SESSION['otp_verified'] !== true
) {
    header("Location: adminlogin.php");
    exit();
}

//paten
$id_kunjungan = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id_kunjungan <= 0) {

    header("Location: kelolakunjungan.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $target_id = (int) ($_POST['id_kunjungan'] ?? 0);
    $admin_notes = trim($_POST['notes'] ?? '');

    switch ($action) {

        case 'terima':

            $stmt = $conn->prepare("
                UPDATE kunjungan
                SET status = 'disetujui'
                WHERE id = ?
            ");

            $stmt->bind_param(
                "i",
                $target_id
            );

            $stmt->execute();

            header(
                "Location: detailkunjungan.php?id="
                . $target_id .
                "&status=success_terima"
            );

            exit();

        case 'tolak':

            $stmt = $conn->prepare("
                UPDATE kunjungan
                SET status = 'ditolak'
                WHERE id = ?
            ");

            $stmt->bind_param(
                "i",
                $target_id
            );

            $stmt->execute();

            header(
                "Location: detailkunjungan.php?id="
                . $target_id .
                "&status=success_tolak"
            );

            exit();

        case 'selesai':

            $stmt = $conn->prepare("
                UPDATE kunjungan
                SET status = 'selesai'
                WHERE id = ?
            ");

            $stmt->bind_param(
                "i",
                $target_id
            );

            $stmt->execute();

            header(
                "Location: detailkunjungan.php?id="
                . $target_id .
                "&status=success_selesai"
            );

            exit();

        case 'save':

            $stmt = $conn->prepare("
                UPDATE kunjungan
                SET notes = ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "si",
                $admin_notes,
                $target_id
            );

            $stmt->execute();

            header(
                "Location: detailkunjungan.php?id="
                . $target_id .
                "&status=success_save"
            );

            exit();

        case 'delete':

            $stmt = $conn->prepare("
                DELETE FROM kunjungan
                WHERE id = ?
            ");

            $stmt->bind_param(
                "i",
                $target_id
            );

            $stmt->execute();

            header(
                "Location: kelolakunjungan.php?status=success_delete"
            );

            exit();
    }
}

$stmt = $conn->prepare("
    SELECT *
    FROM kunjungan
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $id_kunjungan
);

$stmt->execute();

$data = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$data) {

    die("Data kunjungan tidak ditemukan.");
}
$status = strtolower(
    $data['status']
);
if ($status == 'menunggu') {
    $badge =
        'bg-yellow-100 text-yellow-700';
} elseif ($status == 'disetujui') {
    $badge =
        'bg-green-100 text-green-700';
} elseif ($status == 'ditolak') {
    $badge =
        'bg-red-100 text-red-700';
} elseif ($status == 'selesai') {
    $badge =
        'bg-blue-100 text-blue-700';
} else {
    $badge =
        'bg-gray-100 text-gray-700';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Profil Tamu</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
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
</head>
<body class="bg-[#f3f4f6] min-h-screen w-full flex flex-col font-montserrat antialiased overflow-x-hidden relative">

    <div class="absolute top-0 left-0 w-full h-full z-0 overflow-hidden pointer-events-none">
        <img class="w-full h-full object-cover opacity-[0.12]" src="image/batik.png" alt="Batik Background" />
    </div>

    <nav class="relative z-30 w-full h-[72px] bg-white shadow-sm flex justify-center items-center px-4 md:px-8">
        <div class="w-full max-w-[1400px] flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <img class="w-[39px] h-[37px] object-contain" src="image/Logo.png" alt="Logo" />
                <span class="font-bold text-black text-2xl tracking-[0.10px]">SIPPK</span>
            </div>
            
            <div class="hidden md:flex items-center gap-11 font-normal text-black text-base mx-auto pl-12">
                <a href="dashboard.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm pb-1 transition">Dashboard</a>
                <a href="kelolakunjungan.php" class="font-semibold text-[#6a5750] border-b-4 rounded-sm border-[#6a5750] pb-1">Kelola Kunjungan</a>
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

    <div class="relative z-10 w-full h-[160px] md:h-[230px] overflow-hidden shadow-sm flex items-center">
        <img class="absolute inset-0 w-full h-full object-cover z-0" src="image/hero-detail.svg" alt="Background Hero" />
        <div class="relative z-10 w-full max-w-[1200px] mx-auto text-left px-4 md:px-8">
            <span class="text-xs font-semibold text-gray-300 opacity-80 block mb-1 font-roboto">Kelola Kunjungan / Profil Tamu</span>
            <h1 class="font-bold text-white text-2xl sm:text-3xl md:text-[36px] tracking-wide leading-tight drop-shadow-md">
                Profil Tamu
            </h1>
        </div>
    </div>

    <main class="relative z-10 w-full max-w-[1200px] mx-auto px-4 md:px-8 -mt-10 md:-mt-14 pb-16 flex flex-col gap-6 justify-start">
        
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 md:p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 border border-gray-100 shadow-sm flex items-center justify-center">
                    <img src="image/avatar-profile.svg" alt="Foto Profil" class="w-full h-full object-cover" />
                </div>
                <div class="flex flex-col min-w-0">
                    <h2 class="text-lg font-bold text-gray-600 truncate"><?= htmlspecialchars($data['nama_pengunjung']) ?></h2>
                    <span class="text-xs font-medium text-gray-400 truncate font-roboto mt-0.5"><?= htmlspecialchars($data['no_telp']) ?></span>
                </div>
            </div>
            
            <div class="w-full sm:w-auto">
                <form method="POST" class="flex items-center gap-4 w-full sm:w-auto justify-end text-xs font-bold font-montserrat">
                    <input type="hidden" name="id_kunjungan" value="<?= htmlspecialchars($id_kunjungan) ?>">
                    <button type="submit" name="action" value="terima" class="flex items-center gap-1.5 text-[#00a884] hover:opacity-80 transition py-2 px-3 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        TERIMA KUNJUNGAN
                    </button>
                    <button type="submit" name="action" value="tolak" class="flex items-center gap-1.5 text-[#ef4444] hover:opacity-80 transition py-2 px-3 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        TOLAK
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <div class="lg:col-span-7 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-5">
                <h3 class="text-sm font-bold text-gray-500 tracking-tight border-b border-gray-100 pb-2">Informasi Profil</h3>
                
                <div class="flex flex-col gap-4 text-xs font-medium font-roboto">
                    <div class="flex flex-col sm:flex-row sm:items-center py-0.5">
                        <span class="text-gray-400 font-bold uppercase tracking-wider w-full sm:w-[150px] flex-shrink-0">Nama Lengkap</span>
                        <span class="text-gray-500 font-medium hidden sm:inline mr-3">:</span>
                        <span class="text-gray-600 font-semibold text-sm mt-0.5 sm:mt-0"><?= htmlspecialchars($data['nama_pengunjung']) ?></span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center py-0.5">
                        <span class="text-gray-400 font-bold uppercase tracking-wider w-full sm:w-[150px] flex-shrink-0">Nomor Telepon</span>
                        <span class="text-gray-500 font-medium hidden sm:inline mr-3">:</span>
                        <span class="text-gray-600 font-semibold mt-0.5 sm:mt-0"><?= htmlspecialchars($data['no_telp']) ?></span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center py-0.5">
                        <span class="text-gray-400 font-bold uppercase tracking-wider w-full sm:w-[150px] flex-shrink-0">Tanggal & Waktu</span>
                        <span class="text-gray-500 font-medium hidden sm:inline mr-3">:</span>
                        <span class="text-gray-600 font-semibold mt-0.5 sm:mt-0"><?= date('d M Y', strtotime($data['tanggal'])) ?>
                        <?= htmlspecialchars($data['waktu_mulai']) ?>
                        -
                        <?= htmlspecialchars($data['waktu_selesai']) ?></span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center py-0.5">
                        <span class="text-gray-400 font-bold uppercase tracking-wider w-full sm:w-[150px] flex-shrink-0">Asal / Instansi</span>
                        <span class="text-gray-500 font-medium hidden sm:inline mr-3">:</span>
                        <span class="text-gray-600 font-bold uppercase mt-0.5 sm:mt-0 tracking-wide truncate"><?= htmlspecialchars($data['instansi']) ?></span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-4">
                <h3 class="text-sm font-bold text-gray-500 tracking-tight border-b border-gray-100 pb-2">Maksud dan Tujuan Kunjungan</h3>
                <p class="text-xs font-medium text-gray-500 font-roboto leading-relaxed text-justify">
                    <?= htmlspecialchars($data['keperluan']) ?>
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col gap-3">
            <h3 class="text-sm font-bold text-gray-500 tracking-tight">Admin Notes (Opsional)</h3>
            
            <form id="form-admin-notes" method="POST" class="flex flex-col gap-3">
                <input type="hidden" name="id_kunjungan" value="<?= htmlspecialchars($id_kunjungan) ?>">
                <textarea name="notes" class="w-full min-h-[70px] text-xs font-medium text-gray-500 font-roboto bg-[#f9fafb] border border-gray-200/80 rounded-xl p-4 focus:outline-none focus:ring-1 focus:ring-[#6a5750] transition resize-none" placeholder="Tambahkan catatan administrasi atau alasan penolakan di sini..."><?= htmlspecialchars($data['notes'] ?? '') ?></textarea>
                
                <div class="flex justify-end gap-3 mt-2 text-xs font-bold">
                    <button type="submit" name="action" value="delete" class="bg-red-50 text-red-500 hover:bg-red-100 transition px-5 py-2.5 rounded-xl flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Delete
                    </button>
                    <button type="submit" name="action" value="save" class="bg-[#564641] text-white hover:bg-[#463834] transition px-6 py-2.5 rounded-xl flex items-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Save
                    </button>
                </div>
            </form>
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
    </script>
</body>
</html>