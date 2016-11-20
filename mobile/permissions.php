<?php
require_once("includes/ini.php");

global $method;
global $headers;
global $contentType;
global $body;

$admin = $headers['User'];

/**
 * GET: listado de usuarios de un administrador.
 */
if ($method == 'GET' && !isset($_GET['which'])) {
    $permissions = permissions($admin);
    echo toJson($permissions);
}
/**
 * GET: obtención de datos de un usuario.
 */
elseif ($method == 'GET' && isset($_GET['which'])) {
    $column = $_GET['which'];
    $permission = permissions($admin, $column);
    echo toJson($permission);
}