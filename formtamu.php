<?php
require_once 'config.php';

$success_message = "";
$error_message = "";
// Bagian ini disiapkan untuk Backend PHP Anda untuk memproses form saat di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari atribut name masing-masing input
    $nama_tamu         = $_POST['nama_tamu'];
    $keperluan         = $_POST['keperluan'];
    $no_telp           = $_POST['no_telp'];
    $asal_instansi     = $_POST['asal_instansi'];
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'];
    $waktu_mulai       = $_POST['waktu_mulai'];
    $waktu_selesai     = $_POST['waktu_selesai'];
    $maksud_tujuan     = $_POST['maksud_tujuan'];

    // Di sini backend tinggal membuat query INSERT INTO ke MySQL...
    $stmt = $conn->prepare("
        INSERT INTO kunjungan
        (
            nama_pengunjung,
            no_telp,
            instansi,
            keperluan,
            tujuan,
            tanggal,
            waktu_mulai,
            waktu_selesai,
            status
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, 'menunggu'
        )
    ");

    $stmt->bind_param(
        "ssssssss",
        $nama_tamu,
        $no_telp,
        $asal_instansi,
        $keperluan,
        $maksud_tujuan,
        $tanggal_kunjungan,
        $waktu_mulai,
        $waktu_selesai
    );

    if ($stmt->execute()) {

        $id_kunjungan = $conn->insert_id;

        header("Location: tiket.php?id=".$id_kunjungan);
        exit();

    } else {

        $error_message =
            "Gagal menyimpan data kunjungan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Formulir Tamu</title>
    
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

    <div class="fixed inset-0 z-0 pointer-events-none">
        <img class="w-full h-full object-cover opacity-[0.12]" src="image/batik.png" alt="Batik Background" />
        <div class="absolute inset-0 bg-gradient-to-b from-white/20 via-white/50 to-white"></div>
    </div>

    <nav class="relative z-20 w-full h-[72px] bg-white shadow-sm flex justify-center items-center px-4 md:px-8">
        <div class="w-full max-w-[1400px] flex justify-between items-center">
            <div class="flex items-center gap-3.5">
                <img class="w-[39px] h-[37px] object-contain" src="image/Logo.png" alt="Logo" />
                <span class="font-bold text-black text-2xl tracking-[0.10px]">SIPPK</span>
            </div>
            <div class="flex items-center gap-6 md:gap-11">
                <div class="hidden md:flex items-center gap-11 font-normal text-black text-base">
                    <a href="index.php" class="hover:text-[#6a5750] transition">Home</a>
                    <a href="#" class="hover:text-[#6a5750] transition">Cek Status Kunjungan</a>
                    <a href="https://wa.me/6282261882303" class="hover:text-[#6a5750] transition">Contact Us?</a>
                </div>
                <a href="adminlogin.php" class="inline-block bg-[#6a5750] hover:bg-[#574741] text-white font-normal text-base px-5 py-2 rounded transition shadow-sm">
                    Masuk Admin
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-10 w-full max-w-[1200px] mx-auto px-4 sm:px-6 md:px-8 pt-10 pb-24 flex flex-col items-center flex-grow">
        
        <div class="w-full bg-white/95 rounded-[24px] shadow-2xl p-6 sm:p-10 md:p-12 relative border border-gray-100 backdrop-blur-xs">
            <h1 class="w-full text-center font-bold text-[#121212] text-xl md:text-2xl tracking-wide mb-8 border-b pb-4 border-gray-100">
                Form Tamu
            </h1>
            <?php if (!empty($success_message)): ?>
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            <form action="formtamu.php" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5 items-stretch">
                    
                    <div class="flex flex-col gap-5 justify-between">
                        <div class="form-group flex flex-col gap-1 relative">
                            <label class="text-xs font-semibold text-gray-400">Nama Tamu</label>
                            <input type="text" name="nama_tamu" placeholder="Nama Lengkap" required
                                   class="form-control w-full h-[48px] bg-gray-50/50 border border-gray-200 rounded-xl px-4 text-sm outline-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900" />
                        </div>

                        <div class="form-group flex flex-col gap-1 relative">
                            <label class="text-xs font-semibold text-gray-400">Keperluan</label>
                            <div class="relative">
                                <select name="keperluan" required
                                        class="form-control w-full h-[48px] bg-gray-50/50 border border-gray-200 rounded-xl px-4 text-sm outline-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900 appearance-none">
                                    <option value="" disabled selected class="text-gray-400">Pilih Keperluan</option>
                                    <option value="Mahasiswa">Mahasiswa</option>
                                    <option value="Kunjungan Perpustakaan">Kunjungan Perpustakaan</option>
                                    <option value="Janji Temu">Janji Temu</option>
                                    <option value="Kunjungan Dinas">Kunjungan Dinas</option>
                                    <option value="Menghadiri Acara">Menghadiri Acara</option>
                                    <option value="UPT Perpustakaan">UPT Perpustakaan</option>
                                    <option value="Tata Usaha">Tata Usaha</option>
                                    <option value="Humas (Hubungan Masyarakat)">Humas (Hubungan Masyarakat)</option>
                                    <option value="HIMA (Himpunan Mahasiswa)">HIMA (Himpunan Mahasiswa)</option>
                                </select>
                                <div class="absolute right-4 top-4 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="form-group flex flex-col gap-1 relative">
                            <label class="text-xs font-semibold text-gray-400">Nomor Telepon</label>
                            <input type="tel" id="phoneField" name="no_telp" placeholder="Contoh: 08123456789" required
                                   class="form-control w-full h-[48px] bg-gray-50/50 border border-gray-200 rounded-xl px-4 text-sm outline-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900" />
                        </div>

                        <div class="form-group flex flex-col gap-1 relative">
                            <label class="text-xs font-semibold text-gray-400">Asal atau Instansi</label>
                            <input type="text" name="asal_instansi" placeholder="contoh: UPN &quot;Veteran&quot; Jawa Timur" required
                                   class="form-control w-full h-[48px] bg-gray-50/50 border border-gray-200 rounded-xl px-4 text-sm outline-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-5 justify-between h-full">
                        <div class="form-group flex flex-col gap-1 relative">
                            <label class="text-xs font-semibold text-gray-400">Tanggal Kunjungan</label>
                            <input type="date" name="tanggal_kunjungan" required
                                   class="form-control w-full h-[48px] bg-gray-50/50 border border-gray-200 rounded-xl px-4 text-sm outline-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-group flex flex-col gap-1 relative">
                                <label class="text-xs font-semibold text-gray-400">Waktu Mulai</label>
                                <input type="time" id="startTime" name="waktu_mulai" required
                                       class="form-control w-full h-[48px] bg-gray-50/50 border border-gray-200 rounded-xl px-4 text-sm outline-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900" />
                            </div>

                            <div class="form-group flex flex-col gap-1 relative">
                                <label class="text-xs font-semibold text-gray-400">Waktu Selesai</label>
                                <input type="time" id="endTime" name="waktu_selesai" required
                                       class="form-control w-full h-[48px] bg-gray-50/50 border border-gray-200 rounded-xl px-4 text-sm outline-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900" />
                            </div>
                        </div>

                        <div class="form-group flex flex-col gap-1 relative flex-grow">
                            <label class="text-xs font-semibold text-gray-400">Maksud dan Tujuan</label>
                            <textarea name="maksud_tujuan" placeholder="Isi tujuan kunjungan anda" required
                                      class="form-control w-full h-full min-h-[116px] bg-gray-50/50 border border-gray-200 rounded-xl p-4 text-sm outline-none resize-none transition-all duration-200 focus:bg-blue-50/40 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900"></textarea>
                        </div>
                    </div>
                </div>

                <div class="w-full flex justify-center mt-8">
                    <button type="submit" class="w-full max-w-[180px] h-[48px] bg-[#6a5750] hover:bg-[#574741] text-white font-medium text-sm rounded-xl shadow-md transition duration-200">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="relative w-full bg-[#6a5750] mt-auto z-10 block shrink-0">
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 md:px-8 py-6 flex flex-col gap-4">
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
        const startTimeInput = document.getElementById('startTime');
        const endTimeInput = document.getElementById('endTime');
        const phoneField = document.getElementById('phoneField');

        // 1. Fitur Auto-Fill Waktu Selesai (+60 Menit Otomatis)
        startTimeInput.addEventListener('change', function() {
            if (this.value) {
                let [hours, minutes] = this.value.split(':').map(Number);
                
                // Tambahkan 1 jam (60 Menit) ke waktu mulai
                hours = (hours + 1) % 24;
                
                // Format kembali menjadi string HH:MM standar input tipe time
                const formattedHours = String(hours).padStart(2, '0');
                const formattedMinutes = String(minutes).padStart(2, '0');
                
                endTimeInput.value = `${formattedHours}:${formattedMinutes}`;
            }
        });

        // 2. Sanitasi Input Nomor HP (Hanya menerima angka)
        phoneField.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>