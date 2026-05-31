<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

$error_message = "";

$stmt = $conn->prepare("
    SELECT
        otp_code,
        otp_expired
    FROM admins
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $_SESSION['admin_id']
);

$stmt->execute();

$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$debugOtp = $admin['otp_code'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['otp']) && is_array($_POST['otp'])) {

        $otp_code = implode("", $_POST['otp']);

        if (!preg_match('/^\d{4}$/', $otp_code)) {

            $error_message = "Kode OTP harus terdiri dari 4 digit angka.";

        } else {
            if (!isset($_SESSION['otp_attempts'])) {
                    $_SESSION['otp_attempts'] = 0;
                }

            if ($_SESSION['otp_attempts'] >= 5) {
                $error_message =
                    "Terlalu banyak percobaan OTP.";
            }
            else if (
                $otp_code === $admin['otp_code']
                &&
                strtotime($admin['otp_expired']) > time()
            ) {
                unset($_SESSION['otp_attempts']);
                $_SESSION['otp_verified'] = true;

                $clearOtp = $conn->prepare("
                    UPDATE admins
                    SET
                        otp_code = NULL,
                        otp_expired = NULL
                    WHERE id = ?
                ");

                $clearOtp->bind_param(
                    "i",
                    $_SESSION['admin_id']
                );

                $clearOtp->execute();

                header("Location: dashboard.php");
                exit();

            } else {

                $error_message = "Kode OTP salah atau sudah kedaluwarsa.";

            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Verifikasi OTP</title>
    
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
<body class="w-full min-h-screen bg-white font-montserrat antialiased relative flex justify-center items-center overflow-x-hidden p-4 md:p-8">

    <div class="absolute inset-0 z-0 opacity-[0.12]">
        <img class="w-full h-full object-cover" src="image/batik.png" alt="Batik Background" />
    </div>

    <main class="relative z-10 w-full max-w-[1299px] min-h-[650px] md:h-[700px] bg-white rounded-[24px] shadow-2xl flex flex-col md:flex-row overflow-hidden">
        
        <div class="hidden md:block w-100 max-w-[640px] flex-1 h-full relative overflow-hidden">
            <img class="w-full h-full object-cover p-3 rounded-[36px]" src="image/wpadmin.jpg.jpeg" alt="Selamat Datang Admin" />
            <div class="absolute inset-0 flex flex-col justify-center items-center text-center p-12">
                <h1 class="text-white text-3xl lg:text-4xl font-bold tracking-wide drop-shadow-md leading-snug">
                    Selamat Datang,<br/>Admin!
                </h1>
            </div>
        </div>

        <div class="w-full md:w-[640px] h-full flex flex-col items-center justify-center px-6 sm:px-12 lg:px-[62px] py-8 bg-white my-auto">
            
            <img class="w-[110px] h-[110px] object-contain mb-2" src="image/Logo.png" alt="Logo SIPPK" />
            
            <h2 class="w-full text-center font-bold text-[#121212] text-xl lg:text-[22px] tracking-wide mb-1">
                Verifikasi OTP
            </h2>
            <p class="text-xs text-blue-600 text-center">
                OTP Testing:
                <?php echo htmlspecialchars($debugOtp); ?>
            </p>
            <p class="text-xs text-gray-500 font-medium text-center mb-6">
                Kami telah mengirim kode OTP ke E-mail anda
            </p>

            <?php if (!empty($error_message)): ?>
                <div class="w-full max-w-[460px] bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl p-3 mb-2 text-center font-medium">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form id="otpForm" method="POST" action="" class="w-full max-w-[460px] flex flex-col items-center gap-6">
                
                <div class="flex items-center justify-center gap-3 w-full">
                    <input type="text" name="otp[]" maxlength="1" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="otp-input w-[65px] h-[50px] sm:w-[75px] sm:h-[55px] border border-gray-300 rounded-[12px] text-center font-semibold text-lg text-black focus:border-[#6a5750] focus:ring-1 focus:ring-[#6a5750] outline-none transition" required />
                    <span class="text-gray-400 font-medium text-lg">-</span>
                    <input type="text" name="otp[]" maxlength="1" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="otp-input w-[65px] h-[50px] sm:w-[75px] sm:h-[55px] border border-gray-300 rounded-[12px] text-center font-semibold text-lg text-black focus:border-[#6a5750] focus:ring-1 focus:ring-[#6a5750] outline-none transition" required />
                    <span class="text-gray-400 font-medium text-lg">-</span>
                    <input type="text" name="otp[]" maxlength="1" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="otp-input w-[65px] h-[50px] sm:w-[75px] sm:h-[55px] border border-gray-300 rounded-[12px] text-center font-semibold text-lg text-black focus:border-[#6a5750] focus:ring-1 focus:ring-[#6a5750] outline-none transition" required />
                    <span class="text-gray-400 font-medium text-lg">-</span>
                    <input type="text" name="otp[]" maxlength="1" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="otp-input w-[65px] h-[50px] sm:w-[75px] sm:h-[55px] border border-gray-300 rounded-[12px] text-center font-semibold text-lg text-black focus:border-[#6a5750] focus:ring-1 focus:ring-[#6a5750] outline-none transition" required />
                </div>

                <button type="submit" class="w-full h-[50px] bg-[#6a5750] hover:bg-[#574741] text-white font-medium text-base rounded-[12px] shadow transition mt-2">
                    Verifikasi
                </button>
            </form>

            <p class="text-[12px] text-gray-500 font-normal tracking-wide mt-6 text-center">
                Tidak mendapatkan code? <a href="#" class="font-semibold text-[#6a5750] hover:underline">Kirim Ulang OTP</a>
            </p>

            <p class="text-[11px] text-gray-300 font-normal tracking-wide mt-12">
                © 2026 | SIPPK
            </p>

        </div>
    </main>

    <script>
        const inputs = document.querySelectorAll('.otp-input');

        inputs.forEach((input, index) => {
            // Auto-focus ke kolom berikutnya setelah diisi angka
            input.addEventListener('input', (e) => {
                if (e.target.value.length >= 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // Mendukung hapus (Backspace) untuk otomatis mundur ke kotak sebelumnya
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>

</body>
</html>
