<?php
// ==========================================
// --- TAMULOGIN.PHP (LOGIKA BACKEND TAMU) ---
// ==========================================
session_start();

$error_message = "";

$stmt = $conn->prepare("
    SELECT id
    FROM visitors
    WHERE email = ?
");

$stmt->bind_param("s",$email);
$stmt->execute();

$result = $stmt->get_result();

require_once 'config.php';

$stmt = $conn->prepare("
SELECT *
FROM kunjungan
WHERE email = ?
");

$stmt->bind_param("s",$email);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if($data)
{
    $_SESSION['tamu_email'] = $email;
    $_SESSION['otp_tamu'] = rand(1000,9999);

    header("Location: tamuotp.php");
    exit();
}
else
{
    $error_message = "Email tidak ditemukan.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Status Kunjungan</title>
    
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

    <main class="relative z-20 w-full max-w-[1160px] mx-auto px-6 md:px-8 pt-12 md:pt-20 flex-grow flex items-center justify-center">
        
        <div class="w-full rounded-[40px] p-[1.5px] bg-gradient-to-tl from-[#6a5750]/70 via-[#6a5750]/35 to-transparent shadow-[0_20px_50px_rgba(106,87,80,0.12)] mb-24">
            
            <div class="w-full bg-white/85 backdrop-blur-md rounded-[39px] grid grid-cols-1 lg:grid-cols-12 overflow-hidden min-h-[560px]">
                
                <div class="lg:col-span-6 p-6 md:p-8 flex flex-col justify-center items-center text-center relative border-b lg:border-b-0 lg:border-r border-gray-100/50">
                    <div class="max-w-[380px] flex flex-col items-center">
                        <img src="image/logotamu.svg" alt="Buka Akses Status" class="w-[220px] h-[220px] md:w-[360px] md:h-[280px] object-contain mb-2 transform hover:scale-105 transition duration-300" />
                        
                        <h2 class="text-2xl md:text-[30px] font-bold tracking-tight leading-tight">
                            <span class="text-[#3a2e2a]">Buka akses</span> <span class="text-[#5c4a44]">status kunjungan anda.</span>
                        </h2>
                    </div>
                </div>

                <div class="lg:col-span-6 p-4 md:p-6 flex flex-col justify-center items-center relative">
                    
                    <div class="w-full max-w-[520px] flex flex-col items-center">
                        
                        <div class="flex items-center gap-2 mb-2">
                            <img class="w-[48px] h-[48px] object-contain" src="image/Logo.svg" alt="Logo Mini" />
                            <span class="font-extrabold text-black text-lg tracking-wide">SIPPK</span>
                        </div>

                        <h3 class="text-xl md:text-[22px] font-bold text-gray-600 tracking-tight text-center mb-1">
                            Cari Data Kunjungan
                        </h3>
                        <p class="text-xs text-gray-500 font-medium font-roboto text-center max-w-[360px] mb-8 leading-relaxed">
                            Masukkan e-mail yang terdaftar untuk memuat detail status tiket.
                        </p>

                        <?php if (!empty($error_message)): ?>
                            <div class="w-full bg-red-50/80 border border-red-200 text-red-600 text-xs rounded-xl p-3 mb-4 text-center font-medium">
                                <?= $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="statuskunjungan.php" method="POST" autocomplete="off" class="w-full flex flex-col gap-4">
                            
                            <div class="w-full flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-gray-400 font-roboto">Nama</label>
                                <input
                                    type="text"
                                    name="nama"
                                    placeholder="Nama Lengkap"
                                    required
                                >

                                <input
                                    type="text"
                                    name="no_telp"
                                    placeholder="Nomor Telepon"
                                    required
                                >
                            </div>

                            <button type="submit" class="w-full h-[38px] bg-[#6a5750] hover:bg-[#574741] text-white font-medium text-sm rounded-md shadow transition transform active:scale-95 flex items-center justify-center gap-2 mt-1">
                                Cek Status
                            </button>

                        </form>

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
