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
    if (!isset($body['code'])) {
        http_response_code(400);
        jsonError();
    }
	$valor==checkambio($body['code']);
		if (!$valor['cambio']='si'){
		$albums = screen_albums($body['code']);
		}else{
		pausarInactivos($body['code']);	
		}
		if (!$albums) {
        exit;
    	} else {
        $config = parse_ini_file('includes/config.ini');
        $result['result'] = $siteurl."/api/".$config['rss_album']."?album=".$albums[0]['album_id'];
        echo json_encode($result);
		exit;
    	}
    $q = "
    UPDATE screen_album SET
    cambio = 'no',
    ts = CURRENT_TIMESTAMP,
	valor=''
    WHERE screen = '".db_escape($body['code'])."'
    ";
    db_query($q);
	return($valor['valor']);
	
}
?>