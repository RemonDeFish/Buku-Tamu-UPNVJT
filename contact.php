<?php
// --- LOGIKA BACKEND PHP (SIAP DIKEMBANGKAN) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email        = $_POST['email'];
    $no_telp      = $_POST['no_telp'];
    $subjek       = isset($_POST['subjek']) ? $_POST['subjek'] : '';
    $pesan        = $_POST['pesan'];

    // --- TEMPAT QUERY DATABASE / EMAIL ENGINE ANDA ---
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Contact Us</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                    <a href="tamulogin.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Status Kunjungan</a>
                    <a href="contact.php" class="text-[#6a5750] font-semibold border-b-4 rounded-sm border-[#6a5750] pb-1">Contact Us?</a>
                </div>
                <a href="adminlogin.php" class="inline-block bg-[#6a5750] hover:bg-[#574741] text-white font-normal text-base px-5 py-2 rounded transition shadow-sm">
                    Masuk Admin
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-20 w-full max-w-[1280px] mx-auto px-4 md:px-8 pt-12 md:pt-20 flex-grow flex items-center justify-center">
        
        <div class="w-full rounded-[40px] p-[1.5px] bg-gradient-to-tl from-[#6a5750]/70 via-[#6a5750]/35 to-transparent shadow-[0_20px_50px_rgba(106,87,80,0.12)] mb-24">
            
            <div class="w-full bg-white rounded-[39px] grid grid-cols-1 lg:grid-cols-12 overflow-hidden min-h-[600px]">
                
                <div class="lg:col-span-5 bg-white p-8 md:p-12 flex flex-col justify-between items-start relative">
                    
                    <div class="w-full flex flex-col items-center text-center">
                        <h2 class="text-[34px] font-bold text-black tracking-tight mb-4">Contact Us!</h2>
                        <p class="text-xs text-gray-500 font-medium font-roboto leading-relaxed max-w-[340px] mb-8">
                            Punya pertanyaan atau saran? Hubungi kontak di bawah ini atau Anda dapat melakukan pengisian formulir yang telah disediakan.
                        </p>
                    </div>

                    <div class="flex flex-col gap-5 items-start text-left w-full max-w-[280px] mt-2 mb-auto pl-2 md:pl-4">
                        <div class="flex items-center gap-4 group w-full justify-start">
                            <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                <img src="image/email-icon.svg" alt="Email" class="w-full h-full object-contain" />
                            </div>
                            <a href="mailto:SIPPK@gmail.com" class="text-base font-bold text-black hover:text-[#6a5750] transition tracking-wide">
                                SIPPK@gmail.com
                            </a>
                        </div>

                        <div class="flex items-center gap-4 group w-full justify-start">
                            <div class="w-5 h-5 flex items-center justify-center flex-shrink-0">
                                <img src="image/whatsapp-icon.svg" alt="WhatsApp" class="w-full h-full object-contain" />
                            </div>
                            <a href="https://wa.me/6212312345678" target="_blank" class="text-base font-bold text-black hover:text-[#6a5750] transition tracking-wide">
                                +62 123-1234-5678
                            </a>
                        </div>
                    </div>

                    <div class="mt-12 lg:mt-6 flex justify-center w-full">
                        <img src="image/logocontact.svg" alt="Maskot SIPPK" class="w-[240px] h-[240px] md:w-[320px] md:h-[320px] object-contain transform hover:scale-105 transition duration-300" />
                    </div>
                </div>

                <div class="lg:col-span-7 bg-white p-8 md:p-12 flex flex-col justify-start pt-12 md:pt-14 relative">
                    
                    <form id="contactForm" action="" method="POST" autocomplete="off" class="relative z-10 w-full">
                        
                        <div class="w-full flex flex-col gap-1 mb-6">
                            <label class="text-xs font-medium text-gray-400 font-roboto">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" required placeholder="Ketik nama lengkap Anda" 
                                class="w-full h-[40px] border-b border-gray-200 text-sm focus:border-[#6a5750] outline-none transition pb-1 placeholder-gray-300 font-medium" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mb-6">
                            <div class="w-full flex flex-col gap-1">
                                <label class="text-xs font-medium text-gray-400 font-roboto">Email</label>
                                <input type="email" name="email" required placeholder="Ketik email Anda" 
                                    class="w-full h-[40px] border-b border-gray-200 text-sm focus:border-[#6a5750] outline-none transition pb-1 placeholder-gray-300 font-medium" />
                            </div>
                            <div class="w-full flex flex-col gap-1">
                                <label class="text-xs font-medium text-gray-400 font-roboto">Nomor Telepon</label>
                                <div class="flex items-center border-b border-gray-200 focus-within:border-[#6a5750] transition">
                                    <input type="text" id="phone" name="no_telp" required placeholder="08123456789" 
                                        class="w-full h-[40px] text-sm outline-none pb-1 placeholder-gray-300 font-medium" />
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex flex-col gap-2 mb-6">
                            <label class="text-xs font-medium text-gray-400 font-roboto">Subjek yang Ditanyakan?</label>
                            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-1">
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-500 cursor-pointer select-none">
                                    <input type="radio" name="subjek" value="Pengaduan" class="w-3.5 h-3.5 accent-[#6a5750]" required checked />
                                    <span>Pengaduan</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-500 cursor-pointer select-none">
                                    <input type="radio" name="subjek" value="Kunjungan" class="w-3.5 h-3.5 accent-[#6a5750]" />
                                    <span>Kunjungan</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-500 cursor-pointer select-none">
                                    <input type="radio" name="subjek" value="Dokumen" class="w-3.5 h-3.5 accent-[#6a5750]" />
                                    <span>Dokumen</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-medium text-gray-500 cursor-pointer select-none">
                                    <input type="radio" name="subjek" value="Lain-lain" class="w-3.5 h-3.5 accent-[#6a5750]" />
                                    <span>Lain-lain</span>
                                </label>
                            </div>
                        </div>

                        <div class="w-full flex flex-col gap-1 mb-8 relative">
                            <label class="text-xs font-medium text-gray-400 font-roboto">Pesan</label>
                            <textarea name="pesan" required placeholder="Ketik pesan Anda..." 
                                class="w-full h-[70px] border-b border-gray-200 text-sm focus:border-[#6a5750] outline-none transition pt-2 pb-1 resize-none placeholder-gray-300 font-medium"></textarea>
                            
                            <div class="absolute right-16 -bottom-60 pointer-events-none hidden md:block z-0">
                                <img src="image/letter_send.svg" alt="Ornamen Pesawat" class="w-[200px] h-auto object-contain opacity-90" />
                            </div>
                        </div>

                        <div class="w-full flex justify-end relative z-10">
                            <button type="submit" class="w-full max-w-[100px] h-[38px] bg-[#6a5750] hover:bg-[#574741] text-white font-medium text-sm rounded-lg shadow-sm transition transform active:scale-95">
                                Kirim
                            </button>
                        </div>

                    </form>
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

    <script>
        document.getElementById('phone').addEventListener('input', function() { 
            this.value = this.value.replace(/[^0-9]/g, ''); 
        });
    </script>
</body>
</html>
