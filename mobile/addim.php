<?php
require_once("includes/ini.php");

/**
 * Comprobamos credenciales
 */
if (!$_GET['u'] || !$_GET['t']) {
    http_response_code(400);
    exit;
}
$email = $_GET['u'];
$hash = $_GET['t'];
$user = user($email);
if (!$user) {
    http_response_code(406);
    exit;
}
$expectedHash = md5($email.$user['password']);
if ($expectedHash != $hash) {
    http_response_code(403);
    exit;
}

/**
 * Recojemos, procesamos y guardamos en disco la imagen
 */
/*
$config = parse_ini_file('includes/config.ini');
$images_directory = $config['images_directory'];
$thumbnails_directory = $config['thumbnails_directory'];
$filename = md5($body['image']).'.jpg';
$url = siteURL().$config['project']."/".$images_directory.$filename;
$url_thumbnail = siteURL().$config['project']."/".$thumbnails_directory.$filename;
$imageName = base64_to_jpeg($body['image'], $images_directory.$filename);
$resultOriginal = prepareOriginalImage($imageName, $images_directory.$filename);
$resultThumbnail = prepareThumbnailImage($imageName, $thumbnails_directory.$filename);
if (!$resultOriginal || !$resultThumbnail) {
    http_response_code(500);
    exit;
}
*/

$config = parse_ini_file('includes/config.ini');
$images_directory = $config['images_directory'];
$thumbnails_directory = $config['thumbnails_directory'];
$filename = md5_file($_FILES["Filedata"]["tmp_name"]).".jpg";
$filename = md5(time().$filename).".jpg";
$url = siteURL().$config['project']."/".$images_directory.$filename;
$url_thumbnail = siteURL().$config['project']."/".$thumbnails_directory.$filename;
$resultOriginal = prepareOriginalImage($_FILES["Filedata"]["tmp_name"], $images_directory.$filename);
$resultThumbnail = prepareThumbnailImage($_FILES["Filedata"]["tmp_name"], $thumbnails_directory.$filename);
if (!$resultOriginal || !$resultThumbnail) {
    http_response_code(500);
    exit;
}

/**
 * Si la imagen ya existe, la devolvemos.
 */
if ($image = image(false, false, $filename, $user)) {
    echo toJson($image);
    exit;
}

/**
 * Insertamos la imagen en la galería del usuario
 */
if (($image = image_in($filename, $url, $url_thumbnail, $email)) === false) {
    http_response_code(500);
    exit;
}

/**
 * La imagen debe asociarse a un álbum
 */
//if (isset($_POST['album'])) {
if (isset($_GET['a'])) {
    /**
     * Si la imagen no está asociada al album, la asociamos
     */
    //if (!image($image['id'], $_POST['album'])) {
    if (!image($image['id'], $_GET['a'])) {
        /**
         * 500: error al insertar la imagen en la base de datos.
         */
        //if (!image_album_in($image['id'], $_POST['album'])) {
        if (!image_album_in($image['id'], $_GET['a'])) {
            http_response_code(500);
            exit;
        }
    }
}
