<?php
session_start();
require_once('include/db_utils.php');
require_once('include/functions.php');

if($_REQUEST['login'] == '1') {
    if(!check_admin_credentials($_REQUEST['username'], $_REQUEST['password'])) {
        header("Location: index.php?error");
        exit;
    }
}

if(isset($_REQUEST['logout'])) {
    logout();
    header("Location: index.php");
    exit;
}

if(!check_access()) {
    header("Location: index.php");
    exit;
}

$user = get_user();

$url_absolute = 'http://wayhoy.com/wayhoy/panel/';