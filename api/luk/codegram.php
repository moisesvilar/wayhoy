<?php
require_once("miniconfig.php");
if (!isset($_POST['code'])){
	http_response_code(406);
    exit;
	}
	function rander(){
		$caracter= "0123456789";
		    $rand='';
		    srand((double)microtime()*1000000);
  			for($i=0; $i<6; $i++) {
    			$rand.= $caracter[rand()%strlen($caracter)];
  			}
			return $rand;
	}
	$scr=db_escape($_POST['code']);
	$query ="select user from `user_screen` where screen = '$scr'";		
		if (!$result = db_query($query)){
			http_response_code(404);
    		exit;
			}
		$row = $result -> fetch_object();
		$user = $row -> user;
	$query ="select pantalla from `gramola` where pantalla = '$scr'";		
		if (!$result = db_select($query)) {
			$query ="insert into `gramola` (pantalla, validado) values ('$scr', '$user')";
			db_in($query);
			}
 	$rand=rander();
	$query ="Update `gramola` SET pass='$rand' WHERE pantalla='$scr'"; 
	db_query($query);
	echo $rand;
	exit;