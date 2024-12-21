<?php
require_once "../Libraries/vendor/autoload.php";

use Appwrite\Client;
use Appwrite\Services\Account;

// Initialize Appwrite Client
$client = new Client();
$client
    ->setEndpoint('https://cloud.appwrite.io/v1') // Replace with your Appwrite endpoint
    ->setProject('accessframe');             // Replace with your Appwrite project ID

$account = new Account($client);
$googleLoginUrl = "https://cloud.appwrite.io/v1/account/sessions/oauth2/google?project=accessframe";
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["googlesubmit"])) {
    try {
        $user = $account->get();
        if ($user) {
            // Store user data in session
            $_SESSION['userId'] = $user['$id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
    
            // Redirect to the dashboard
            header("Location: startpage.php");
            exit();
        }
    } catch (Exception $e) {
        // Redirect to Google OAuth login if session doesn't exist
        $googleLoginUrl = "https://cloud.appwrite.io/v1/account/sessions/oauth2/google?project=accessframe";
        header("Location: $googleLoginUrl");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
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
            <label>Stay sign-in</label>
            <input type="checkbox" value="staysignin">
            <input type="submit" class="submitbutton">
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
            <button type="submit" name="googlesubmit" class="googlesignin">Sign in with Google</button>
        </form>
        <br>
        <button class="facebookesignin">Sign in with Facebook</button>
    </div>
    </div>
</body>

<?php
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
                    header("Location: startpage.php");
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
?>

</html>