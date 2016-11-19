<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $body;

$_GET['which'] = str_replace('/', '', $_GET['which']);

/**
 * Consulta de cliente a partir de ICC
 */
if ($method == 'GET' && $_GET['which'] == 'customer') {
    /**
     * 400: el cliente no proporciona los parámetros requeridos.
     */
    if (!$_GET['query']) {
        http_response_code(400);
        jsonError();
    }

    /**
     * 404: no existe el cliente con ese ICC
     */
    if (!($stb = stb(false, $_GET['query']))) {
        http_response_code(404);
        jsonError();
    }

    $result['result'] = $stb[0]['user_id_r'];
    echo json_encode($result);
}
/**
 * Consulta de álbum a partir de ICC
 */
elseif ($method == 'GET' && $_GET['which'] == 'albums') {

    /**
     * 400: el cliente no proporciona los parámetros requeridos.
     */
    if (!$_GET['query']) {
        http_response_code(400);
        jsonError();
    }

    /**
     * 404: no existe el cliente con ese ICC
     */
    if (!($stb = stb(false, $_GET['query']))) {
        http_response_code(404);
        jsonError();
    }

   // print_r($stb);


    /**
     * NULL: el cliente no se ha inicializado, no tiene álbumes y tiene que crearlos.
     */
    $userId = $stb[0]['user_id_r'];
    $user = user($userId);
    $albums = albums($user['email']);
    if (!$albums || count($albums) == 0) {
        $result['result'] = 'null';
        echo json_encode($result);
        exit;
    }
	if (!($albums = albumprogramm($stb[0]['code']))){
		$albums = screen_albums($stb[0]['code']);
		}
		else{
		pausarInactivos($stb[0]['code']);	
		}
    
	// $siteUrl=siteURL().$config['project'];
	$siteurl='http://promotv.mundo-r.com/promotv/api';
    /**
     * El cliente tiene álbumes pero ninguno asociado a la pantalla: se envía el RSS de tutorial de envío de album
     */
    if (!$albums) {
        $language = $_GET['language'] ? $_GET['language'] : 'es';
        $config = parse_ini_file('includes/config.ini');
        $result['result'] = $siteurl."/".$config['rss_tutorial_album']."?language=".$language;
        echo json_encode($result);
    }
    /**
     * El cliente ya tiene un álbum asociado a la pantalla: se envía el RSS con las imágenes del álbum
     */
    else {
        $config = parse_ini_file('includes/config.ini');
        $result['result'] = $siteurl."/".$config['rss_album']."?album=".$albums[0]['album_id'];
        echo json_encode($result);
    }


}
/**
 * 405: método no soportado
 */
else {
    http_response_code(405);
    jsonError();
}
?>