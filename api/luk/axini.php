<?php
require_once("miniconfig.php");
if (!isset($_POST['code'])){
	exit;
	}
  $screen = db_escape($_POST['code']);
    $q = "SELECT album_id, video, font, tpl, fondo, radio, twitter FROM screen_album WHERE screen = '$screen'";
	$result = db_query($q);
    if($result === false) return false;
    if ($row = mysqli_fetch_array($result)) {
        $album=$row['album_id'];
		$video=$row['video'];
		$font=$row['font'];
		$tpl=$row['tpl'];
		$fondo=$row['fondo'];
		$radio=$row['radio'];
		$twitter=$row['twitter'];
    }
    echo $album.':'.$fondo.':'.$font.':'.$tpl.':'.$radio.':'.$video.':'.$twitter;
	exit;