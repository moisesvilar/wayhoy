<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $body;

$user = $headers['User'];

if($method == 'GET' && $_GET['id']) {
    $result = array(
        'total_licenses' => total_licenses($_GET['id'])
    );

    echo toJson($result);
    exit;
}