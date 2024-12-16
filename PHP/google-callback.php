<?php
    require "vendor/autoload.php";
    use Google\Client;
    use Google\Service\Oauth2;
    require "google-auth-config.php";

    if(isset($_GET["code"])) {
        try {
            $token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);
            $client->setAccessToken($token);

            $google_service = new Oauth2($client);
            $userdata = $google_service->userinfo->get();

            // Store user data in session
            $_SESSION['email'] = $userdata->email;
            $_SESSION["username"] = $userdata->name;
            $_SESSION["picture"] = $userdata->picture;
            header("Location: startpage.php");
            exit;
        } catch (Exception $e) {
            echo "Error:".$e->getMessage();
            exit();
        }
    } else {
        die("No authorization code found");
    }
?>