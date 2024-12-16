<?php
    require_once "../Libraries/vendor/autoload.php";
    session_start();
    $client = new Google_Client();
    $client->setClientId("805307410549-67rfmihm32avcenv324ldhegtgl7p28h.apps.googleusercontent.com");
    $client->setClientSecret("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s");
    $client->setRedirectUri("https://cloud.appwrite.io/v1/account/sessions/oauth2/callback/google/accessframe");
    $client->addScope("email");
    $client->addScope("profile");
?>