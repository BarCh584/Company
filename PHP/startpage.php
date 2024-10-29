<?php
ini_set('session.gc_maxlifetime', 86000);
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
<?php
    include_once('../Libraries/navbar.php');
    createnavbar();
?>
    <?php
    /*$servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Company";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT id, email, username, password FROM users WHERE username = '" . $_SESSION['username'] . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {

    }*/

    ?>
</body>

</html>