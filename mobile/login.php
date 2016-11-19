<?php
require_once("includes/ini.php");

global $method;
if ($method == 'GET') {
    require_once("includes/login.php");
}
else {
    http_response_code(405);
    exit;
}

?>