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
        require "../Libraries/vendor/autoload.php";
        include_once('../Libraries/navbar.php');
        createnavbar("settings.profile");
        createsettingsnavbar('settings.preferences');
        
        ?>
        <div class="normalcontentnavbar">
            <h1>Preferences</h1>
            <form method="POST">
                <p>Enable 2FA authentication</p>
                <input type="checkbox" name="2fa" id="2fa">
                <input type="submit" class="submitbutton" value="Save">
                <br>
            </form>
            <?php
            $servername = "localhost";
            $dbusername = "root";
            $password = "";
            $dbname = "Company";
            $conn = new mysqli($servername, $dbusername, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['2fa'])) {
                    $_SESSION["authenticator"] = new PHPGangsta_GoogleAuthenticator();
                    $_SESSION["secret"] = $_SESSION["authenticator"]->createSecret();
                    echo "<p>secret: {$_SESSION['secret']}</p>";
                    $website = "AccessFrame";
                    $title = $_SESSION['username'];
                    $qrCodeUrl = $_SESSION["authenticator"]->getQRCodeGoogleUrl($title, $_SESSION["secret"], $website);
                    echo "<img src='$qrCodeUrl'>";
                }
                // Check if the QR code was successfully scanned by the user
                if (isset($_POST['verification_code'])) {
                    $verificationCode = $_POST['verification_code'];
                    $checkResult = $_SESSION["authenticator"]->verifyCode($_SESSION["secret"], $verificationCode, 2); // 2 = 2*30sec clock tolerance
                    print ("<script>console.log('{$checkResult}');</script>");
                    if ($checkResult) {
                        print ("<script>console.log('2FA sign up successfull');</script>");
                        // Store the secret in the database to verify the code on every login
                        $true = 1;
                        $secretstmt = $conn->prepare("UPDATE users SET 2FAsecret = ?, 2FAstatus = ? WHERE email = ?");
                        $secretstmt->bind_param("sis", $_SESSION["secret"], $true, $_SESSION['email']);
                        $secretstmt->execute();
                        $secretstmt->close();
                    } else {
                        echo "Invalid verification code.";
                    }
                }
            }
            ?>
            <form method="POST">
                <label for="verification_code">Enter verification code:</label>
                <input type="text" name="verification_code" id="verification_code">
            </form>
        </div>

    </div>
    <script>
        if (window.innerWidth < 768) {
            $(".innavbar").hide();
        }
    </script>

</body>

</html>