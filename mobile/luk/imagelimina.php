<?php
require_once("../includes/ini.php");
require_once("credens.php");
$image = db_escape($_GET['image']);
if(!image_de($image)){
	http_response_code(404);
    exit;
	}
	http_response_code(200);
    exit;

