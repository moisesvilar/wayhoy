<?php
require_once ('miniconfig.php');
if (!isset($_GET['cod'])) {
	http_response_code(404);
	exit;
	}else{
	$code=db_escape($_GET['cod']);	
}
$q='select code from screen where code="' . $code . '"';
	if(!$result = db_query($q)){
		http_response_code(404);
    	exit;
	}
	if (isset($_GET['q'])){
		$q=$_GET['q'];
	}else{
		http_response_code(406);
    	exit;
	}
	if (isset($_GET['li'])){
		$li=$_GET['li'];
		if ($li=='--'){
			$li='';
			}else{
			$li = str_replace('--', '', $li);
			$li='&pageToken='.$li;
			}
	}else{
		http_response_code(406);
    	exit;
	}
$content = file_get_contents("https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=".$q.$li."&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o&maxResults=50");
echo $content;
exit;