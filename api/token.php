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

    $stb = stb(false, $body['ICC']);
    /**
     * 404: no existe el ICC
     */
    if (!$stb) {
        http_response_code(404);
        jsonError();
    }

    $result['result'] = $stb[0]['code'];
    echo json_encode($result);
}

?>