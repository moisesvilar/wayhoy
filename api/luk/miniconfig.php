<?PHP
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
function db_connect($server=false, $user=false, $pass=false, $db=false) {
    static $connection;
    if (!isset($connection)) {
        $config = parse_ini_file('../includes/config.ini');
        if (!$server) $server = $config['server'];
        if (!$user) $user = $config['user'];
        if (!$pass) $pass = $config['pass'];
        if (!$db) $db = $config['db'];
        $connection = mysqli_connect($server, $user, $pass, $db);
    }
    return $connection;
}
function db_query($q) {
    $connection = db_connect();
    $info = mysqli_query($connection, $q);
    return $info;
}
function db_escape($value) {
    $connection = db_connect();
    return mysqli_real_escape_string($connection, $value);
}
function db_in($q) {
    $connection = db_connect();
    $info = mysqli_query($connection, $q);
    if ($info) return mysqli_insert_id($connection);
    return false;
}
function db_select($q) {
    $rows = array();
    $result = db_query($q);
    if($result === false) return false;
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
function toJson($object) {
    //TODO: header('Content-Type: application/json; charset=utf-8');
    return json_encode($object);
}