<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("search");
    ?>
    <form method="POST" class="content">
        <input type="text" class="textinpfld" placeholder="Search for a username" name="username">
        <input type="submit" name="submit" value="Search" class="submitbutton">
    </form>
    <?php
    session_start();
    if (!isset($_SESSION['id'])) {
        die("You must be logged in to post comments.");
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
        if (empty($_POST["username"])) {
            echo "No username entered";
            session_abort();
            exit();
        } else {
            $uname = $_POST["username"];
            $stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE CONCAT('%', ?, '%')");
            $stmt->bind_param("s", $uname);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                print ("<div class='contentnavbar'>");
                while ($row = $result->fetch_assoc()) {
                    print ("<li><a href='search.results.php?username=$row[username]'>Username: " . $row["username"] . "</a></li><br>");
                }
                print ("</div>");
            } else {
                print ("<div class='contentnavbar'>
                     <p>No user found</p>
                     </<div>");
            }
        }
    }



    ?>
</body>

</html>