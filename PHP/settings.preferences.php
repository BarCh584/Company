<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <?php
        include_once('../Libraries/navbar.php');
        createnavbar("settings.profile");
        ?>
        <div class="content">
            <h1>Preferences</h1>
            <form action="settings.preferences.php" method="post">
                <br>
                <label for="theme">Theme:</label>
                <span class="slider"></span>
                <br>
                <!--/* Parts of this site uses code from the project "smooth-frog-53" made by JkHuger (https://uiverse.io/JkHuger/smooth-frog-53), licensed under the MIT License*/-->
                <p>Select your theme</p>
                <div class="sliderbutton">
                    <label>
                        <input class="toggle-checkbox" type="checkbox">
                        <div class="toggle-slot">
                            <div class="sun-icon-wrapper">
                                <div class="iconify sun-icon" data-icon="feather-sun" data-inline="false"></div>
                            </div>
                            <div class="toggle-button"></div>
                            <div class="moon-icon-wrapper">
                                <div class="iconify moon-icon" data-icon="feather-moon" data-inline="false"></div>
                            </div>
                        </div>
                    </label>
                </div>
                <br>
                <p>Enable 2FA authentication</p>
                <input type="checkbox" name="2fa" id="2fa">
                <input type="submit" class="submitbutton" value="Save">
            </form>
        </div>
    </div>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['2fa'])) {
        include_once "../Libraries/GoogleAuthenticator-2.x/src/FixedBitNotation.php";
        include_once "../Libraries/GoogleAuthenticator-2.x/src/GoogleAuthenticatorInterface.php";
        include_once "../Libraries/GoogleAuthenticator-2.x/src/GoogleAuthenticator.php";
        include_once "../Libraries/GoogleAuthenticator-2.x/src/GoogleQrUrl.php";

        $secret = 'test';
        
        $g = new Sonata\GoogleAuthenticator\GoogleAuthenticator();
        $code = $g->getCode($secret);
        echo 'Current Code is: ';
        echo $g->getCode($secret);
        echo '<br>';
        echo "Check if $code is valid: ";
        if($g->checkCode($secret, $code)){
            echo 'Valid';
        } else {
            echo 'Invalid';
        }
        $secret = $g->generateSecret();
        echo "Get a new secret $secret <br>";
        echo "Scan this QR code: (to scan with an app like Google Authenticator) <br>";
        echo Sonata\GoogleAuthenticator\GoogleQrUrl::generate('test', $secret, 'GoogleAuthenticatorExample');
    }
    ?>
</body>

</html>