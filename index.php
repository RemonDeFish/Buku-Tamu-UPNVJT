<?php
// Mengatur zona waktu default ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

// Membuat array manual untuk lokalisasi nama hari dan bulan ke Bahasa Indonesia
$hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
$bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

// Tanggal hari ini dikunci dari Sisi Server PHP agar akurat
$tanggal_display = $hari[date('w')] . ", " . date('j') . " " . $bulan[date('n')] . " " . date('Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Sistem Informasi Pengunjung</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        montserrat: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white min-h-screen w-full flex flex-col font-montserrat antialiased overflow-x-hidden relative">

    <div class="absolute top-0 left-0 w-full h-[440px] z-0 overflow-hidden pointer-events-none">
        <img class="w-full h-full object-cover opacity-100" src="image/batik.png" alt="Batik Background" />
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/70 to-white"></div>
    </div>

    <nav class="relative z-20 w-full h-[72px] bg-white shadow-sm flex justify-center items-center px-4 md:px-8">
        <div class="w-full max-w-[1400px] flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <img class="w-[39px] h-[37px] object-contain" src="image/Logo.png" alt="Logo" />
                <span class="font-bold text-black text-2xl tracking-[0.10px]">SIPPK</span>
            </div>
            
            <div class="flex items-center gap-6 md:gap-11">
                <div class="hidden md:flex items-center gap-11 font-normal text-black text-base">
                    <a href="index.php" class="font-semibold text-[#6a5750] border-b-4 rounded-sm border-[#6a5750] pb-1">Home</a>
                    <a href="statuskunjungan.php" class="hover:text-[#6a5750] transition">Cek Status Kunjungan</a>
                    <a href="https://wa.me/6282261882303" class="hover:text-[#6a5750] transition">Contact Us?</a>
                </div>
                <a href="adminlogin.php" class="inline-block bg-[#6a5750] hover:bg-[#574741] text-white font-normal text-base px-5 py-2 rounded transition shadow-sm">
                    Masuk Admin
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-10 w-full max-w-[1200px] mx-auto px-6 md:px-8 pt-10 md:pt-16 flex flex-col gap-6 justify-start">
        
        <header class="w-full pt-2 text-center mb-8 md:mb-12">
            <h1 class="font-bold text-[#121212] text-2xl md:text-[32px] tracking-[-0.16px] leading-tight max-w-5xl mx-auto">
                Sistem Informasi Pengunjung dan Pertemuan Kampus<br class="hidden md:inline"/> (SIPPK)
            </h1>
        </header>

        <div class="w-full h-[135px] bg-gradient-to-b from-[#6a5750] to-[#9e8d87] rounded-[24px] px-8 md:px-12 flex justify-between items-center text-white shadow-sm mb-2 relative overflow-hidden">
            <div class="flex flex-col gap-0.5">
                <h2 class="font-semibold text-2xl md:text-[32px] tracking-wide text-white drop-shadow-sm">Buku Tamu SIPPK</h2>
                <p class="text-sm md:text-lg font-semibold text-white/90 drop-shadow-sm" id="currentDate"><?php echo $tanggal_display; ?></p>
            </div>
            <div class="flex items-center h-full">
                <span class="font-medium text-3xl md:text-[42px] tracking-widest text-white drop-shadow-sm" id="currentTime">00 : 00 : 00</span>
            </div>
        </div>
    </main>

    <section class="relative z-10 w-full bg-[#faf2ef] shadow-sm grid grid-cols-1 md:grid-cols-12 items-stretch border-y border-gray-100 min-h-[250px] mt-12 mb-20">
        <div class="md:col-span-12 max-w-[1200px] w-full mx-auto px-6 md:px-8 grid grid-cols-1 md:grid-cols-12 items-stretch h-full">
            
            <div class="md:col-span-7 flex flex-col justify-center items-center text-center p-6 md:p-8 h-full">
                <h3 class="font-bold text-black text-2xl md:text-[32px] tracking-wide mb-2">Daftar Antrean</h3>
                <p class="font-medium text-gray-600 text-xs md:text-[14px] max-w-[420px] leading-relaxed mb-4">
                    Silakan daftarkan kunjungan Anda untuk mendapatkan nomor antrean.
                </p>
                <div>
                    <a href="formtamu.php" class="inline-block bg-[#6a5750] hover:bg-[#574741] text-white text-sm px-8 py-2.5 rounded-[10px] transition shadow-md font-medium">
                        Formulir Tamu
                    </a>
                </div>
            </div>

            <div class="md:col-span-5 flex justify-center items-center p-0 relative min-h-[220px] md:min-h-full">
                <img class="absolute w-full h-[175%] object-contain drop-shadow-2xl transform scale-125 -translate-x-6 translate-y-3 z-30 origin-center" src="image/logoduduk.svg" alt="Ilustrasi SIPPK" />
            </div>

        </div>
    </section>

    <footer class="relative w-full min-h-[140px] mt-auto z-10 flex flex-col justify-end overflow-hidden">
        <img class="absolute bottom-0 left-0 w-full h-[115%] object-cover z-0 pointer-events-none transform translate-y-6" src="image/vector-1.svg" alt="Footer Wave" />
        
        <div class="relative z-10 w-full max-w-[1200px] mx-auto px-6 md:px-8 pb-6 flex flex-col gap-4">
            <div class="flex gap-3 justify-center md:justify-start">
                <a href="#" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/twitter-icon.svg" alt="Twitter" /></a>
                <a href="#" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/linkedin-icon.svg" alt="Linkedin" /></a>
                <a href="#" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/instagram-icon.svg" alt="Instagram" /></a>
                <a href="https://www.youtube.com/@MelvinJovannySimon" class="hover:opacity-80 transition"><img class="w-[24px] h-[24px]" src="image/youtube-icon.svg" alt="Youtube" /></a>
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

    <script>
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('currentTime').innerText = `${hours} : ${minutes} : ${seconds}`;
        }
        setInterval(updateTime, 1000);
        updateTime(); // Jalankan instan di awal
    </script>
</body>
</html>