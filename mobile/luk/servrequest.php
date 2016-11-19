<?PHP
require_once("../includes/ini.php");
require_once("credens.php");
if (!isset($_GET['tienda'])){
	http_response_code(404);
    exit;
	}
$tienda = db_escape($_GET['tienda']);
function sendPostData($url, $post){
$headers= array('Accept: application/json','Content-Type: application/json');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
  $result = curl_exec($ch);
  curl_close($ch);  // Seems like good practice
  return $result;
}	
	$postdata =  array(
        'placeID' => $tienda,
        'fields' => array('name', 'price'),
		'filters' => array('placeIDs' => array($tienda)),
		'relations' => array('imageURL', 'otherimages')
    );
	$postdata = json_encode($postdata);
	$url='http://dl-3.cloudapp.net/3/webservice/tpv/product/get';
//$postdata = http_build_query($postdata);
	
//$postdata=urldecode($postdata);
/*
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);
$context  = stream_context_create($opts);
$result = file_get_contents($url, false, $context);
echo $result;

*/
	echo sendPostData($url, $postdata);


