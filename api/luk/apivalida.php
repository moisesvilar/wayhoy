<?php
require_once ('miniconfig.php');
if (!isset($_POST['cod'])) {
	http_response_code(400);
	exit;
	}else{
	$code=db_escape($_POST['cod']);
}
$q='select * from gramola where pass="'.$code.'"';
$result = db_query($q);
  if (!$row = mysqli_fetch_array($result)) {
		http_response_code(409); 
		exit;
		
		}
	echo 'ok';
	exit;
	
	
?>