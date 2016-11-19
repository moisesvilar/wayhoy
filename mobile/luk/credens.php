<?PHP 
if (!$_GET['u'] || !$_GET['t']) {
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
$expectedHash = $user['password'];
if ($expectedHash != $hash) {
    if(!checkCredentialsR($email, $hash)) {
        http_response_code(403);
        exit;
    }

}