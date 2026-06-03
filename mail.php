<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function kirimOTP($tujuanEmail, $nama, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->setFrom(
            $_ENV['MAIL_FROM_ADDRESS'],
            $_ENV['MAIL_FROM_NAME']
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