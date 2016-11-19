<?php
require_once("includes/ini.php");
require_once("includes/login.php");

global $method;
global $headers;
global $contentType;
global $body;

$user = $headers['User'];

/**
 * GET: listado de STB.
 */
if ($method == 'GET' && !isset($_GET['code'])) {
    $screens = screens($user);
    echo toJson($screens);
}
/**
 * GET: obtención de datos de un STB.
 */
elseif ($method == 'GET' && isset($_GET['code'])) {
    $code = $_GET['code'];
    $screen = screen($code, false, $user);
    /**
     * 404: el STB no existe
     */
    if (!$screen) {
        http_response_code(404);
        exit;
    }
    echo toJson($screen);
}
/**
 * Añadir un STB a la cuenta del usuario.
 */
elseif($method == 'POST') {
    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($body['code']) || !isset($body['name'])) {
        http_response_code(400);
        exit;
    }

    $code = $body['code'];

    /**
     * 404: el STB no existe
     */
    if (!$user_r=screen($code)) {
        http_response_code(404);
        exit;
    }
	
    $name = $body['name'];

    /**
     * 500: error al escibir en la base de datos.
     */
    if (!user_screen_in($code, $user, $name)) {
        http_response_code(500);
        exit;
    }
	
    $screen = user_screen($user, $code);
    echo toJson($screen);
}
/**
 * Actualizar los datos de un STB
 */
elseif($method == 'PUT') {
    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($body['code']) || !isset($body['name'])) {
        http_response_code(400);
        exit;
    }

    $code = $body['code'];

    /**
     * 404: el STB no existe
     */
    if (!screen($code)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el STB no pertenece al usuario.
     */
    if (!isYours('user_screen', $code, $user, 'screen')) {
        http_response_code(403);
        exit;
    }

    $name = $body['name'];

    /**
     * 500: error al actualizar en la base de datos.
     */
    if (!user_screen_up($code, $name)) {
        http_response_code(500);
        exit;
    }

    $screen = user_screen($user, $code);
    echo toJson($screen);
}

?>
