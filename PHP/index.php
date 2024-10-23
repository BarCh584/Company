<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css">
    <script src="script.js"></script>
    <title>Document</title>
</head>

<body>
    <div class="index">
        <h1 class="title">
            Login into your account
        </h1>
        <form method="POST" class="form" action="">
            <input type="text" class="textinpfld" name="uname" placeholder="example@email.com" required><br>
            <input type="password" class="textinpfld" name="pswrd" minlength="8" placeholder="Password" required><br>
            <label for="staysignin">Stay sign-in</label>
            <input type="checkbox" value="staysignin">
            <input type="submit" class="submitbutton">
        </form>
        <br>
        <a href="signup.php">
            Sign up
        </a>
        <br>
        <br>
        <h2>Or sign in with: </h2>
        <button class="googlesignin">Sign in with Google</button>
        <br>
        <button class="facebookesignin">Sign in with Facebook</button>
    </div>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Company";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $conn->real_escape_string($_POST['uname']);
        $password = $_POST['pswrd'];
        $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if(password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                echo "Login successful";
            } else {
                echo "Password is incorrect";
            }
        }
    }
    ?>
</body>

</html>