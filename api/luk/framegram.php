<?php
require_once("miniconfig.php");
if (!isset($_POST['code'])){
	http_response_code(406);
   	exit;
	}
$scr=db_escape($_POST['code']);
$del=db_escape($_POST['del']);
if ($del=='no'){
	$myArray = array();
	$q= "select video from vidgram where scr='$scr'";
	$result = db_select($q);
		if ($result) {
    		echo toJson($result);
			exit;
		}else{
			http_response_code(200);
    		exit;
		}
}else{
	$video = db_escape($_POST['video']);
	$q = "DELETE FROM vidgram WHERE scr='$scr' AND video='$video'";
	$result = db_query($q);
		if ($result) {
    		http_response_code(200);
    		exit;
		}else{
			http_response_code(403);
    		exit;
		}
}