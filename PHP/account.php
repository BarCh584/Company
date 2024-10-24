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
        <nav class="outnavbar">
            <ul style="list-style-type: none; padding: 0;">
                <li><a href="startpage.php"><img src="../Images/Navbar/home.png" alt="Logo"
                            style="width: 5vw; height: 5vh;"></a></li>
                <li><a href="startpage.php"><img src="../Images/Navbar/search.png" alt="Logo"
                            style="width: 5vw; height: 5vh;"></a></li>
                <li><a href="add.php"><img src="../Images/Navbar/add.png" alt="Logo"
                            style="width: 5vw; height: 5vh;"></a></li>
                <li><a href="settings.php"><img src="../Images/Navbar/Settings.png" alt="Logo"
                            style="width: 5vw; height: 5vh;"></a></li>
                <li><a href="message.php"><img src="../Images/Navbar/message.png" alt="Logo"
                            style="width:5vw; height: 5vh"></a></li>
                <li><a href="account.php"><img src="../Images/Navbar/user.png" alt="Logo"
                            style="width: 5vw; height: 5vh"></a></li>
            </ul>
        </nav>

        <?php
        // Check if the session variables exist before using them
        if (isset($_SESSION['username']) && isset($_SESSION['email']) && isset($_SESSION['password'])) {
            $username = $_SESSION['username'];
            $email = $_SESSION['email'];

            // Note: For security, do NOT display or use the plain-text password directly
            // Instead, you can use the hashed password if needed for authentication purposes
            $hashedPassword = $_SESSION['password'];

            echo "<h1>Welcome, $username!</h1>";
            echo "<p>Your email: $email</p>";
            echo "<p>Your password: $hashedPassword</p>";
        } else {
            echo "<p>No session data found. Please log in or sign up first.</p>";
        }

        ?>

        <div class="commentcreate">
        </div>
    </div>
</body>

</html>