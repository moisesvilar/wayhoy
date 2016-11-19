<?php
require_once("../includes/ini.php");
require_once("credens.php");
$imageAlbumId = db_escape($_GET['image']);
$albumId = db_escape($_GET['album']);
$orden = db_escape($_GET['posicion']);
ordenamelo($orden, $albumId, $imageAlbumId);
actualizaPosicion($orden, $imageAlbumId);

