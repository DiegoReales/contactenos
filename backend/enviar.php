<?php 
// Permitimos unicamente peticiones con el metodo http POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $response = ['success' => false, 'message' => "Metodo HTTP no permitido"];
    http_response_code(405);
    echo json_encode($response);
    exit();
}

// Permitimos unicamente peticiones con ajax
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    http_response_code(403);
    echo "acceso restringido";
    exit();
}

define('DEBUG', false);
define('MAIL_HOST', 'smtp.mailtrap.io');
define('MAIL_USER', '8c97e1217ff34d');
define('MAIL_PASS', 'ed512ab7dd4b51');
define('MAIL_PORT', '25');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = utf8_decode($value);
    }

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
    $mail->setFrom($_POST['email'], $_POST['name']);
    $mail->addAddress('diego.reales@hotmail.com', 'Diego Reales');           // Add a recipient, Name is optional
    $mail->addReplyTo($_POST['email'], $_POST['name']);

    // Content
    $mail->isHTML(false);                                       // Set email format to HTML
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
