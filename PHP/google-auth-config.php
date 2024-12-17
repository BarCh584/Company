<?php
    require_once "../Libraries/vendor/autoload.php";

    /*use Appwrite\Client;
    use Appwrite\Services\Account;

    $client = new Client();
    $client
    ->setEndpoint('http://localhost/v1') // Your API Endpoint
    ->setProject('accessframe'); // Your project ID

    $account = new Account($client);
    $googleloginurl = $account->createOAuth2Token('google', 'https://cloud.appwrite.io/v1/account/sessions/oauth2/callback/google/accessframe');

    header("Location: $googleloginurl");
    exit();*/
    session_start();
    $client = new Google_Client();
    $client->setClientId("805307410549-67rfmihm32avcenv324ldhegtgl7p28h.apps.googleusercontent.com");
    $client->setClientSecret("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");
    $client->setRedirectUri("https://cloud.appwrite.io/v1/account/sessions/oauth2/callback/google/accessframe");
    $client->addScope("email");
    $client->addScope("profile");
?>