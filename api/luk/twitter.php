<?php
require_once ('miniconfig.php');
if (!isset($_GET['cod'])) {
	http_response_code(405);
	exit;
	}
	$code=db_escape($_GET['cod']);
	$query='select pantalla from gramola where pass="' . $code . '"';
	if(!$result = db_query($query)){
		http_response_code(404);
    	exit;
	}
require_once("twitteroauth.php"); //Path to twitteroauth library
 
$twitteruser = $_GET['set'];
if ((strpos($twitteruser,'@'))===false && (strpos($twitteruser,'from:')) === false) {
 $twitteruser = "%23".$_GET['set']."";
}
if (strpos($twitteruser,'@')){
	$twitteruser=str_replace('@', '', $twitteruser);
	$twitteruser=str_replace($twitteruser, '@'.$twitteruser, $twitteruser);
}
$notweets = 30;
$consumerkey = "A1XvStK73zZ405LpgndoDvzlp";
$consumersecret = "JdTF8kOagg5t1miJMRCOOtyI1ZAsON4O0hhUhGgj0kulBmmY3Q";
$accesstoken = "1052419110-3XqL25oywQuvZKGhkxB4g5EFKl1wY5ldEUUjfnM";
$accesstokensecret = "9JCRzQLNIQeWbcAbp5saFnz2SruAW78pjEKo18K7Jxcj8";
 
function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
  return $connection;
}
 
$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
 
$tweets = $connection->get("https://api.twitter.com/1.1/search/tweets.json?q=$twitteruser&count=30");
 
echo json_encode($tweets);
?>