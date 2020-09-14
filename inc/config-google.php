<?php

//Include Google Client Library for PHP autoload file
require_once 'google-api/vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('767593746677-3d9c6eelr0cltbs3pugem219e81ppooc.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('Z9FM2yADNYfLzxgxpoNsQf_h');

//Set the OAuth 2.0 Redirect URI
$google_client->setRedirectUri(getProtocol().'://'.$SERVER_NAME.'/biochem/');

//
$google_client->addScope('email');

$google_client->addScope('profile');

// Disable security only for localhost
if(!isSecureConn()){
    $google_client->setHttpClient(new \GuzzleHttp\Client(array(
        'verify' => false,
    )));
}

//start session on web page
session_start();

?>