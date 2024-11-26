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
    $stmt = $conn->prepare("UPDATE users SET email=?, username=? WHERE email=?");
    $stmt->bind_param("ssssssssss", $_SESSION["email"], $_SESSION["username"], $_SESSION['email']);

    if ($stmt->execute()) {
        echo "<script>alert('Account updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating your account, please contact the support team');</script>";
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <?php
        include_once '../Libraries/navbar.php';
        createnavbar("settings.profile");
        createsettingsnavbar("settings.profile");
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
                    <input type='text' name='username' class='twotextinpfld' placeholder='Username: {$row['username']}' value='{$row['username']}'></h3><br>
                    <input type='text' name='email' class='twotextinpfld' placeholder='example@email.com' value='{$row['email']}'><br>
                    <input type='submit' class='submitbutton' value='Save'>
                </div>
            </form>";
            }
        }
        $conn->close();
        ?>
    </div>
</body>

</html>