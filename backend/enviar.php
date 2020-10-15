<?php 
// Permitimos unicamente peticiones con el metodo http POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response = ['success' => false, 'message' => "Metodo HTTP no permitido"];
    http_response_code(405);
    echo json_encode($response);
    exit();
}

// Permitimos unicamente peticiones con ajax
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    http_response_code(405);
    echo "acceso restringido";
    exit();
}

define('DEBUG', false);
define('MAIL_HOST', 'smtp.mailtrap.io');
define('MAIL_USER', 'c5b44a87c035e8');
define('MAIL_PASS', 'a6517fd8213436');
define('MAIL_PORT', '25');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Parametros de Servidor SMTP
    if (DEBUG) $mail->SMTPDebug = SMTP::DEBUG_SERVER;           // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = MAIL_HOST;                              // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = MAIL_USER;                              // SMTP username
    $mail->Password   = MAIL_PASS;                              // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = MAIL_PORT;                              // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('contacto@example.com', 'Pagina Web');
    $mail->addAddress('joe@example.net', 'Joe User');           // Add a recipient, Name is optional
    $mail->addReplyTo($_POST['email'], $_POST['name']);

    // Content
    $mail->isHTML(false);                                  // Set email format to HTML
    $mail->Subject = "Nuevo Mensaje - {$_POST['subject']}";
    $mail->Body    = $_POST['body'];
    $mail->AltBody = $_POST['body'];

    $mail->send();
    $response = [
        'success' => true,
        'message' => "Mensaje enviado con exito!"
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Ha ocurrido un error, Fallo envio de mensaje!. Mailer Error: {$mail->ErrorInfo}"
    ];
}

echo json_encode($response);
