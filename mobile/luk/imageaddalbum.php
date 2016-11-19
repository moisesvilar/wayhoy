<?php
require_once("../includes/ini.php");
require_once("credens.php");
if (!$_GET['hazlo']){
$imageId = db_escape($_GET['image']);
$albumId = db_escape($_GET['album']);
if ($_GET['album']){
	$albumId = db_escape($_GET['album']);
  $title = db_escape($_GET['title']);
  $description = db_escape($_GET['desc']);
  $subdescription = db_escape($_GET['subdesc']);
$q = "update image_album SET posicion = posicion + 1 
WHERE album_id = '$albumId'";
	db_query($q);

  $q = "INSERT INTO image_album (album_id, image_id, update_ts, title, description, subdescription, posicion) VALUES ('$albumId', '$imageId', CURRENT_TIMESTAMP, '$title', '$description', '$subdescription','1')";
  $result = db_in($q);
  $result = str_replace('\n', '', $result);
  $result = str_replace('\r', '', $result);
	echo $result;
}
else {
  $q = "select image_album_id from image_album where image_album_id>=1 order by image_album_id desc limit 1";
  $row = db_result($q);
  if ($row === false) return false;
  $result = $row['image_album_id'];
  $result = str_replace('\n', '', $result);
  $result = str_replace('\r', '', $result);
	echo $result;
}
}else{
	$q = "select album_id from image_album where image_album_id>=1 order by image_album_id";
	$albums = db_result($q, true);	
    $i=0;
    foreach($albums as $album) {
		$album=$album['album_id'];
		$q2 = "select image_album_id from image_album where album_id='$album' order by image_album_id";
		$imagesalbum = db_result($q2, true);	
   		$ib=1;
		foreach($imagesalbum as $image) {
			$image=$image['image_album_id'];
			$q3="UPDATE image_album set posicion = '$ib' where image_album_id = '$image'";
			db_query($q3);
			$ib++;
		}
	$i++;
	}
}
