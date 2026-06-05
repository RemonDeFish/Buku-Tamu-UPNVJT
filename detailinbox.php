<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

require_once 'config.php';
$id = (int)($_GET['id'] ?? 0);

$update = $conn->prepare("
    UPDATE inbox
    SET status = 'Sudah Dibaca'
    WHERE id = ?
");

if (!$update) {
    die('Query update gagal: ' . $conn->error);
}

$update->bind_param("i", $id);
$update->execute();

$stmt = $conn->prepare("
    SELECT *
    FROM inbox
    WHERE id = ?
");

if (!$stmt) {
    die('Query select gagal: ' . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die('Pesan tidak ditemukan');
}

// --- HANDLING FORM ACTIONS (MOCKUP/SIMULASI FRONTEND) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'delete':
            $hapus = $conn->prepare("
                DELETE FROM inbox
                WHERE id = ?
            ");
            $hapus->bind_param("i",$id);
            $hapus->execute();
            header("Location: inbox.php?status=success_delete");
            exit;
            }
}

/*
IAMSDJGPNWEFNKWEBFLBEWLNJSBLKBDKGHSBDG
NOTIFIKASI PESAN
NKUASDIFJAIUSDGYF ERIAFHBSKHDGILUDSLKL
*/

$qInbox = $conn->query("
    SELECT
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
            $row['subjek']
    ];
}

$jumlah_notif = count($notifikasi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Detail Inbox</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#6a5750',
                        brandHover: '#574741'
                    },
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
                <a href="kelolakunjungan.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm pb-1 transition">Kelola Kunjungan</a>
                <a href="inbox.php" class="font-semibold text-[#6a5750] border-b-4 rounded-sm border-[#6a5750] pb-1">Inbox</a>
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
            <span class="text-xs font-semibold text-gray-300 opacity-80 block mb-1 font-roboto">Inbox / Detail Pesan</span>
            <h1 class="font-bold text-white text-2xl sm:text-3xl md:text-[36px] tracking-wide leading-tight drop-shadow-md">
                Detail Pesan
            </h1>
        </div>
    </div>

    <main class="relative z-10 w-full max-w-[1200px] mx-auto px-4 md:px-8 -mt-10 md:-mt-14 pb-16 flex flex-col gap-6 justify-start">
        
        <div class="bg-white rounded-2xl shadow-md border border-gray-100/80 p-6 md:p-8 flex flex-col gap-6">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-5">
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <div class="w-12 h-12 rounded-full bg-[#6a5750] flex items-center justify-center text-white text-base font-bold font-montserrat shadow-sm shrink-0 select-none">
                        <?= strtoupper(substr($data['nama_lengkap'], 0, 1)) ?>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <h2 class="text-base font-bold text-gray-800 truncate leading-tight"><?= htmlspecialchars($data['nama_lengkap']) ?></h2>
                        <span class="text-xs font-medium text-gray-400 font-roboto truncate mt-0.5">&lt;<?= htmlspecialchars($data['email']) ?>&gt;</span>
                        <span class="text-[11px] font-medium text-gray-400 font-roboto mt-0.5"><?= htmlspecialchars($data['no_telp']) ?></span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between sm:justify-end gap-5 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-50">
                    <div class="text-right flex flex-col">
                        <span class="text-[10px] font-medium text-gray-400 font-roboto mt-0.5"><?= date('d F Y H:i', strtotime($data['created_at'])) ?></span>
                    </div>
                    
                    <form method="POST" action="" class="flex items-center" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button type="submit" name="action" value="delete" class="p-2 bg-red-50 text-red-500 hover:bg-red-100 rounded-xl transition flex items-center justify-center shadow-sm border border-red-100/50" title="Hapus Pesan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Subject :</span>
                <h3 class="text-base font-bold text-gray-900 leading-snug">
                    <?= htmlspecialchars($data['subjek']) ?>
                </h3>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <?php if ($data['status'] === 'Belum Dibaca'): ?>
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-lg">
                        Belum Dibaca
                    </span>
                <?php else: ?>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-600 rounded-lg">
                        Sudah Dibaca
                    </span>
                <?php endif; ?>
            </div>
            <div class="bg-[#f9fafb] border border-gray-100 rounded-2xl p-5 md:p-6 min-h-[180px]">
                <p class="text-xs font-medium text-gray-600 font-roboto leading-relaxed text-justify whitespace-pre-line">
                    <?= htmlspecialchars($data['pesan']) ?>
                </p>
            </div>

            <div class="flex justify-end items-center mt-2">
                <a href="inbox.php" class="bg-[#6a5750] text-white hover:bg-[#574741] font-bold text-xs px-6 py-2.5 rounded-xl flex items-center gap-2 transition shadow-sm tracking-wide">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    KEMBALI
                </a>
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