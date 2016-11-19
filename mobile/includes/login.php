<?php

global $headers;
if (!isset($headers['User']) || !isset($headers['Secret'])) {
    http_response_code(403);
    exit;
}

$user = $headers['User'];
$secret = $headers['Secret'];

if (!checkCredentials($user, $secret)) {
    if(!checkCredentialsR($user, $secret)) {
        http_response_code(403);
        exit;
    }
    else {
        echo json_encode(array('is_admin' => true));
    }
}