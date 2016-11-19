<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $contentType;
global $body;

$admin = $headers['User'];

if ($method == 'GET' && !isset($_GET['assigned'])) {
    $q = "
    SELECT DISTINCT
    user.*
    FROM user
    WHERE admin = '$admin'
    AND email NOT IN (SELECT user FROM user_screen WHERE screen = '$_GET[screen]')
    AND deleted IS NULL
    ";
    echo toJson(db_select($q));
    exit;
}

if($method == 'GET' && isset($_GET['assigned'])) {
    $q = "
    SELECT
    user.*,
    user_screen.visible
    FROM user
    INNER JOIN user_screen ON user_screen.user = user.email
    WHERE admin = '$admin'
    AND user_screen.screen = '$_GET[screen]'
    AND deleted IS NULL
    GROUP BY user.email
    ";
    echo toJson(db_select($q));
    exit;
}

if($method == 'POST') {
    $q = "
    INSERT INTO user_screen SET
    screen = '$_GET[screen]',
    user = '$body[user]'
    ";
    if(!db_query($q)) {
        http_response_code(500);
        exit;
    }
    echo toJson(user($body['user']));
    exit;
}

if($method == 'PUT' && !$_GET['visible'] && !$_GET['invisible']) {
    $q = "
    DELETE FROM user_screen WHERE screen = '$_GET[screen]' AND user = '$body[user]';
    ";
    if(!db_query($q)) {
        http_response_code(500);
        exit;
    }
    echo toJson(user($body['user']));
    exit;
}

if($method == 'PUT' && $_GET['visible']) {
    $q = "UPDATE user_screen SET visible = '1' WHERE screen = '$_GET[screen]' AND user = '$body[user]'";
    if(!db_query($q)) {
        http_response_code(500);
        exit;
    }
    echo toJson(user($body['user']));
    exit;
}

if($method == 'PUT' && $_GET['invisible']) {
    $q = "UPDATE user_screen SET visible = '0' WHERE screen = '$_GET[screen]' AND user = '$body[user]'";
    if(!db_query($q)) {
        http_response_code(500);
        exit;
    }
    echo toJson(user($body['user']));
    exit;
}