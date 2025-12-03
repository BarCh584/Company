<?php
require "../Libraries/vendor/autoload.php";
include_once('../Libraries/navbar.php');
createnavbar("settings.profile");
createsettingsnavbar('settings.preferences');
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "Company";
$conn = new mysqli($servername, $dbusername, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$check2fastatus = $conn->prepare("SELECT 2FAstatus FROM users WHERE email = ?");
$check2fastatus->bind_param("s", $_SESSION['email']);
$check2fastatus->execute();
$check2fastatus->store_result();
$check2fastatus->bind_result($twoFAstatus);
$check2fastatus->fetch();
if (isset($_POST['enable2fa'])) {
    $_SESSION["authenticator"] = new PHPGangsta_GoogleAuthenticator();
    $_SESSION["secret"] = $_SESSION["authenticator"]->createSecret();
    echo "<p>secret: {$_SESSION['secret']}</p>";
    $website = "AccessFrame";
    $title = $_SESSION['username'];
    $qrCodeUrl = $_SESSION["authenticator"]->getQRCodeGoogleUrl($title, $_SESSION["secret"], $website);

}
// Check if the QR code was successfully scanned by the user
if (isset($_POST['verification_code'])) {
    $verificationCode = $_POST['verification_code'];
    $checkResult = $_SESSION["authenticator"]->verifyCode($_SESSION["secret"], $verificationCode, 2); // 2 = 2*30sec clock tolerance
    if ($checkResult) {
        // Store the secret in the database to verify the code on every login
        $true = 1;
        $secretstmt = $conn->prepare("UPDATE users SET 2FAsecret = ?, 2FAstatus = ? WHERE email = ?");
        $secretstmt->bind_param("sis", $_SESSION["secret"], $true, $_SESSION['email']);
        $secretstmt->execute();
        $secretstmt->close();
        echo "<script>window.location.href = 'startpage.php';</script>";
    } else {
        echo "Invalid verification code.";
    }
}
if (isset($_POST['disable2fa'])) {
    $false = 0;
    $disable2fa = $conn->prepare("UPDATE users SET 2FAstatus = ?, 2FAsecret = ? WHERE email = ?");
    $disable2fa->bind_param("iss", $false, $false, $_SESSION['email']);
    $disable2fa->execute();
    $disable2fa->close();
    echo "<script>window.location.href = 'startpage.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
    <link rel="icon" href="../Logo2.png">
</head>

<body>
    <img src="../Logo2.png" class="logo">
    <script src="../Libraries/jquery/jquery-3.6.0.min.js"></script>
    <div class="innormalcontentnavbar">
        <h1>Preferences</h1>
        <?php

        if ($twoFAstatus == 1) { ?>
            <p>2FA is enabled</p>
            <form method="POST">
                <input type="submit" class="submitbutton" name="disable2fa" value="Disable 2FA">
            </form>
            <?php

        } else {
            ?>
            <form method="POST">
                <p>Enable 2FA authentication</p>
                <input type="submit" class="submitbutton" name="enable2fa" value="Enable 2FA QR code">
                <br>
            </form>
            <?php
            if (isset($qrCodeUrl)) {
                echo "<p>Scan the QR code with your authenticator app & enter the code from your authenticator app to confirm.</p>";
                echo "<img src='$qrCodeUrl'>";
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enable2fa'])) { ?>
                <form method="POST">
                    <label for="verification_code">Enter verification code:</label>
                    <input type="text" name="verification_code" id="verification_code">
                </form>
                <?php
                $false = 0;
                $disable2fa = $conn->prepare("UPDATE users SET 2FAstatus = ?, 2FAsecret = ? WHERE email = ?");
                $disable2fa->bind_param("iss", $false, $false, $_SESSION['email']);
                $disable2fa->execute();
                $disable2fa->close();
            }
        }
        ?>
    </div>
    <script>
        if (window.innerWidth < 768) {
            $(".innavbar").hide();
        }
    </script>
</body>

</html>