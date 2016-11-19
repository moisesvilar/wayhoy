<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, User, Secret");
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, User, Secret");
    exit;
}

require_once("global.php");
require_once('PHPMailer/PHPMailerAutoload.php');
require_once("db_utils.php");
require_once("model.php");
require_once("functions.php");

ini_set("allow_url_fopen", true);

$body = json_decode(file_get_contents('php://input'), true);
$headers = getallheaders();
$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'OPTIONS') $method = 'GET';
if (isset($_SERVER['CONTENT_TYPE'])) {
    $contentType = $_SERVER["CONTENT_TYPE"];
}

?>