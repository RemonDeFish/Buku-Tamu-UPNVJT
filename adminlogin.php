<?php
session_start();
require_once 'config.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    if (empty($recaptcha_response)) {
        $error_message = "Silakan centang verifikasi bahwa Anda bukan robot.";
    } else {
        $stmt = $conn->prepare("
            SELECT
                id,
                nama_lengkap,
                email,
                password
            FROM admins
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {

                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['nama_lengkap'];
                $_SESSION['admin_email'] = $admin['email'];

                $otp = str_pad(rand(0,9999), 4, '0', STR_PAD_LEFT);

                $expired = date(
                    'Y-m-d H:i:s',
                    strtotime('+5 minutes')
                );

                $otpStmt = $conn->prepare("
                    UPDATE admins
                    SET
                        otp_code = ?,
                        otp_expired = ?
                    WHERE id = ?
                ");

                if (!$otpStmt) {
                    die("Prepare gagal: " . $conn->error);
                }

                $otpStmt->bind_param(
                    "ssi",
                    $otp,
                    $expired,
                    $admin['id']
                );

                $otpStmt->execute();
                header("Location: adminotp.php");
                exit();
            } else {
                $error_message = "Password salah.";
            }
        } else {
            $error_message = "Email tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>SIPPK - Login Admin</title>
    
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
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            
            <img class="w-[130px] h-[123px] object-contain mb-4" src="image/Logo.png" alt="Logo SIPPK" />
            
            <h2 class="w-full max-w-[377px] text-center font-bold text-[#121212] text-xl lg:text-[22px] tracking-wide mb-6">
                Masuk Sebagai Admin Kampus
            </h2>

            <?php if (!empty($error_message)): ?>
                <div class="w-full max-w-[516px] bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl p-3 mb-4 text-center font-medium">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="" class="w-full max-w-[516px] flex flex-col items-center gap-5">
                
                <div class="w-full flex flex-col gap-1">
                    <label class="text-xs font-medium text-gray-400">Email</label>
                    <input type="email" name="email" required placeholder="email@gmail.com" 
                        class="w-full h-[45px] border-b border-gray-300 text-sm focus:border-[#6a5750] outline-none transition pb-1" />
                </div>

                <div class="w-full flex flex-col gap-1 relative">
                    <label class="text-xs font-medium text-gray-400">Password</label>
                    <input type="password" id="passwordField" name="password" required placeholder="Password" 
                        class="w-full h-[45px] border-b border-gray-300 text-sm focus:border-[#6a5750] outline-none transition pb-1 pr-8" />
                    
                    <button type="button" onclick="togglePassword()" class="absolute right-0 bottom-2 text-gray-400 hover:text-gray-600">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>

                <div class="w-full flex flex-col items-start mt-2">
                    <div id="captchaWrapper" class="w-[290px] h-[76px] bg-gray-50 rounded flex items-center justify-center overflow-hidden">
                        <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
                    </div>
                </div>

                <button type="submit" class="w-full h-[53px] bg-[#6a5750] hover:bg-[#574741] text-white font-medium text-base rounded-[12px] shadow transition mt-4">
                    Masuk
                </button>
            </form>

            <p class="text-[11px] text-gray-300 font-normal tracking-wide mt-8">
                © 2026 | SIPPK
            </p>

        </div>
    </main>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('passwordField');
            const eyeIcon = document.getElementById('eyeIcon');
            
            const eyeOpenPath = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
            const eyeClosedPath = `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`;

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = eyeOpenPath;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = eyeClosedPath;
            }
        }
    </script>
</body>
</html>
