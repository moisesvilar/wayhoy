<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $body;

// $siteUrl=siteURL().$config['project'];
$siteurl='http://promotv.mundo-r.com/promotv/api';
/**
 * Obtención de RSS de tutorial: GET
 */
if ($method == 'GET' && $_GET['which'] == 'tutorial') {
    $language = $_GET['language'] ? $_GET['language'] : 'es';

    $config = parse_ini_file('includes/config.ini');
    $result['result'] = $siteurl."/".$config['rss_tutorial']."?language=".$language;
    echo json_encode($result);
}
/**
 * Obtención de RSS de contratación: GET
 */
elseif ($method == 'GET' && $_GET['which'] == 'advertising') {
    $language = $_GET['language'] ? $_GET['language'] : 'es';

    $config = parse_ini_file('includes/config.ini');
    $result['result'] = $siteurl."/".$config['rss_advertising']."?language=".$language;
    echo json_encode($result);
}

?>