<?php
require_once("includes/ini.php");

$album = $_GET['album'];
$user = $_GET['user'];

if ($album) {
	$q = "SELECT * FROM image_album WHERE album_id = '$album' ORDER BY creation_ts DESC LIMIT 1";	
}
elseif ($user) {
	$q = "SELECT * FROM image WHERE user = '$user' ORDER BY ts DESC LIMIT 1";	
}


$row = db_result($q);

echo $row['image_id'];