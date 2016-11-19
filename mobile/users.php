<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $contentType;
global $body;

$admin = $headers['User'];

/**
 * GET: listado de usuarios de un administrador.
 */
if ($method == 'GET' && !isset($_GET['id'])) {
    $users = users($admin);
    echo toJson($users);
}
/**
 * GET: obtención de datos de un usuario.
 */
elseif ($method == 'GET' && isset($_GET['id'])) {
    $email = $_GET['id'];
    $user = user($email);
    /**
     * 404: el usuario no existe
     */
    if (!$user) {
        http_response_code(404);
        exit;
    }
    echo toJson($user);
}
/**
 * Añadir un nuevo usuario
 */
elseif($method == 'POST') {
    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($body['email']) || !isset($body['password']) || !isset($body['name']) || !isset($body['nif'])) {
        http_response_code(400);
        exit;
    }

    $email = $body['email'];

    /**
     * 409: el usuario ya existe
     */
    if (user($email)) {
        http_response_code(409);
        exit;
    }

    $password = $body['password'];
    $name = $body['name'];
    $nif = $body['nif'];

    /**
     * 500: error al escibir en la base de datos.
     */
    if (!user_in(date('Ymdhis').generar_clave(2), $email, $password, $name, $nif, $admin)) {
        http_response_code(500);
        exit;
    }



    $user = user($email);
    echo toJson($user);
}
/**
 * Actualizar los datos de un usuario
 */
elseif($method == 'PUT') {
    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($body['email']) || !isset($body['name']) || !isset($body['nif'])) {
        http_response_code(400);
        exit;
    }

    $email = $body['email'];

    /**
     * 404: el usuario no existe
     */
    if (!user($email)) {
        http_response_code(404);
        exit;
    }

    $name = $body['name'];
    $nif = $body['nif'];

    /**
     * 500: error al actualizar en la base de datos.
     */
    if (!user_up($email, false, $name, $nif)) {
        http_response_code(500);
        exit;
    }

    if($body['permissions']) {
        update_permissions($email, $body['permissions']);
    }

    $user = user($email);
    echo toJson($user);
}
/**
 * DELETE: eliminación de usaurios
 */
elseif ($method == 'DELETE') {

    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($_GET['email']) || !$_GET['email']) {
        http_response_code(400);
        exit;
    }

    $email = $_GET['email'];

    /**
     * 404: el usuario no existe
     */
    if (!user($email)) {
        http_response_code(404);
        exit;
    }

    /**
     * 500: error al elimnar el album
     */
    if (!user_de($email)) {
        http_response_code(500);
        exit;
    }
}
