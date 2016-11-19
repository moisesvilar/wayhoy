<?php
require_once("miniconfig.php");
if (!isset($_POST['code'])){
	exit;
	}
  $screen = db_escape($_POST['code']);
    $q = "SELECT cambio, valor, album_id, tpl FROM screen_album WHERE screen = '$screen'";
	$result = db_query($q);
    if($result === false) http_response_code(404);
    if ($row = mysqli_fetch_array($result)) {
        $cambio=$row['cambio'];
		$valor=$row['valor'];
		$album=$row['album_id'];
    }
	if ($cambio!=''){
		$tpl = $row['tpl'];
		if ($tpl == 3 && $cambio == 'album'){
			$masq=", tpl='1'";
			}else if ($tpl == 1 && $cambio != str_replace('Youtube', '', $cambio)){
			$masq=", tpl='3'";
			}else{
			$masq='';	
			}
		// dejamos cambio a nada para que ignore hacer cambios en la pantalla.
		// Si el cambio es album se hará la consulta a search222
		$q = "UPDATE screen_album set cambio = ''$masq WHERE screen = '$screen'";
		db_query($q);
		}else{
		$cambio='no';	
		}
		$q = "UPDATE user_screen set ts_up = CURRENT_TIMESTAMP WHERE screen = '$screen'";
		db_query($q);
    echo $cambio.':'.$album.':'.$valor;
	exit;