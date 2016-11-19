<?php
require_once("miniconfig.php");
$cambio='';
if (!isset($_POST['code'])){
	exit;
	}
	$screen = db_escape($_POST['code']);
	if (isset($_POST['pr']) && $_POST['pr']!=''){
		$q="UPDATE programming SET pausado = '0' WHERE screen = '$screen'";
			$result = db_query($q);
		}	
    $q="SELECT * FROM programming WHERE DAYOFWEEK(NOW()) = day AND CURTIME() BETWEEN start AND end AND screen = '$screen' AND pausado = 0 ORDER BY start DESC LIMIT 1";
	$result = db_query($q);
	if ($row = mysqli_fetch_array($result)){
    	if($result === false) {
		}else{ 
			$programming_id=$row['programming_id'];
			$cambio=$row['cambio'].':'.$row['album'].':'.$row['valor'];
			$q2="UPDATE programming SET pausado = '1' WHERE programming_id='$programming_id'";
			db_query($q2);					
			$q3="UPDATE programming SET pausado = '0' WHERE (DAYOFWEEK(NOW()) != day || CURTIME() NOT BETWEEN start AND end) AND screen = '$screen'";
			db_query($q3);
				if ($row['cambio'] != str_replace('Yout', '', $row['cambio'])){
					$campo = 'video';
					$video = $row['cambio'].':'.$row['valor'];
				}else if ($row['cambio']  == 'font'){
					$campo = 'font';
					$video = $row['valor'];
				}else if ($row['cambio']  == 'tpl'){
					$campo = 'tpl';
					$video = $row['valor'];
				}else if ($row['cambio']  == 'fondo'){
					$campo = 'fondo';
					$video = $row['valor'];
				}else if ($row['cambio']  == 'radio'){
					$campo = 'radio';
					$video = $row['valor'];
				}else{ 
					$campo = 'album_id';
					$video = $row['album'];
				}
				$q="UPDATE screen_album SET `".$campo."` = '$video' WHERE screen = '$screen'";
				$result = db_query($q);
    	}}
    echo $cambio;
	exit;