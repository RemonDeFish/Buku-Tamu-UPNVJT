<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function kirimOTP($tujuanEmail, $nama, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sippk.upnjatim@gmail.com';
        $mail->Password = 'AdminSIPPK@123';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom(
            'sippk.upnjatim@gmail.com',
            'SIPPK'
        );
        $mail->addAddress(
            $tujuanEmail,
            $nama
        );
        $mail->isHTML(true);
        $mail->Subject =
            'Kode OTP Login SIPPK';
        $mail->Body = "
            <h2>Kode OTP SIPPK</h2>

            <p>Halo {$nama},</p>

            <p>Kode OTP Anda adalah:</p>

            <h1>{$otp}</h1>

            <p>OTP berlaku selama 5 menit.</p>
        ";

        return $mail->send();

    } catch (Exception $e) {

        return false;
    }
}