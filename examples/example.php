<?php 

require '../vendor/autoload.php';

use Mamlaka\MamlakaAPI;

$api = new MamlakaAPI('production');
$response1 = $api->getToken('CometAppMain', 'cometappmain');
if(!$response1['error']){
    $response = $api->initiateMobilePayment(
        'Test1aBCDEFGHIJKLMmamlaka',
        'KES',
        1.0,
        '254768899729',
        'M-Pesa',
        'externalId3',
        'https://b8ca-217-21-116-242.ngrok-free.app'
    );
    print_r($response);
} else {
    echo "Authentication failed\n";
}

