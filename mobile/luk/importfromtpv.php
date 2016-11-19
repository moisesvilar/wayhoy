<?php
require_once("../includes/ini.php");
require_once("credens.php");
$image = db_escape($_GET['image']);
$user = db_escape($_GET['u']);
if (($image = fromtpv_in($image,$user)) === false) {
        http_response_code(500);
        exit;
    }
	echo $image;
	exit;



