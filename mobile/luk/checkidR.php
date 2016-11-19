<?PHP
require_once("../includes/ini.php");
require_once("credens.php");
$email=db_escape($_GET['u']);
$q='select user_id_r from user where email = "'.$email.'"';
$user_r = db_result($q);
print_r($user_r['user_id_r']);
exit;

