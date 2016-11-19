<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $contentType;
global $body;

$user = $headers['User'];

/**
 * POST: envío de un álbum al módulo WayHoy!
 */
if ($method == 'POST') {
    if (!isset($body['album']) || !isset($body['screen'])) {
        http_response_code(400);
        exit;
    }

    $album = $body['album'];
    /**
     * 404: el álbum no existe
     */
    if (!$album) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: el album no pertenece al usuario.
     */
    if (!isYours('album', $album, $user)) {
        http_response_code(403);
        exit;
    }

    $screen = $body['screen'];
    /**
     * 404: la pantalla no existe.
     */
    if (!screen($screen)) {
        http_response_code(404);
        exit;
    }

    /**
     * 403: la pantalla no pertenece al usuario
     */
    if (!isYours('user_screen', $screen, $user, 'screen')) {
        http_response_code(403);
        exit;
    }

    /**
     * 500: error al enviar el módulo
     */
    if(!sendAlbum($album, $screen)) {
        http_response_code(500);
        exit;
    }
}
else {
    http_response_code(405);
    exit;
}

?>