<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("" . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $creditcard = $conn->real_escape_string($_POST['creditcard']);
    $cardname = $conn->real_escape_string($_POST['cardname']);
    $cvc = $conn->real_escape_string($_POST['cvc']);
    $stmt = $conn->prepare("INSERT INTO paymentinformation (creditcard, cardname, cvc) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $creditcard, $cardname, $cvc);
    if ($stmt->execute()) {
        echo "New record created successfully";
        header("Location: settings.php");
        $stmt->close();
        $conn->close();
    } else {
        echo "Error: " . $stmt->error;
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
    <script src="../script.js"></script>
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="paymentform">
            <a href="settings.paymentinformationcreditcard.php"><button class="paymentbuttonactive paymentchoicebutton"><img
                        src="../Images/paymentinfos/credit-card.png"></button></a>
            <a href="settings.paymentinformationpaypal.php"><button class=" paymentchoicebutton"><img
                        src="../Images/paymentinfos/paypal.png"></button></a>
        </div>
        <?php
        include_once('../Libraries/navbar.php');
        include_once('../Libraries/paymentform.php');
        createnavbar("settings.profile");
        createsettingsnavbar("settings.paymentinformationcreditcard");
        creditcardform();

        ?>

    </div>
</body>

</html>