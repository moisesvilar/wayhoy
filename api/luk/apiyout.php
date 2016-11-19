<?php
require_once ('miniconfig.php');
if (!isset($_GET['cod'])) {
	http_response_code(400);
	exit;
	}
	$code=db_escape($_GET['cod']);
	$query='select pantalla from gramola where pass="' . $code . '"';
	if(!$result = db_query($query)){
		http_response_code(400);
    	exit;
	}
if(!isset($_GET['q'])){
		http_response_code(400);
    	exit;
	}
$q=$_GET['q'];
$q=str_replace(' ', '%20', $q);
if (isset($_GET['kind'])){
	$content = file_get_contents("https://www.googleapis.com/youtube/v3/videos/?id=".$q."&part=contentDetails,snippet&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o");
	}else{
	$content = file_get_contents("https://www.googleapis.com/youtube/v3/search/?q=".$q."&maxResults=10&part=snippet&chart=mostPopular&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o");
	}
echo $content;
exit;