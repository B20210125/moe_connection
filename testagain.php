<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // Configure PHPMailer to use SMTP
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Port = 2525;
    $mail->Username = '7832c81feddf91';
    $mail->Password = '99cabf98844d7b';

    // Set email parameters
    $mail->setFrom('sender@example.com', 'Admin');
    $mail->addAddress('recipient@example.com', 'Recipient Name');
    $mail->Subject = 'Testing HTML Email';

    // HTML message body
    $mail->isHTML(true);
    $mail->Body = '
        <html>
        <head>
            <title>Testing HTML Email</title>
        </head>
        <body>
            <h1>Title of the Email</h1>
            <img src="img/logo_2.png" alt="Image">
            <p>Body content of the email. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at enim dolor.</p>
        </body>
        </html>
    ';

    // Plain text version for non-HTML mail clients
    $mail->AltBody = 'Title of the Email

        Body content of the email. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at enim dolor.
    ';

    // Send the email
    if ($mail->send()) {
        echo 'Email sent successfully';
    } else {
        echo 'Error: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}



?>