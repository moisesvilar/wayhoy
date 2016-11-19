<?php
require_once("../includes/ini.php");
require_once("credens.php");
$albumId = db_escape($_GET['album']);
$fanpage_name = db_escape($_GET['fanpage_name']);
$album_title = db_escape($_GET['album_title']);
$album_id = db_escape($_GET['album_id']);
$q = "update image_album SET posicion = posicion + 1 
WHERE album_id = '$albumId'";
	db_query($q);

  $q = "INSERT INTO image_album (album_id, image_id, update_ts, title, description, subdescription, duration) VALUES ('$albumId', 595, CURRENT_TIMESTAMP, '$album_id', 'Fanpage:', ' $fanpage_name - Álbum: $album_title', '60')";
  if(!($result = db_in($q))){
	http_response_code(409);
        exit;  
	  }
	$result = str_replace('\n', '', $result);
  	$result = str_replace('\r', '', $result);
	echo $result;
	exit;