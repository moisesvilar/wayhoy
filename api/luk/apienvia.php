<?php
require_once ('miniconfig.php');
if (!isset($_POST['cod'])) {
	http_response_code(400);
	exit;
	}else{
	$code=db_escape($_POST['cod']);
}
	$q='select pantalla, validado from gramola where pass="' . $code . '"';
	if(!$result = db_query($q)){
		 echo '<h1>El código ha cambiado</h1>'; 
		 exit;
	}else{
		$row = $result -> fetch_object();
		$pantalla = $row -> pantalla;
	}
	if (isset($_POST['video'])){
		$video=db_escape($_POST['video']);
		$q = "delete from vidgram where video='$video' AND scr='$pantalla'";
			if (!$result = db_query($q)) {
				echo "Hubo algún error 404";
				exit;
			}
		
		$q = "insert into vidgram (video, scr) values('$video', '$pantalla')";
		db_in($q);
	}
		$q = "UPDATE `screen_album` SET cambio='videogram', valor='videogram' where screen='$pantalla'";
		if (!db_query($q)) {
			echo "Hubo algún error 400";
			exit;
		} 
		echo 'ok';
?>