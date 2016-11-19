<?php
require_once("../includes/ini.php");
require_once("credens.php");
//funciones apara APP TV
function albumprogramms($stb){
	$q="SELECT album as album_id FROM programming WHERE DAYOFWEEK(NOW()) = day AND CURTIME() BETWEEN start AND end AND pausado = '0' AND screen = '$stb' ORDER BY start DESC LIMIT 1";
	return db_result($q, true);
	}
function pausarInactivoss($stb){
	$q="UPDATE programming SET pausado = '0' WHERE screen = '$stb'";
	return db_query($q);
}

/**
 * Obtiene los álbumes asociados a una pantalla.
 * @param string $screen Código de la pantalla.
 * @return array|bool Los datos requeridos o false si ha ocurrido algún error.
 */
function screen_albumss($screen) {
    $screen = db_escape($screen);
    $q = "SELECT * FROM screen_album WHERE screen = '$screen'";
    return db_result($q, true);
}
	 /**
     * 404: no existe el cliente con ese ICC
     */
    if (!($stb = stb(false, $_GET['stb']))) {
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
		echo $albums;