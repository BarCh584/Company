<?php
session_start();
if (!isset($_SESSION['email'])) {
    echo "<script>alert('You are not logged in');</script>";
    exit();
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
    $firstname = $conn->real_escape_string($_POST["firstname"]);
    $lastname = $conn->real_escape_string($_POST["lastname"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $telnumber = $conn->real_escape_string($_POST["telnumber"]);
    $streetaddress = $conn->real_escape_string($_POST["streetaddress"]);
    $streetaddress2 = $conn->real_escape_string($_POST["streetaddress2"]);
    $city = $conn->real_escape_string($_POST["city"]);
    $stateorprovince = $conn->real_escape_string($_POST["stateorprovince"]);
    $postalorzipcode = $conn->real_escape_string($_POST["postalorzipcode"]);

    $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, email=?, telnumber=?, streetaddress=?, streetaddress2=?, city=?, stateorprovince=?, postalorzipcode=? WHERE email=?");
    $stmt->bind_param("ssssssssss", $firstname, $lastname, $email, $telnumber, $streetaddress, $streetaddress2, $city, $stateorprovince, $postalorzipcode, $_SESSION['email']);

    if ($stmt->execute()) {
        echo "<script>alert('Account updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating your account, please contact the support team');</script>";
    }
    $stmt->close();
}

$sql = "SELECT * FROM users WHERE email = '" . $_SESSION['email'] . "'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css">
    <title>Document</title>
</head>
<body>
<div class="container">
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("settings.profile");
    createsettingsnavbar("settings.profile");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "
            <form class='form' method='POST'>
                <div class='content' style='margin-left: 15vw'>
                    <h1>Account</h1>
                    <h3>Username: {$row['username']}</h3>
                    <h3>Email-address is: {$row['email']}</h3>
                    <input type='text' name='firstname' class='twotextinpfld' placeholder='First name' value='{$row['firstname']}'>
                    <input type='text' name='lastname' class='twotextinpfld' placeholder='Last name' value='{$row['lastname']}'>
                    <br>
                    <input type='text' name='email' class='twotextinpfld' placeholder='example@email.com' value='{$row['email']}'>
                    <input type='tel' name='telnumber' class='twotextinpfld' placeholder='Telephone number' value='{$row['telnumber']}'>
                    <br>
                    <input type='text' name='streetaddress' class='textinpfld' placeholder='Street address' value='{$row['streetaddress']}'>
                    <br>
                    <input type='text' name='streetaddress2' class='textinpfld' placeholder='Street address line 2' value='{$row['streetaddress2']}'>
                    <br>
                    <input type='text' name='city' class='textinpfld' placeholder='City' value='{$row['city']}'>
                    <br>
                    <input type='text' name='stateorprovince' class='twotextinpfld' placeholder='State/Province' value='{$row['stateorprovince']}'>
                    <input type='text' name='postalorzipcode' class='twotextinpfld' placeholder='Postal/Zip code' value='{$row['postalorzipcode']}'><br>
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
