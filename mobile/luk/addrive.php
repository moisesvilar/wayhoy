<?php
require_once("../includes/ini.php");
require_once("credens.php");
if (isset($_GET['album'])){
$albumId = db_escape($_GET['album']);
}
if (isset($_GET['album_id'])){
$album_id = db_escape($_GET['album_id']);
}
if (isset($_GET['image_id'])){
$image_id = db_escape($_GET['image_id']);
}
$q = "update image_album SET posicion = posicion + 1 
WHERE album_id = '$albumId'";
	if(!($result = db_query($q))){
		http_response_code(406);
        exit;  
	  }
$q = "INSERT INTO image_album (album_id, image_id, update_ts, title, description, duration) VALUES 
('$albumId', '$image_id', CURRENT_TIMESTAMP, '$album_id', 'Drive:', '60')";
  	if(!($result = db_in($q))){
		http_response_code(409);
        exit;
	  }
	$result = str_replace('\n', '', $result);
  	$result = str_replace('\r', '', $result);
	echo $result;
	exit;