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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <?php
        include_once('../Libraries/navbar.php');
        createnavbar("settings.profile");
        createsettingsnavbar('settings.preferences');
        ?>
        <div class="normalcontentnavbar">
            <h1>Preferences</h1>
            <form action="settings.preferences.php" method="post">
                <p>Enable 2FA authentication</p>
                <input type="checkbox" name="2fa" id="2fa">
                <input type="submit" class="submitbutton" value="Save">
            </form>
        </div>
    </div>
    <script>
        if (window.innerWidth < 768) {
            $(".innavbar").hide();
        }
        </script>
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