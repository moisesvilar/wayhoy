<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $body;

/**
 * Alta de STBs: POST
 */
if ($method == 'POST') {
    /**
     * Error 400: el cliente no proporciona los parámetros adecuados.
     */
    if (!isset($body['idCustomer']) || !isset($body['iCC'])) {
        http_response_code(400);
        jsonError();
    }

    $idCustomer = $body['idCustomer'];
    $icc = $body['iCC'];

    /**
     * 404: no existe el STB
     */
    if (!user($idCustomer)) {
        http_response_code(404);
        jsonError();
    }

    $stb = stb($idCustomer, $icc);

    /**
     * 409: se intenta dar de alta un duplicado.
     */
    if ($stb) {
        http_response_code(409);
        jsonError();
    }

    /**
     * 500: error interno del servidor
     */
    if (!stb_in($idCustomer, $icc)) {
        http_response_code(500);
        jsonError();
    }

    jsonSuccess();
}
/**
 * Modificación de STBs: PUT
 */
elseif($method == 'PUT' && isset($_GET['icc'])) {
    /**
     * Error 400: el cliente no proporciona los parámetros adecuados.
     */
    if (!isset($body['newICC'])) {
        http_response_code(400);
        jsonError();
    }

    /**
     * 404: No existe el oldICC
     */
    if (!stb(false, $_GET['icc'])) {
        http_response_code(404);
        jsonError();
    }

    /**
     * 409: conflicto a la hora de hacer la modificación.
     */
    if (stb(false, $body['newICC'])) {
        http_response_code(409);
        jsonError();
    }

    /**
     * 500: error interno del servidor.
     */
    if (!stb_up($_GET['icc'], $body['newICC'])) {
        http_response_code(500);
        jsonError();
    }

    jsonSuccess();
}
/**
 * Baja de STB: DELETE
 */
elseif($method == 'DELETE' && isset($_GET['icc'])) {

    /**
     * 404: no existe el desco
     */
    if (!stb(false, $_GET['icc'])) {
        http_response_code(404);
        jsonError();
    }

    /**
     * 500: error interno del servidor.
     */
    if (!stb_de($_GET['icc'])) {
        http_response_code(500);
        jsonError();
    }

    /**
     * Eliminamos usuarios sin descos
     */
    removeUsersWithouthStbs();

    jsonSuccess();
}
/**
 * 405: método no soportado.
 */
else {
    http_response_code(405);
    jsonError();
}

?>