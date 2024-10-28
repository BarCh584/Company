<?php
ini_set('session.gc_maxlifetime', 3600);
session_start();
?>

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
    <div id="grid">
        <h1>
            Sign up
        </h1>
        <form method="POST">
            <input type="text" name="username" class="textinpfld" placeholder="username"><br>
            <input type="email" name="email" class="textinpfld" placeholder="email"><br>
            <input type="password" name="password" class="textinpfld" placeholder="password"><br>
            <input type="password" name="confirmpassword" class="textinpfld" placeholder="confirm password"><br>
            <input type="submit" name="submit" class="submitbutton">
        </form>
        <br>
        <a href="index.php">
            Or log-in
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
    $serverusername = "root";
    $serverpassword = "";
    $serverdbname = "Company";
    $conn = new mysqli($servername, $serverusername, $serverpassword, $serverdbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['username'] = $conn->real_escape_string($_POST['username']);
        $username = $_SESSION['username'];
        $_SESSION['email'] = $conn->real_escape_string($_POST['email']);
        $email = $_SESSION['email'];
        $_SESSION['password'] = $conn->real_escape_string($_POST['password']);
        $password = $_SESSION['password'];
        $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedpassword);
        $emailexists = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $emailexists->bind_param("s", $email);
        $emailexists->execute();
        $emailexists->store_result();
        if ($emailexists->num_rows > 0) {
            echo "Email already exists";
        } else {
            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }

    $conn->close();
    ?>
</body>

</html>