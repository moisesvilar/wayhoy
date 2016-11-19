<?php
require_once("../includes/ini.php");
require_once("credens.php");
// para insertar una función para enviar a la pantalla

function sendAlbum($albumId, $screenId, $cambio, $valor) {
    $albumId = db_escape($albumId);
    $screenId = db_escape($screenId);
	$cambio = db_escape($cambio);
	$valor = db_escape($valor);
	if ($album_id == '1000'){
		$campo = 'video';
		$video = $cambio.':'.$valor;
	}else if ($album_id == '1001'){
		$campo = 'font';
		$video = $valor;
	}else if ($album_id == '1002'){
		$campo = 'tpl';
		$video = $valor;
	}else if ($album_id == '1003'){
		$campo = 'fondo';
		$video = $valor;
	}else{ 
		$campo = 'album_id';
		$video = $albumId;
	}
    $q = "
    INSERT INTO screen_album (screen, album_id, cambio, valor, video, font, tpl, fondo, ts) VALUES
    ('$screenId', '$albumId', '$cambio', '$valor', '$video', '', '', '', CURRENT_TIMESTAMP)
    ON DUPLICATE KEY UPDATE
    `".$campo."` = '$video',
	`cambio` = '$cambio',
	`valor` = '$valor',
    `ts` = CURRENT_TIMESTAMP
    ";
    if (!db_query($q)) return false;
	$q = "SELECT programming_id FROM programming WHERE DAYOFWEEK(NOW()) = day AND CURTIME() BETWEEN start AND end AND pausado = '0' AND screen = '$screenId' ORDER BY start DESC LIMIT 1";
	$programming = db_result($q);
	$programmingId = $programming['programming_id'];
	$q = "UPDATE programming SET pausado = '1' WHERE programming_id = '$programmingId'";
	return db_query($q);
}
		
	
			