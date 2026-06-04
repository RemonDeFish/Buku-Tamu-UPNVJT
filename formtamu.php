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

    $cek = $conn->prepare("
        SELECT id
        FROM kunjungan
        WHERE tanggal = ?
        AND (
            waktu_mulai < ?
            AND waktu_selesai > ?
        )
    ");

    $cek->bind_param(
        "sss",
        $tanggal_kunjungan,
        $waktu_selesai,
        $waktu_mulai
    );

    $cek->execute();

    if (!$stmt) {
        die($conn->error);
    }

    if ($cek->get_result()->num_rows > 0) {

        $error_message =
            "Jadwal kunjungan bertabrakan dengan jadwal lain.";

    } else if ($stmt->execute()) {

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
    <title>SIPPK - Form Tamu</title>
    
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
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        .custom-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
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
                    <a href="statuskunjungan.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Status Kunjungan</a>
                    <a href="contact.php" class="hover:text-[#6a5750] hover:font-semibold hover:border-b-4 hover:border-[#6a5750] hover:rounded-sm transition">Contact Us?</a>
                </div>
                <a href="adminlogin.php" class="inline-block bg-[#6a5750] hover:bg-[#574741] text-white font-normal text-base px-5 py-2 rounded transition shadow-sm">
                    Masuk Admin
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-20 w-full max-w-[1240px] mx-auto px-4 md:px-8 pt-10 md:pt-14 flex-grow flex items-center justify-center">
        
        <div class="w-full rounded-[28px] shadow-2xl mb-20 bg-white">
            
            <div class="w-full rounded-[27px] p-6 md:p-12 relative">
                
                <div class="w-full flex flex-col items-center mb-10 relative px-2 md:px-6">
                    <div class="text-center pb-3 translate-y-[1px] z-10">
                        <h2 class="text-3xl font-bold text-gray-800 tracking-wide px-6">Form Tamu</h2>
                    </div>
                    <div class="w-48 h-[3px] bg-[#6a5750] rounded-full"></div>
                    <div class="w-full h-[1px] bg-gray-200/90 z-0"></div>
                </div>

                <form id="guestForm" action="formtamu.php" method="POST" autocomplete="off" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-5 items-stretch md:px-4">
                        
                        <div class="flex flex-col gap-5">
                            <div id="container_nama" class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 focus-within:bg-white border border-slate-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 rounded-xl px-4 py-2 flex flex-col justify-center min-h-[64px] transition group">
                                <label class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider group-focus-within:text-blue-500 transition-colors">Nama Tamu</label>
                                <input type="text" id="nama_tamu" name="nama_tamu" placeholder="Nama Lengkap" value="<?php echo isset($nama_tamu) ? htmlspecialchars($nama_tamu) : ''; ?>" required class="w-full bg-transparent text-sm font-medium text-gray-800 outline-none mt-0.5 placeholder-gray-300" />
                            </div>

                            <div class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 border border-slate-200 rounded-xl px-4 py-2 flex flex-col justify-center min-h-[64px] transition relative cursor-pointer select-none group" id="dropdownTrigger">
                                <label id="dropdownLabel" class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider cursor-pointer transition-colors duration-200">Keperluan</label>
                                <div class="flex items-center justify-between mt-0.5">
                                    <span id="dropdownSelectedText" class="text-sm font-medium <?php echo isset($keperluan) && !empty($keperluan) ? 'text-gray-800' : 'text-gray-300'; ?>">
                                        <?php echo isset($keperluan) && !empty($keperluan) ? htmlspecialchars($keperluan) : 'Select Keperluan'; ?>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" id="dropdownIcon" class="w-3.5 h-3.5 text-gray-400 transition-colors"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                </div>
                                <input type="hidden" name="keperluan" id="keperluanValue" value="<?php echo isset($keperluan) ? htmlspecialchars($keperluan) : ''; ?>" required>

                                <div id="dropdownMenu" class="absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl p-3 z-50 hidden cursor-default">
                                    <div class="relative flex items-center mb-2">
                                        <span class="absolute left-3 text-slate-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" /></svg>
                                        </span>
                                        <input type="text" id="dropdownSearch" placeholder="Search..." class="w-full h-[38px] pl-9 pr-4 bg-white border border-slate-200 rounded-lg text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" />
                                    </div>
                                    <ul id="dropdownList" class="max-h-[180px] overflow-y-auto custom-scroll flex flex-col gap-0.5">
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">Mahasiswa</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">Kunjungan Perpustakaan</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">Janji Temu</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">Kunjungan Dinas</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">Menghadiri Acara</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">UPT Perpustakaan</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">Tata Usaha</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">Humas (Hubungan Masyarakat)</li>
                                        <li class="item-opt px-3 py-2 text-[13px] text-slate-700 hover:bg-slate-50 rounded-lg cursor-pointer font-medium transition">HIMA (Himpunan Mahasiswa)</li>
                                    </ul>
                                </div>
                            </div>

                            <div id="container_email" class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 focus-within:bg-white border border-slate-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 rounded-xl px-4 py-2 flex flex-col justify-center min-h-[64px] transition group">
                                <label class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider group-focus-within:text-blue-500 transition-colors">E-mail Tamu</label>
                                <input type="email" id="email_tamu" name="email_tamu" placeholder="Contoh: 24081111110@student.upnjatim.ac.id" value="<?php echo isset($email_tamu) ? htmlspecialchars($email_tamu) : ''; ?>" required class="w-full bg-transparent text-sm font-medium text-gray-800 outline-none mt-0.5 placeholder-gray-300" />
                            </div>

                            <div id="container_phone" class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 focus-within:bg-white border border-slate-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 rounded-xl px-4 py-2 flex flex-col justify-center min-h-[64px] transition group">
                                <label class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider group-focus-within:text-blue-500 transition-colors">Nomor Telepon</label>
                                <input type="text" id="phone" name="no_telp" placeholder="08123456789" value="<?php echo isset($no_telp) ? htmlspecialchars($no_telp) : ''; ?>" required class="w-full bg-transparent text-sm font-medium text-gray-800 outline-none mt-0.5 placeholder-gray-300" />
                            </div>

                            <div id="container_asal" class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 focus-within:bg-white border border-slate-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 rounded-xl px-4 py-2 flex flex-col justify-center min-h-[64px] transition group">
                                <label class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider group-focus-within:text-blue-500 transition-colors">Asal atau Instansi</label>
                                <input type="text" id="asal_instansi" name="asal_instansi" placeholder="Contoh: UPN &quot;Veteran&quot; Jawa Timur" value="<?php echo isset($asal_instansi) ? htmlspecialchars($asal_instansi) : ''; ?>" required class="w-full bg-transparent text-sm font-medium text-gray-800 outline-none mt-0.5 placeholder-gray-300" />
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-5 justify-between">
                            
                            <div class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 border border-slate-200 rounded-xl px-4 py-2 flex items-center gap-3.5 min-h-[64px] relative transition cursor-pointer select-none group" id="datePickerTrigger">
                                <span id="dateIcon" class="text-gray-400 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                </span>
                                <div class="flex flex-col flex-grow">
                                    <label id="dateLabel" class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider cursor-pointer transition-colors duration-200">Tanggal Kunjungan</label>
                                    <span id="dateDisplay" class="text-sm font-medium text-gray-300 mt-0.5">Select Tanggal</span>
                                </div>
                                <input type="hidden" name="tanggal_kunjungan" id="tanggalKunjunganValue" value="" required>

                                <div id="calendarModal" class="absolute left-0 top-full mt-2 w-[320px] bg-white border border-slate-200 rounded-2xl shadow-2xl p-4 z-50 hidden cursor-default">
                                    <div class="flex justify-between items-center mb-4 px-1">
                                        <div class="flex items-center gap-1">
                                            <span id="calendarMonthYearLabel" class="font-bold text-gray-800 text-sm">Mei 2026</span>
                                        </div>
                                        <div class="flex gap-4 text-gray-400">
                                            <button type="button" id="prevMonthBtn" class="hover:text-gray-700 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.75-7.5" /></svg></button>
                                            <button type="button" id="nextMonthBtn" class="hover:text-gray-700 transition"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg></button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-7 text-center text-xs font-medium text-gray-400 mb-2">
                                        <div>S</div><div>M</div><div>T</div><div>W</div><div>T</div><div>F</div><div>S</div>
                                    </div>
                                    <div class="grid grid-cols-7 text-center gap-y-1 text-xs font-semibold text-gray-700" id="calendarDaysGrid">
                                    </div>
                                </div>
                            </div>

                            <div class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 border border-slate-200 rounded-xl px-4 py-2 flex items-center gap-3.5 min-h-[64px] relative transition cursor-pointer select-none group" id="timeRangeTrigger">
                                <span id="timeIcon" class="text-gray-400 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </span>
                                <div class="flex flex-col flex-grow">
                                    <label id="timeLabel" class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider cursor-pointer transition-colors duration-200">Estimasi Waktu Berkunjung</label>
                                    <span id="timeRangeDisplay" class="text-sm font-medium text-gray-300 mt-0.5">Select Rentang Waktu</span>
                                </div>
                                
                                <input type="hidden" name="waktu_mulai" id="waktuMulaiValue" value="" required>
                                <input type="hidden" name="waktu_selesai" id="waktuSelesaiValue" value="" required>

                                <div id="timePickerModal" class="absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl p-4 z-50 hidden cursor-default">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">Estimasi Waktu Berkunjung</h4>
                                            <p class="text-[10px] text-gray-400 mt-0.5">Pilih rentang waktu yang Anda inginkan agar kami dapat menjadwalkan kunjungan terbaik.</p>
                                        </div>
                                        <button type="button" id="closeTimeModal" class="text-gray-300 hover:text-gray-500 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-3 my-2">
                                        <div class="relative flex-1">
                                            <div id="startSelectBox" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs font-semibold text-gray-700 flex justify-between items-center cursor-pointer hover:bg-slate-100 transition">
                                                <span id="startSelectText">00:00</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                            </div>
                                            <ul id="startContainerList" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-[140px] overflow-y-auto custom-scroll hidden z-50"></ul>
                                        </div>

                                        <div class="text-gray-400 flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                        </div>

                                        <div class="relative flex-1">
                                            <div id="endSelectBox" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs font-semibold text-gray-700 flex justify-between items-center cursor-pointer hover:bg-slate-100 transition">
                                                <span id="endSelectText">00:00</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                            </div>
                                            <ul id="endContainerList" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-[140px] overflow-y-auto custom-scroll hidden z-50"></ul>
                                        </div>
                                    </div>

                                    <div class="flex justify-end mt-3">
                                        <button type="button" id="btnApplyTime" class="bg-[#6a5750] hover:bg-[#574741] text-white text-xs font-semibold px-5 py-2 rounded-lg transition shadow-sm">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="container_maksud" class="input-container w-full bg-[#f1f5f9]/60 hover:bg-[#f1f5f9]/90 focus-within:bg-white border border-slate-200 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 rounded-xl px-4 py-3 flex flex-col flex-grow min-h-[148px] transition group">
                                <label class="input-label text-[11px] font-semibold text-gray-400 uppercase tracking-wider group-focus-within:text-blue-500 transition-colors">Maksud dan Tujuan</label>
                                <textarea id="maksud_tujuan" name="maksud_tujuan" placeholder="Isi tujuan kunjungan anda" required class="w-full h-full bg-transparent text-sm font-medium text-gray-800 outline-none mt-1 resize-none placeholder-gray-300"><?php echo isset($maksud_tujuan) ? htmlspecialchars($maksud_tujuan) : ''; ?></textarea>
                            </div>

                            <div class="flex flex-col items-start mt-2">
                                <div id="recaptcha_container" class="p-1.5 rounded-lg border border-transparent transition-all duration-200 inline-block flex flex-col items-start bg-transparent">
                                    <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" data-callback="recaptchaCallback" data-expired-callback="recaptchaExpiredCallback"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div id="globalErrorMessage" class="w-full text-center mt-6 text-sm font-semibold text-red-500 hidden"></div>

                    <div class="w-full flex justify-center mt-6">
                        <button type="submit" class="w-full max-w-[150px] h-[46px] bg-[#6a5750] hover:bg-[#574741] text-white font-medium text-sm rounded-xl shadow-md transition transform active:scale-95">
                            Simpan Data
                        </button>
                    </div>
                </form>
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
        let isRecaptchaVerified = false;

        function recaptchaCallback() {
            isRecaptchaVerified = true; 
            const container = document.getElementById('recaptcha_container');
            container.classList.remove('border-red-400', 'bg-red-50/30');
            container.classList.add('border-transparent');
        }

        function recaptchaExpiredCallback() {
            isRecaptchaVerified = false;
        }

        function setElementFocus(triggerEl, labelEl, iconEl = null, activate = true) {
            if (triggerEl.classList.contains('border-red-400')) return;

            if (activate) {
                triggerEl.classList.remove('border-slate-200');
                triggerEl.classList.add('border-blue-500', 'ring-1', 'ring-blue-500', 'bg-white');
                if (labelEl) { labelEl.classList.remove('text-gray-400'); labelEl.classList.add('text-blue-500'); }
                if (iconEl) { iconEl.classList.remove('text-gray-400'); iconEl.classList.add('text-blue-500'); }
            } else {
                triggerEl.classList.remove('border-blue-500', 'ring-1', 'ring-blue-500', 'bg-white');
                triggerEl.classList.add('border-slate-200');
                if (labelEl) { labelEl.classList.remove('text-blue-500'); labelEl.classList.add('text-gray-400'); }
                if (iconEl) { iconEl.classList.remove('text-blue-500'); iconEl.classList.add('text-gray-400'); }
            }
        }

        function markAsWarning(containerEl) {
            containerEl.classList.remove('border-slate-200', 'bg-white', 'border-blue-500', 'ring-1', 'ring-blue-500');
            containerEl.classList.add('border-red-400', 'bg-red-50/30');
        
            const label = containerEl.querySelector('.input-label');
            if (label) { label.classList.remove('text-gray-400', 'text-blue-500'); label.classList.add('text-red-500'); }
            
            const svgIcon = containerEl.querySelector('svg');
            if (svgIcon) { svgIcon.classList.remove('text-gray-400', 'text-blue-500'); svgIcon.classList.add('text-red-500'); }
        }

        function removeWarning(containerEl) {
            if (containerEl.classList.contains('border-red-400')) {
                containerEl.classList.remove('border-red-400', 'bg-red-50/30');
                containerEl.classList.add('border-slate-200');
                
                const label = containerEl.querySelector('.input-label');
                if (label) { label.classList.remove('text-red-500'); label.classList.add('text-gray-400'); }
                
                const svgIcon = containerEl.querySelector('svg');
                if (svgIcon) { svgIcon.classList.remove('text-red-500'); svgIcon.classList.add('text-gray-400'); }
            }
        }

        const form = document.getElementById('guestForm');

        form.addEventListener('invalid', function(e) {
            e.preventDefault(); 
        }, true);

        form.addEventListener('input', function(e) {
            const container = e.target.closest('.input-container');
            if (container && e.target.value.trim() !== "") {
                removeWarning(container);
            }
        });

        const dTrigger = document.getElementById('dropdownTrigger');
        const dMenu = document.getElementById('dropdownMenu');
        const dSearch = document.getElementById('dropdownSearch');
        const dItems = document.querySelectorAll('.item-opt');
        const hiddenKeperluan = document.getElementById('keperluanValue');
        const textDisplay = document.getElementById('dropdownSelectedText');
        const dLabel = document.getElementById('dropdownLabel');
        const dIcon = document.getElementById('dropdownIcon');

        dTrigger.addEventListener('click', (e) => {
            if(e.target.closest('#dropdownMenu')) return;
            const isHidden = dMenu.classList.contains('hidden');
            
            calendarModal.classList.add('hidden'); setElementFocus(dateTrigger, dateLabel, dateIcon, false);
            tModal.classList.add('hidden'); setElementFocus(tTrigger, timeLabel, timeIcon, false);

            if(isHidden) {
                removeWarning(dTrigger);
                dMenu.classList.remove('hidden');
                dSearch.focus();
                setElementFocus(dTrigger, dLabel, dIcon, true);
            } else {
                dMenu.classList.add('hidden');
                setElementFocus(dTrigger, dLabel, dIcon, false);
            }
        });

        dSearch.addEventListener('input', function() {
            const f = this.value.toLowerCase();
            dItems.forEach(item => { item.style.display = item.textContent.toLowerCase().includes(f) ? '' : 'none'; });
        });

        dItems.forEach(item => {
            item.addEventListener('click', function() {
                textDisplay.textContent = this.textContent;
                textDisplay.className = "text-sm font-medium text-gray-800 mt-0.5";
                hiddenKeperluan.value = this.textContent;
                dMenu.classList.add('hidden');
                setElementFocus(dTrigger, dLabel, dIcon, false);
                removeWarning(dTrigger);
            });
        });

        let currentYear = 2026;
        let currentMonth = 4; 
        let selectedDay = null; 
        const monthsNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        
        const dateTrigger = document.getElementById('datePickerTrigger');
        const calendarModal = document.getElementById('calendarModal');
        const dateDisplay = document.getElementById('dateDisplay');
        const hiddenDateInput = document.getElementById('tanggalKunjunganValue');
        const monthYearLabel = document.getElementById('calendarMonthYearLabel');
        const daysGrid = document.getElementById('calendarDaysGrid');
        const dateLabel = document.getElementById('dateLabel');
        const dateIcon = document.getElementById('dateIcon');

        function renderCalendar() {
            monthYearLabel.textContent = `${monthsNames[currentMonth]} ${currentYear}`;
            daysGrid.innerHTML = '';
            let firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
            let totalDays = new Date(currentYear, currentMonth + 1, 0).getDate();

            for (let i = 0; i < firstDayIndex; i++) { daysGrid.appendChild(document.createElement('div')); }

            for (let day = 1; day <= totalDays; day++) {
                const dayCell = document.createElement('div');
                dayCell.textContent = day;
                dayCell.className = "p-2 cursor-pointer hover:bg-slate-100 rounded-full transition font-semibold text-gray-700";

                if(selectedDay && day === selectedDay && currentMonth === 4 && currentYear === 2026) {
                    dayCell.className = "p-2 bg-[#6a5750] text-white rounded-full cursor-pointer font-bold";
                }

                dayCell.addEventListener('click', () => {
                    selectedDay = day;
                    const formattedDay = String(day).padStart(2, '0');
                    const formattedMonth = String(currentMonth + 1).padStart(2, '0');
                    
                    dateDisplay.textContent = `${formattedDay}/${formattedMonth}/${currentYear}`;
                    dateDisplay.className = "text-sm font-medium text-gray-800 mt-0.5";
                    hiddenDateInput.value = `${currentYear}-${formattedMonth}-${formattedDay}`;
                    
                    calendarModal.classList.add('hidden');
                    setElementFocus(dateTrigger, dateLabel, dateIcon, false);
                    removeWarning(dateTrigger);
                    renderCalendar();
                });
                daysGrid.appendChild(dayCell);
            }
        }

        dateTrigger.addEventListener('click', (e) => {
            if(e.target.closest('#calendarModal')) return;
            const isHidden = calendarModal.classList.contains('hidden');

            dMenu.classList.add('hidden'); setElementFocus(dTrigger, dLabel, dIcon, false);
            tModal.classList.add('hidden'); setElementFocus(tTrigger, timeLabel, timeIcon, false);

            if(isHidden) {
                removeWarning(dateTrigger);
                calendarModal.classList.remove('hidden');
                setElementFocus(dateTrigger, dateLabel, dateIcon, true);
            } else {
                calendarModal.classList.add('hidden');
                setElementFocus(dateTrigger, dateLabel, dateIcon, false);
            }
        });

        document.getElementById('prevMonthBtn').addEventListener('click', () => { currentMonth--; if(currentMonth < 0) { currentMonth = 11; currentYear--; } renderCalendar(); });
        document.getElementById('nextMonthBtn').addEventListener('click', () => { currentMonth++; if(currentMonth > 11) { currentMonth = 0; currentYear++; } renderCalendar(); });
        renderCalendar();

        const tTrigger = document.getElementById('timeRangeTrigger');
        const tModal = document.getElementById('timePickerModal');
        const tClose = document.getElementById('closeTimeModal');
        const btnApply = document.getElementById('btnApplyTime');
        const displayTime = document.getElementById('timeRangeDisplay');
        const timeLabel = document.getElementById('timeLabel');
        const timeIcon = document.getElementById('timeIcon');

        const startSelectBox = document.getElementById('startSelectBox');
        const startSelectText = document.getElementById('startSelectText');
        const startContainerList = document.getElementById('startContainerList');

        const endSelectBox = document.getElementById('endSelectBox');
        const endSelectText = document.getElementById('endSelectText');
        const endContainerList = document.getElementById('endContainerList');

        let activeStartTime24h = "";
        let activeEndTime24h = "";

        function generateTimeOptions() {
            const options = [];
            for (let i = 0; i < 24; i++) {
                let hour = i % 12;
                if (hour === 0) hour = 12;
                const ampm = i < 12 ? "AM" : "PM";
                const strHour = String(hour).padStart(2, '0');
                const strHour24 = String(i).padStart(2, '0');
                
                options.push({ text: `${strHour}:00 ${ampm}`, val24: `${strHour24}:00` });
                options.push({ text: `${strHour}:30 ${ampm}`, val24: `${strHour24}:30` });
            }
            return options;
        }

        const timeOptionsData = generateTimeOptions();

        function buildTimeLists() {
            startContainerList.innerHTML = '';
            endContainerList.innerHTML = '';

            timeOptionsData.forEach(opt => {
                const liStart = document.createElement('li');
                liStart.textContent = opt.text;
                liStart.className = "px-4 py-2 text-xs text-slate-700 font-semibold hover:bg-[#6a5750]/10 hover:text-[#6a5750] cursor-pointer transition flex items-center";
                liStart.addEventListener('click', (e) => {
                    e.stopPropagation();
                    startSelectText.textContent = opt.text;
                    activeStartTime24h = opt.val24;
                    startContainerList.classList.add('hidden');
                });
                startContainerList.appendChild(liStart);

                const liEnd = document.createElement('li');
                liEnd.textContent = opt.text;
                liEnd.className = "px-4 py-2 text-xs text-slate-700 font-semibold hover:bg-[#6a5750]/10 hover:text-[#6a5750] cursor-pointer transition flex items-center";
                liEnd.addEventListener('click', (e) => {
                    e.stopPropagation();
                    endSelectText.textContent = opt.text;
                    activeEndTime24h = opt.val24;
                    endContainerList.classList.add('hidden');
                });
                endContainerList.appendChild(liEnd);
            });
        }

        buildTimeLists();

        startSelectBox.addEventListener('click', (e) => {
            e.stopPropagation();
            endContainerList.classList.add('hidden');
            startContainerList.classList.toggle('hidden');
        });

        endSelectBox.addEventListener('click', (e) => {
            e.stopPropagation();
            startContainerList.classList.add('hidden');
            endContainerList.classList.toggle('hidden');
        });

        tTrigger.addEventListener('click', (e) => {
            if(e.target.closest('#timePickerModal')) return;
            const isHidden = tModal.classList.contains('hidden');

            dMenu.classList.add('hidden'); setElementFocus(dTrigger, dLabel, dIcon, false);
            calendarModal.classList.add('hidden'); setElementFocus(dateTrigger, dateLabel, dateIcon, false);

            if(isHidden) {
                removeWarning(tTrigger);
                tModal.classList.remove('hidden');
                setElementFocus(tTrigger, timeLabel, timeIcon, true);
            } else {
                tModal.classList.add('hidden');
                setElementFocus(tTrigger, timeLabel, timeIcon, false);
                startContainerList.classList.add('hidden');
                endContainerList.classList.add('hidden');
            }
        });

        tClose.addEventListener('click', () => { 
            tModal.classList.add('hidden'); 
            setElementFocus(tTrigger, timeLabel, timeIcon, false);
            startContainerList.classList.add('hidden');
            endContainerList.classList.add('hidden');
        });

        btnApply.addEventListener('click', () => {
            if(!activeStartTime24h) activeStartTime24h = "00:00";
            if(!activeEndTime24h) activeEndTime24h = "00:00";
            
            displayTime.textContent = `${startSelectText.textContent} - ${endSelectText.textContent}`;
            displayTime.className = "text-sm font-medium text-gray-800 mt-0.5";

            document.getElementById('waktuMulaiValue').value = activeStartTime24h;
            document.getElementById('waktuSelesaiValue').value = activeEndTime24h;
            
            tModal.classList.add('hidden');
            setElementFocus(tTrigger, timeLabel, timeIcon, false);
            removeWarning(tTrigger);
        });

        document.addEventListener('click', (e) => {
            if (!dTrigger.contains(e.target)) { dMenu.classList.add('hidden'); setElementFocus(dTrigger, dLabel, dIcon, false); }
            if (!dateTrigger.contains(e.target)) { calendarModal.classList.add('hidden'); setElementFocus(dateTrigger, dateLabel, dateIcon, false); }
            if (!tTrigger.contains(e.target)) { 
                tModal.classList.add('hidden'); 
                setElementFocus(tTrigger, timeLabel, timeIcon, false); 
                startContainerList.classList.add('hidden');
                endContainerList.classList.add('hidden');
            }
        });

        document.getElementById('phone').addEventListener('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); });

        form.addEventListener('submit', function(e) {
            const globalError = document.getElementById('globalErrorMessage');
            
            document.querySelectorAll('.input-container').forEach(c => removeWarning(c));
            const recaptchaContainer = document.getElementById('recaptcha_container');
            recaptchaContainer.classList.remove('border-red-400', 'bg-red-50/30');

            const namaInput = document.getElementById('nama_tamu');
            if (!namaInput.value.trim()) {
                e.preventDefault();
                markAsWarning(document.getElementById('container_nama'));
                globalError.textContent = "⚠️ Mohon isi Nama Tamu terlebih dahulu!";
                globalError.classList.remove('hidden');
                namaInput.focus();
                return;
            }

            if (!hiddenKeperluan.value) {
                e.preventDefault();
                markAsWarning(dTrigger);
                globalError.textContent = "⚠️ Mohon pilih Keperluan kunjungan Anda terlebih dahulu!";
                globalError.classList.remove('hidden');
                return;
            }

            const emailInput = document.getElementById('email_tamu');
            if (!emailInput.value.trim()) {
                e.preventDefault();
                markAsWarning(document.getElementById('container_email'));
                globalError.textContent = "⚠️ Mohon isi E-mail Tamu terlebih dahulu!";
                globalError.classList.remove('hidden');
                emailInput.focus();
                return;
            }

            const phoneInput = document.getElementById('phone');
            if (!phoneInput.value.trim()) {
                e.preventDefault();
                markAsWarning(document.getElementById('container_phone'));
                globalError.textContent = "⚠️ Mohon isi Nomor Telepon terlebih dahulu!";
                globalError.classList.remove('hidden');
                phoneInput.focus();
                return;
            }

            const asalInput = document.getElementById('asal_instansi');
            if (!asalInput.value.trim()) {
                e.preventDefault();
                markAsWarning(document.getElementById('container_asal'));
                globalError.textContent = "⚠️ Mohon isi Asal atau Instansi Anda terlebih dahulu!";
                globalError.classList.remove('hidden');
                asalInput.focus();
                return;
            }

            if (!hiddenDateInput.value) {
                e.preventDefault();
                markAsWarning(dateTrigger);
                globalError.textContent = "⚠️ Mohon pilih Tanggal Kunjungan Anda terlebih dahulu!";
                globalError.classList.remove('hidden');
                return;
            }

            if (!document.getElementById('waktuMulaiValue').value) {
                e.preventDefault();
                markAsWarning(tTrigger);
                globalError.textContent = "⚠️ Mohon tentukan Estimasi Waktu Berkunjung Anda terlebih dahulu!";
                globalError.classList.remove('hidden');
                return;
            }

            const maksudInput = document.getElementById('maksud_tujuan');
            if (!maksudInput.value.trim()) {
                e.preventDefault();
                markAsWarning(document.getElementById('container_maksud'));
                globalError.textContent = "⚠️ Mohon isi Maksud dan Tujuan kunjungan Anda terlebih dahulu!";
                globalError.classList.remove('hidden');
                maksudInput.focus();
                return;
            }

            if (!isRecaptchaVerified) {
                e.preventDefault();
                recaptchaContainer.classList.add('border-red-400', 'bg-red-50/30');
                globalError.textContent = "⚠️ Silakan centang reCAPTCHA terlebih dahulu untuk memverifikasi keamanan!";
                globalError.classList.remove('hidden');
                return;
            }

            globalError.classList.add('hidden');
        });
    </script>
</body>
</html>
