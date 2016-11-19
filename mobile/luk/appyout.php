<?php
require_once("../includes/ini.php");
require_once("credens.php");
if (isset($_GET['live'])){
$query = db_escape($_GET['q']);
$content = file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet&eventType=live&q='.$query.'&type=video&maxResults=15&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o');
echo $content;
}elseif (isset($_GET['playlistId'])){
$id = db_escape($_GET['playlistId']);
$content = file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$id.'&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o');
echo $content;
}elseif (isset($_GET['type'])){
$type = db_escape($_GET['type']);
$query = db_escape($_GET['q']);
$content = file_get_contents('https://www.googleapis.com/youtube/v3/search?type='.$type.'&order=viewcount&q='.$query.'&maxResults=20&part=snippet&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o');
echo $content;
}elseif (isset($_GET['plid'])){
$id = db_escape($_GET['id']);
$content = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id='.$id.'&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o');
echo $content;
}elseif (isset($_GET['plidd'])){
$id = db_escape($_GET['id']);
$content = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet&playlistId='.$id.'&key=AIzaSyCJl4dKVUML6lF4fadTJN0Ifh4bxW_D69o');
echo $content;
}else{
http_response_code(405);
        exit;
}