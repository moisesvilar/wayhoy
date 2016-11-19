<?php
require_once("includes/ini.php");

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
elseif($method == 'POST' && $body['isAdmin']) {
    $code = generar_clave(6, 'screen', 'code');
    $icc = generar_clave(6, 'screen', 'icc');
    $q = "
    INSERT INTO screen SET
    code = '$code',
    icc = '$icc',
    user = '$user',
    name = '$body[name]'
    ";
    if(!db_query($q)) {
        http_response_code(500);
        exit;
    }
    $screen = screen($code);
    echo toJson($screen);
    exit;
}
/**
 * Añadir un STB a la cuenta del usuario.
 */
elseif($method == 'POST' && !$body['isAdmin']) {
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
elseif($method == 'PUT'&& $body['isAdmin']) {
    $q = "
    UPDATE screen SET
    name = '$body[name]',
    licensed = '$body[licensed]',
    code = '$body[newcode]'
    WHERE code = '$body[code]'
    ";
    if(!db_query($q)) {
        http_response_code(500);
        exit;
    }
    $screen = screen($body['newcode']);
    echo toJson($screen);
    exit;
}
/**
 * Actualizar los datos de un STB
 */
elseif($method == 'PUT'&& !$body['isAdmin']) {
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
elseif ($method == 'DELETE') {

    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($_GET['code']) || !$_GET['code']) {
        http_response_code(400);
        exit;
    }

    $code = $_GET['code'];

    /**
     * 404: el album no existe
     */
    if (!screen($code)) {
        http_response_code(404);
        exit;
    }

    /**
     * 500: error al elimnar el album
     */
    if (!screen_de($code)) {
        http_response_code(500);
        exit;
    }
}
?>
