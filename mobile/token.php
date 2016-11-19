<?php
require_once("includes/ini.php");

global $method;
if ($method == 'GET') {
    echo json_encode(array('token' => token()));
}
else {
    http_response_code(405);
    exit;
}

?>