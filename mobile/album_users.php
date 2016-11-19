<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $contentType;
global $body;

$admin = $headers['User'];

if ($method == 'GET' && !isset($_GET['assigned'])) {
    $q = "
    SELECT user.* FROM user
    WHERE admin = '$admin'
    AND email NOT IN (SELECT user FROM album_user WHERE album_id = '$_GET[album]')
    AND email NOT IN (SELECT user FROM album WHERE album_id = '$_GET[album]')
    AND deleted IS NULL
    ";
    echo toJson(db_select($q));
    exit;
}

if($method == 'GET' && isset($_GET['assigned'])) {
    $q = "
    SELECT user.* FROM user
    WHERE admin = '$admin'
    AND (
        email IN (SELECT user FROM album_user WHERE album_id = '$_GET[album]') OR
        email IN (SELECT user FROM album WHERE album_id = '$_GET[album]')
    )
    AND deleted IS NULL
    ";
    echo toJson(db_select($q));
    exit;
}

if($method == 'POST') {
    $q = "
    INSERT INTO album_user SET
    album_id = '$_GET[album]',
    user = '$body[user]'
    ";
    if(!db_in($q)) {

        http_response_code(500);
        exit;
    }
    echo toJson(user($body['user']));
    exit;
}

if($method == 'PUT') {
    $q = "
    DELETE FROM album_user WHERE album_id = '$_GET[album]' AND user = '$body[user]';
    ";
    if(!db_query($q)) {
        http_response_code(500);
        exit;
    }
    echo toJson(user($body['user']));
    exit;
}