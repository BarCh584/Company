<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../Logo2.png">
</head>

<body>
    <div class="container">
        <h1 class="title">
            Login into your account
        </h1>
        <form method="POST" class="form" action="">
            <input type="text" class="textinpfld" name="emailadd" placeholder="example@email.com" required
                autocomplete="email"><br>
            <input type="password" class="textinpfld" name="pswrd" minlength="8" placeholder="Password" required
                autocomplete="current-password"><br>
            <?php
            if (!isset($_COOKIE['email']) && !isset($_COOKIE['pswrd'])) {
                
                echo "
                <label>Stay sign-in</label>
                <input type='checkbox' name='staysignin' value='staysignin'>";
            }
            ?>
            <input type="submit" name="submitbutton" class="submitbutton">
        </form>
        <br>
        <div class="line">
            <p>Don't have an account?</p>
            <a href="signup.php">
                Sign up
            </a>
        </div><br>
        <div class="line">
            <a href="passwordresetemail.php">
                Forgot password?
            </a>
        </div>
        <br>
        <br>
        <h2>Or sign in with: </h2>
        <form method="POST">
            <button type="submit" name="googlesignin" id="googlesignin" class="googlesignin">Sign in with
                Google</button>
        </form>
        <br>
        <button class="facebookesignin">Sign in with Facebook</button>
    </div>
    </div>

</body>

<?php
error_reporting(E_ALL);
session_start();
$servername = "localhost";
$dbusername = "root";
$password = "";
$dbname = "Company";
if (isset($_POST['emailadd']) && isset($_POST['pswrd'])) {
    $conn = new mysqli($servername, $dbusername, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: {$conn->connect_error}");
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST['emailadd']) || empty($_POST['pswrd'])) {
            echo "Please fill in all fields";
            session_abort();
            exit();
        } else {
            $email = $conn->real_escape_string($_POST['emailadd']);
            $pswrd = $conn->real_escape_string($_POST['pswrd']);
            $stmt = $conn->prepare("SELECT username, id, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($username, $id, $hashedpassword);
                $stmt->fetch();
                if (password_verify($pswrd, $hashedpassword)) {
                    $_SESSION['email'] = $email;
                    $_SESSION['pswrd'] = $pswrd;
                    $_SESSION['id'] = $id;
                    $_SESSION['username'] = $username;
                    // Check if the use is not banned
                    $banstmt = $conn->prepare("SELECT * FROM penalties WHERE userid = ? AND penalty = 'ban'");
                    $banstmt->bind_param("i", $id);
                    $banstmt->execute();
                    $banstmt->store_result();
                    if ($banstmt->num_rows > 0) {
                        echo "<script>alert('You are banned from AccessFrame');</script>";
                        session_abort();
                        exit();
                    }
                    // Check if user has 2FA enabled
                    $twoFAstmt = $conn->prepare("SELECT 2FAstatus FROM users WHERE email = ?");
                    $twoFAstmt->bind_param("s", $email);
                    $twoFAstmt->execute();
                    $twoFAstmt->store_result();
                    $twoFAstmt->bind_result($twoFAstatus);
                    $twoFAstmt->fetch();
                    if ($twoFAstatus == 1) {
                        header("Location: 2FA.php");
                    } else {
                        header("Location: startpage.php");
                    }
                } else {
                    echo "<script>alert('Incorrect password');</script>";
                }
            } else {
                echo "<script>alert('Incorrect email');</script>";
            }
        }
    } else {
        $conn->close();
    }
}
// Google sign in and save email and password in cookies
if (isset($_POST['googlesignin'])) {
    include_once("main.php");
    // Save email and password in cookies
    if (isset($_POST['staysignin'])) {
        setcookie("email", $_SESSION['email'], time() + (86400 * 30), "/");
        setcookie("pswrd", $_SESSION['pswrd'], time() + (86400 * 30), "/");
    }
}
// Normal sign in and save email and password in cookies
if (isset($_POST['emailadd']) && isset($_POST['pswrd']) && isset($_POST['submitbutton'])) {
    // Save email and password in cookies
    if (isset($_POST['staysignin'])) {
        setcookie("email", $_POST['emailadd'], time() + (86400 * 30), "/");
        setcookie("pswrd", $_POST['pswrd'], time() + (86400 * 30), "/");
        echo "<script>console.log('Cookies set');</script>";
    }
}

// Automatically fill in the login form if cookies are set
if (isset($_COOKIE['email']) && isset($_COOKIE['pswrd'])) {
    echo "<script>
        document.querySelector('input[name=\"emailadd\"]').value = '{$_COOKIE['email']}';
        document.querySelector('input[name=\"pswrd\"]').value = '{$_COOKIE['pswrd']}';
        console.log('Cookies set');
    </script>";
}
?>

</html>