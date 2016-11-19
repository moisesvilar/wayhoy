<?php
require_once ('miniconfig.php');
if (!isset($_GET['cod'])) {
	http_response_code(400);
	exit;
	}else{
	$code=db_escape($_GET['cod']);	
}
$q='select code from screen where code="' . $code . '"';
	if(!$result = db_query($q)){
		http_response_code(404);
    	exit;
	}
if(!isset($_GET['q'])){
		http_response_code(400);
    	exit;
	}
$q=$_GET['q'];
	$content = file_get_contents("https://graph.facebook.com/" . $q ."/photos?access_token=343617178992121|1ac7629c8c5a51d0b34c8af53c287d63&posts.limit(15)");
echo $content;
exit;