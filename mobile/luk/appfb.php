<?php
require_once("../includes/ini.php");
require_once("credens.php");
if (isset($_GET['q'])){
$query = db_escape($_GET['q']);
$content = file_get_contents('https://graph.facebook.com/search/?q='.$query.'&limit=10&type=page&access_token=343617178992121|1ac7629c8c5a51d0b34c8af53c287d63');
echo $content;
}elseif (isset($_GET['id'])){
$id = db_escape($_GET['id']);
$content = file_get_contents('https://graph.facebook.com/v2.1/'.$id.'/?access_token=343617178992121|1ac7629c8c5a51d0b34c8af53c287d63');
echo $content;
}elseif (isset($_GET['cta'])){
$cta = db_escape($_GET['cta']);
$content = file_get_contents('https://graph.facebook.com/v2.1/'.$cta.'/?access_token=343617178992121|1ac7629c8c5a51d0b34c8af53c287d63');
echo $content;
}elseif (isset($_GET['ctaal'])){
$ctaal = db_escape($_GET['ctaal']);
$content = file_get_contents('https://graph.facebook.com/'.$ctaal.'/albums?access_token=343617178992121|1ac7629c8c5a51d0b34c8af53c287d63');
echo $content;
}else{
http_response_code(405);
        exit;
}