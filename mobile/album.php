<?php
require_once("includes/ini.php");
require_once("includes/login.php");

global $method;
global $headers;
global $body;

$user = $headers['User'];

/**
 * GET: listado de álbumes
 */
if ($method == 'GET' && !isset($_GET['id'])) {

    /**
     * 500: error de base de datos.
     */
    if (($albumes = albums($user)) === false) {
        http_response_code(500);
        exit;
    }

    echo toJson($albumes);
}
/**
 * GET: obtención de datos de un album
 */
elseif ($method == 'GET' && isset($_GET['id'])) {

    $id = $_GET['id'];

    /**
     * 404: album no encontrador.
     */
    if (($album = album($id)) === false) {
        http_response_code(404);
        exit;
    }

    echo toJson($album);
}
/**
 * POST: creación de nuevos álbumes
 */
elseif($method == 'POST') {

    /**
     * 400: faltan parámetros obligatorios
     */
    if (!isset($body['name'])) {
        http_response_code(400);
        exit;
    }

    $name = $body['name'];

    /**
     * 500: error de base de datos.
     */
    if (($album = album_in($name, $user)) === false) {
        http_response_code(500);
        exit;
    }

    echo toJson($album);
}
/**
 * PUT: edición de albumes ya creados
 */
elseif ($method == 'PUT') {

    /**
     * 400: faltan parámetros obligatorios
     */
    if (!isset($body['name']) || !isset($body['id'])) {
        http_response_code(400);
        exit;
    }

    $name = $body['name'];
    $id = $body['id'];

    /**
     * 404: el album no existe
     */
    if (!album($id)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario
     */
    if (!isYours('album', $id, $user)) {
        http_response_code(403);
        exit;
    }

    /**
     * 500: error de base de datos.
     */
    if (($album = album_up($id, $name)) === false) {
        http_response_code(500);
        exit;
    }

    echo toJson($album);
}
/**
 * DELETE: eliminación de albumes ya creados
 */
elseif ($method == 'DELETE') {

    /**
     * 400: faltan parámetros obligatorios.
     */
    if (!isset($_GET['id']) || !$_GET['id']) {
        http_response_code(400);
        exit;
    }

    $id = $_GET['id'];

    /**
     * 404: el album no existe
     */
    if (!album($id)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario
     */
    if (!isYours('album', $id, $user)) {
        http_response_code(403);
        exit;
    }

    /**
     * 500: error al elimnar el album
     */
    if (!album_de($id)) {
        http_response_code(500);
        exit;
    }
}
/**
 * 405: método no soportado.
 */
else {
    http_response_code(405);
    exit;
}