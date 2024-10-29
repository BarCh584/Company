<?php
ini_set('session.gc_maxlifetime', 3600);
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css">
</head>

<body>
    <div class="containter">
        <?php
        include_once('../Libraries/navbar.php');
        createnavbar();
        ?>

        <?php
        // Check if the session variables exist before using them
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT id, email, username, password FROM users WHERE email = '" . $_SESSION['email'] . "'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                print("
                <div class='content'>
                <h1>Account</h1>
                <h3>Username: " . $row['username'] . "</h3>
                <h3>Email-address is:" .  $row['email'] . "</h3>
                </div>
                ");
                
            }
        }

        ?>
    </div>
</body>

</html>