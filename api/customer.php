<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $body;

/**
 * Alta de clientes: POST
 */
if ($method == 'POST') {

    /**
     * Error 400: el cliente no proporciona los parámetros adecuados.
     */
    if (!isset($body['idCustomer'])) {
        http_response_code(400);
        jsonError();
    }

    $idCustomer = $body['idCustomer'];
    $user = user($idCustomer);

    /**
     * 409: se intenta dar de alta un duplicado.
     */
    if ($user) {
        http_response_code(409);
        jsonError();
    }

    /**
     * 500: error interno del servidor.
     */
    if (!user_in($idCustomer)) {
        http_response_code(500);
        jsonError();
    }

    jsonSuccess();
}
/**
 * Modificación de clientes: PUT
 */
elseif($method == 'PUT' && isset($_GET['idCustomer'])) {
    $idCustomer = $_GET['idCustomer'];

    /**
     * Error 400: el cliente no proporciona los parámetros adecuados.
     */
    if (!isset($body['newIdCustomer'])) {
        http_response_code(400);
        jsonError();
    }

    $oldIdCustomer = $_GET['idCustomer'];
    $newIdCustomer = $body['newIdCustomer'];

    /**
     * 404: no existe el cliente.
     */
    $user = user($oldIdCustomer);
    if (!$user) {
        http_response_code(404);
        jsonError();
    }

    /**
     * 409: conflicto a la hora de hacer la modificación.
     */
    $user = user($newIdCustomer);
    if ($user) {
        http_response_code(409);
        jsonError();
    }

    /**
     * 500: error interno del servidor.
     */
    if (!user_up($oldIdCustomer, $newIdCustomer)) {
        http_response_code(500);
        jsonError();
    }

    jsonSuccess();

}
/**
 * Baja de clientes: DELETE
 */
elseif($method == 'DELETE' && isset($_GET['idCustomer'])) {
    $idCustomer = $_GET['idCustomer'];

    $user = user($idCustomer);

    /**
     * 404: no existe el cliente.
     */
    if (!$user || $user=='galeriar') {
        http_response_code(404);
        jsonError();
    }

    /**
     * 500: error interno del servidor.
     */
    if (!user_de($idCustomer)) {
        http_response_code(500);
        jsonError();
    }

    jsonSuccess();
}
/**
 * 405: método no soportado
 */
else {
    http_response_code(405);
    jsonError();
}
?>