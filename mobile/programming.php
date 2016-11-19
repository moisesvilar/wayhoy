<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $body;

$user = $headers['User'];

/**
 * POST: creacion de programaciones
 */
if($method == 'POST') {
	$days = $body['days'];
	$times = $body['times'];
	$screen = $body['screen'];
	$album = $body['album'];
	
	$error = false;
	foreach($days as $day) {
		$idDay = $day['id'];
		foreach($times as $time) {
			$start = $time['start'];
			$end = $time['end'];
			$q = "INSERT INTO programming
			(
			screen,
			day,
			start,
			end,
			album
			)
			VALUES (
			'$screen',
			'$idDay',
			'$start',
			'$end',
			'$album'
			)
			";
			if (!db_in($q)) $error = true;
		}
	}
	
	if ($error) echo json_encode(array("msg" => "Ha ocurrido un error"));
	else  echo json_encode(array("msg" => "Programaci�n creada"));
	
}
/**
 * GET: obtencion de programaciones
 */
elseif($method == 'GET') {
    $q = "
    SELECT * FROM programming WHERE
    (
      album IN (SELECT album_id FROM album WHERE user = '$user') OR
      album IN (SELECT album_id FROM album_user WHERE user = '$user')
   )
    ORDER BY screen,start
    ";
    $programaciones = db_result($q, true);
    echo json_encode($programaciones);
}
/**
 * DELETE: eliminacion de programaciones
 */
elseif ($method == 'DELETE') {
    $id = $_GET['id'];
    $q = "DELETE FROM programming WHERE programming_id = '$id'";
    db_query($q);
}
/**
 * PUT: actualizacion de programaciones
 */
elseif($method == 'PUT') {
    $id = $body['id'];

    $q = "DELETE FROM programming WHERE programming_id = '$id'";
    db_query($q);

    $days = $body['days'];
    $times = $body['times'];
    $screen = $body['screen'];
    $album = $body['album'];

    $error = false;
    foreach($days as $day) {
        $idDay = $day['id'];
        foreach($times as $time) {
            $start = $time['start'];
            $end = $time['end'];
			$q = "DELETE FROM programming WHERE screen='$screen' AND day='$idDay' AND start='$start' AND end='$end' AND album ='$album'";
    db_query($q);
            $q = "INSERT INTO programming
			(
			screen,
			day,
			start,
			end,
			album
			)
			VALUES (
			'$screen',
			'$idDay',
			'$start',
			'$end',
			'$album'
			)
			";
            if (!db_in($q)) $error = true;
        }
    }

    if ($error) echo json_encode(array("msg" => "Ha ocurrido un error"));
    else  echo json_encode(array("msg" => "Programaci�n actualizada"));
}