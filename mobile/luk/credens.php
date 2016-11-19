<?PHP 
if (!isset($_GET['u']) || !isset($_GET['t'])) {
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

?>