<?php
$id_kunjungan = isset($_GET['id'])? (int) $_GET['id']: 0;

$asal_halaman = $_GET['from'] ?? '';

if ($id_kunjungan <= 0) {
    die("ID kunjungan tidak valid.");
}

require_once 'config.php';

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

$data_tiket =
    $stmt
    ->get_result()
    ->fetch_assoc();

if (!$data_tiket) {
    die("Data kunjungan tidak ditemukan.");
}

$nomor_tiket =
    "KJ-" .
    str_pad(
        $data_tiket['id'], 5, '0', STR_PAD_LEFT
    );

$tanggal_display =
    date(
        'd M Y',
        strtotime(
            $data_tiket['tanggal']
        )
    );

$waktu_display =
    $data_tiket['waktu_mulai']
    . ' - '
    . $data_tiket['waktu_selesai'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Tiket Kunjungan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
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
        @media print {
            body { background: white; color: black; }
            .no-print { display: none !important; }
            .print-card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; margin: 0 !important; }
            /* Menyembunyikan aksen lubang lengkung saat dicetak agar hemat tinta/rapi */
            .ticket-notch { display: none !important; } 
        }
    </style>
</head>
<body class="bg-white min-h-screen w-full flex flex-col font-montserrat antialiased overflow-x-hidden relative">

    <div class="absolute top-0 left-0 w-full h-full z-0 overflow-hidden pointer-events-none no-print">
        <img class="w-full h-full object-cover opacity-[0.12]" src="image/batik.png" alt="Batik Background" />
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/40 to-white"></div>
    </div>

    <nav class="relative z-30 w-full h-[72px] bg-white shadow-sm flex justify-center items-center px-4 md:px-8 no-print">
        <div class="w-full max-w-[1400px] flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <img class="w-[39px] h-[37px] object-contain" src="image/Logo.png" alt="Logo" />
                <span class="font-bold text-black text-2xl tracking-[0.10px]">SIPPK</span>
            </div>
            <div class="flex items-center gap-6 md:gap-11">
                <div class="hidden md:flex items-center gap-11 font-normal text-black text-base">
                    <a href="index.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Home</a>
                    <a href="tamulogin.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Status Kunjungan</a>
                    <a href="contact.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Contact Us?</a>
                </div>
                <a href="adminlogin.php" class="inline-block bg-[#6a5750] hover:bg-[#574741] text-white font-normal text-base px-5 py-2 rounded transition shadow-sm">
                    Masuk Admin
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-20 w-full max-w-[1000px] mx-auto px-4 pt-16 md:pt-24 flex-grow flex flex-col items-center justify-center mb-24">
        
        <div class="text-center mb-14">
            <?php if ($asal_halaman === 'history'): ?>
                <h1 class="text-3xl md:text-[40px] font-bold text-black tracking-tight">Detail Tiket Kunjungan</h1>
                <p class="text-xs md:text-sm text-gray-500 font-medium mt-2">Gunakan QR Code di bawah untuk memverifikasi kedatangan Anda di lokasi</p>
            <?php else: ?>
                <h1 class="text-3xl md:text-[40px] font-bold text-black tracking-tight">Pendaftaran Formulir Berhasil!</h1>
                <p class="text-xs md:text-sm text-gray-500 font-medium mt-2">Tiket kunjungan telah dikirim dan dapat diunduh melalui E-mail Anda</p>
            <?php endif; ?>
        </div>

        <div class="print-card w-full max-w-[1000px] bg-[#F7F7F7] rounded-none shadow-xl flex flex-row items-stretch overflow-hidden border border-gray-100 relative">
            
            <div class="bg-[#6A5750] w-[64px] flex items-center justify-center flex-shrink-0 select-none">
                <span class="transform -rotate-90 text-white font-medium text-sm tracking-[2px] whitespace-nowrap opacity-85">
                    #<?= $nomor_tiket; ?>
                </span>
            </div>

            <div class="flex-grow bg-white p-6 md:p-8 flex flex-col justify-between gap-6">
                <div class="pt-2 pl-6 md:pl-8">
                    <h2 class="text-3xl md:text-[32px] font-bold text-black tracking-wide">Tiket Kunjungan SIPPK</h2>
                </div>

                <div class="flex flex-col gap-4 pl-6 md:pl-8">
                    <div class="flex flex-col">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama</span>
                        <div class="bg-[#EAEAEA] inline-block px-8 py-2.5 rounded-none text-sm font-semibold text-gray-800 self-start min-w-[220px]">
                            <?= htmlspecialchars($data_tiket['nama_pengunjung']); ?>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Keperluan</span>
                        <div class="bg-[#EAEAEA] inline-block px-8 py-2.5 rounded-none text-sm font-semibold text-gray-800 self-start">
                            <?= $data_tiket['keperluan']; ?>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tujuan</span>
                        <div class="bg-[#EAEAEA] inline-block px-8 py-2.5 rounded-none text-sm font-semibold text-gray-800 self-start">
                            <?= htmlspecialchars($data_tiket['tujuan']); ?>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</span>
                        <div class="bg-[#EAEAEA] inline-block px-8 py-2.5 rounded-none text-sm font-semibold text-gray-800 self-start">
                            <?= htmlspecialchars($data_tiket['status']); ?>
                        </div>
                    </div>
                    <div class="flex flex-row gap-16 items-center pt-1">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal</span>
                            <div class="bg-[#EAEAEA] px-12 py-2.5 rounded-none text-sm font-semibold text-gray-800 tracking-wide">
                                <?= htmlspecialchars($tanggal_display); ?>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Waktu Berkunjung</span>
                            <div class="bg-[#EAEAEA] px-12 py-2.5 rounded-none text-sm font-semibold text-gray-800 tracking-wide">
                                <?= htmlspecialchars($waktu_display); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative w-[24px] bg-white flex flex-col justify-between items-center select-none overflow-visible">
                <div class="ticket-notch absolute top-[-12px] left-1/2 transform -translate-x-1/2 w-6 h-6 bg-white rounded-full border border-gray-100 shadow-[inset_0_-3px_5px_rgba(0,0,0,0.03)] z-10"></div>
                
                <div class="h-full border-r-2 border-dashed border-gray-300 my-4"></div>
                
                <div class="ticket-notch absolute bottom-[-12px] left-1/2 transform -translate-x-1/2 w-6 h-6 bg-white rounded-full border border-gray-100 shadow-[inset_0_3px_5px_rgba(0,0,0,0.03)] z-10"></div>
            </div>

            <div class="w-[240px] md:w-[280px] bg-white p-6 flex flex-col items-center justify-center flex-shrink-0 border-l border-gray-50">
                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-4">Scan to check in</span>
                
                <div class="p-2 border border-gray-200/80 rounded-none bg-white shadow-sm flex items-center justify-center">
                    <img class="w-32 h-32 md:w-36 md:h-36 object-contain" src="image/qrcode-placeholder.png" alt="QR Code" />
                </div>
            </div>

        </div>

        <div class="no-print w-full flex flex-col items-center justify-center gap-4 mt-8">
            <a href="statuskunjungan.php" class="w-full max-w-[340px] h-[40px] bg-[#6a5750] hover:bg-[#574741] text-white font-medium text-sm rounded-lg shadow-md transition transform active:scale-95 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                Cek Status Kunjungan
            </a>
            
            <button onclick="window.print();" class="text-xs font-semibold text-gray-400 hover:text-gray-600 transition underline decoration-dashed">
                Cetak Tiket / Simpan PDF
            </button>
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
