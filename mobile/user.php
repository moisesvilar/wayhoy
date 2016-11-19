<?php
require_once("includes/ini.php");

global $method;
global $headers;

/**
 * POST: inserción de nuevo usuario.
 */
if ($method == 'POST') {

    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($body['user']) || !isset($body['password']) | !isset($body['screen']) || !$body['user'] || !$body['password'] || !$body['screen']) {
        http_response_code(400);
        exit;
    }

    $user = $body['user'];
    $password = $body['password'];
    $screen = $body['screen'];
    $stb = screen($screen);


    /**
     * 404: el STB no existe
     */
    if (!$stb) {
        http_response_code(404);
        exit;
    }

    /**
     * 409: el usuario ya existe
     */
    if (user($user)) {
        http_response_code(409);
        exit;
    }

    /**
     * 409: el stb ya está asociado a otro usuario
     */
    $user_stb = user_screen(false, $screen);
    if ($user_stb) {
        http_response_code(409);
        exit;
    }

    $user_id_r = $stb['user_id_r'];

    /**
     * 500: error de base de datos.
     */
    if (!user_in($user_id_r, $user, $password)) {
        http_response_code(500);
        exit;
    }

    /**
     * 500: error de base de datos
     */
    if (!user_screen_in($screen, $user)) {
        http_response_code(500);
        exit;
    }
}
/**
 * PUT: modificación de usuario.
 */
elseif ($method == 'PUT') {

    print_r($headers);

    $user = user($headers['User']);
    if($user['password'] != $headers['Secret']) {
        http_response_code(403);
        exit;
    }

    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($body['password'])) {
        http_response_code(400);
        exit;
    }

    $user = $headers['User'];
    $password = $body['password'];

    /**
     * 404: el usuario no existe
     */
    if (!user($user)) {
        http_response_code(404);
        exit;
    }

    /**
     * 500: error de base de datos
     */
    if (!user_up($user, $password)) {
        http_response_code(500);
        exit;
    }
}
else {
    http_response_code(405);
    exit;
}




?>