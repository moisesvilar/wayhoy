<?php
require_once("includes/ini.php");
global $method;
global $headers;
global $body;

if ($method == 'POST') {

    if (!isset($body['email'])) {
        http_response_code(400);
        exit;
    }

    $email = $body['email'];

    if (!user($email)) {
        http_response_code(404);
        exit;
    }

    $token = setRecoveryToken($email);

    if (!$token) {
        http_response_code(500);
        exit;
    }

    $url = "http://wayhoy.tv/wayhoy/recovery.php?t=$token";
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8' />
        <meta name='viewport' content='user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi' />
        <title>Promos TV - Recuperación de contraseña</title>
    </head>
    <body>
        <h1>Recuperación de contraseña</h1>
        <p>Se ha solicitado una recuperación de contraseña para esta dirección de correo electrónico</p>
        <p>Si usted no ha solicitado esta acción, simplemente ignore este correo.</p>
        <p>Visite la siguiente página para cambiar su contraseña:</p>
        <a href='$url'>$url</a>
    </body>
    </html>
    ";
    $conf = array();
    $conf['email'] = $email;
    $conf['subject'] = "Recuperación de contraseña";
    $conf['body'] = $html;
    if (($error = sendEmail($conf)) != "") {
        header("HTTP/1.0 500 $error");
        exit;
    }

}


?>