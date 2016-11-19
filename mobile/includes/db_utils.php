<?php

/**
 * Realiza la conexión a la base de datos.
 * @param bool|string $server Opcional. El nombre del servidor. Si se omite, se coje el del fichero de configuración.
 * @param bool|string $user Opcional. El nombre del usuario. Si se omite, se coje el del fichero de configuración.
 * @param bool|string $pass Opcional. La contraseña del usuario. Si se omite, se coje el del fichero de configuración.
 * @param bool|string $db Opcional. El nombre de la base de datos. Si se omite, se coje el del fichero de configuración.
 * @return mysqli La conexión a la base de datos.
 */
function db_connect($server=false, $user=false, $pass=false, $db=false) {
    static $connection;
    if (!isset($connection)) {
        $config = parse_ini_file('config.ini');
        if (!$server) $server = $config['server'];
        if (!$user) $user = $config['user'];
        if (!$pass) $pass = $config['pass'];
        if (!$db) $db = $config['db'];
        $connection = mysqli_connect($server, $user, $pass, $db);
		if(!$connection) {
			$errno = mysqli_connect_errno();
			$error = mysqli_connect_error();
			echo "Error $errno al conectar a la base de datos $bd con los parámetros $server, $user, $pass: $error";
			die();
		}
    }
    return $connection;
}

/**
 * Ejecuta una consulta sobre la base de datos.
 * @param $q string La consulta
 * @return bool|mysqli_result El resultado de la consulta o false si ha ocurrido algún error.
 * "puntero" 
 */
function db_query($q) {
    $connection = db_connect();
    $info = mysqli_query($connection, $q) or die(mysqli_error($connection));
    return $info;
}

/**
 * Ejecuta una consulta en la base de datos y devuelve el último identificador autoincremental producido.
 * @param string $q La consulta a ejecutar.
 * @return bool|int|string El identificador, cero si no hay o false si ha ocurrido algún error.
 * mysqli_insert_id devuelve los valores de campos autoincrement generado y si no devuelve 0 (que es lo mismo que false)
 */
function db_in($q) {
    $connection = db_connect();
    $info = mysqli_query($connection, $q);
    if ($info) return mysqli_insert_id($connection);
    return false;
}

/**
 * Realiza un SELECT a la base de datos.
 * @param $q string La consulta
 * @return array|bool Los datos de la consulta o false si ha ocurrido algún error.
 */
function db_select($q) {
    $rows = array();
    $result = db_query($q);
    if($result === false) return false;
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

/**
 * Realiza el escape de una cadena de texto para evitar ataques de inyección SQL.
 * @param $value string El valor.
 * @return string El valor escapado.
 */
function db_escape($value) {
    $connection = db_connect();
    return mysqli_real_escape_string($connection, $value);
}

/**
 * Devuelve el resultado de una consulta en la base de datos, como un array asociativo si la consulta
 * ha devuelto una única fila, o como un array de arrays asociativos si la consulta ha devuelto varias filas.
 * @param $q string La consulta.
 * @return array|bool El resultado de la consulta o false si ha ocurrido algún error.
 * si la consulta devuelve un valor, devuelve un objeto, si no devuelve array de objetos
 */
function db_result($q, $alwaysArray=false) {
    $info = db_select($q);
    if (!$alwaysArray && count($info) == 1) return $info[0];
    return $info;
}

?>