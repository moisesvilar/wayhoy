<?php

require_once("includes/ini.php");

global $method;
global $headers;
global $body;

/**
 * Creación de token a partir de ICC: POST
 */
if ($method == 'POST') {
    /**
     * Error 400: el cliente no proporciona los parámetros adecuados.
     */
    if (!isset($body['ICC'])) {
        http_response_code(400);
        jsonError();
    }
	if (!isset($body['client'])) {
        http_response_code(400);
        jsonError();
    }
    $stb = stb($body['client'], $body['ICC']);
    /**
     * 404: no existe el ICC
     */
    if (!$stb) {
        http_response_code(404);
        jsonError();
    }

    $result['result'] = $stb[0]['code'];
    echo json_encode($result);
	exit;
}
if ($method == 'GET') {
    /**
     * Error 400: el cliente no proporciona los parámetros adecuados.
     */

    $user = user_screen(false, $_GET['code']);
    /**
     * 404: no existe el ICC
     */
    if (!$user) {
        http_response_code(404);
        jsonError();
    }

    $result['result'] = $stb[0]['code'];
    echo json_encode($result);
}
?>