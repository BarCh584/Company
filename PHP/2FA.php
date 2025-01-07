<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../Logo.png">
</head>

<body>
    <div class="normalcontentnavbar">
        <h1>2FA</h1>
        <form method="POST" class="form">
            <input type="text" class="textinpfld" name="code" placeholder="123456" required><br>
            <input type="submit" class="submitbutton">
        </form>
    </div>
    <?php

    require "../Libraries/vendor/autoload.php";
    session_start();
    $servername = "localhost";
    $dbusername = "root";
    $password = "";
    $dbname = "Company";
    $conn = new mysqli($servername, $dbusername, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: {$conn->connect_error}");
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['code'])) {
        $authenticator = new PHPGangsta_GoogleAuthenticator();
        $twoFAstmt = $conn->prepare("SELECT 2FAsecret FROM users WHERE username = ?");
        $twoFAstmt->bind_param("s", $_SESSION['username']);
        $twoFAstmt->execute();
        $twoFAstmt->store_result();
        $twoFAstmt->bind_result($secretcode);
        $twoFAstmt->fetch();
        $_SESSION["secretcode"] = $secretcode;
        $code = $_POST['code'];
        $checkResult = $authenticator->verifyCode($_SESSION["secretcode"], $code, 2);
        if ($checkResult) {
            header("Location: startpage.php");
        } else {
            echo "<p>Wrong code, please type in the correct code</p>";
        }
    }
    ?>
</body>

</html>