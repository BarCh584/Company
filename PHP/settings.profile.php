<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>

<body>
    <div class="normalcontentnavbar">
        <?php
        include_once '../Libraries/navbar.php';
        createnavbar("settings.profile");
        form();
        function form()
        {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "Company";
            $conn = new mysqli($servername, $username, $password, $dbname);
            $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $sql->bind_param("s", $_SESSION['email']);
            $sql->execute();
            $result = $sql->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
            <form class='form' method='POST'>
                <div class='content' style='margin-left: 15vw'>
                    <h1>Account</h1>
                    <input type='text' name='username' class='twotextinpfld' placeholder='Username: {$row['username']}' value='{$row['username']}'><br>
                    <h3>Reset Password</h3>
                    <input type='password' name='password' class='twotextinpfld' placeholder='Reset Password' minlength='8'><br>
                    <input type='password' name='confirmpassword' class='twotextinpfld' placeholder='Confirm Password' minlength='8'><br>
                    <input type='submit' class='submitbutton' value='Save'>
                </div>
            </form>";
                }
            }
            $conn->close();
        }
        ?>
    </div>
</body>

</html>

<?php
if (!isset($_SESSION['email'])) {
    die("User not logged in.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Company";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["password"]) && isset($_POST["confirmpassword"])) {
        if ($_POST["password"] === $_POST["confirmpassword"]) {
            $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $pswrdstmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $pswrdstmt->bind_param("ss", $hashed_password, $_SESSION['email']);
            $hashedpassword = $hashed_password;
            $pswrdstmt->execute();
            $pswrdstmt->close();
            header("Refresh:0"); // Refresh the page to initialize the new language without get parameters in the URL to prevent looping
        } else {
            echo "<script>alert('Passwords do not match');</script>";
        }
    }
    if (isset($_POST["username"])) {
        $stmt = $conn->prepare("UPDATE users SET username=? WHERE email=?");
        $stmt->bind_param("ss", $_POST["username"], $_SESSION['email']);
        print ($_POST["username"]);
        $_SESSION["username"] = $_POST["username"];
        $stmt->execute();
        $stmt->close();
        header("Refresh:0"); // Refresh the page to initialize the new language without get parameters in the URL to prevent looping
    }
}


?>