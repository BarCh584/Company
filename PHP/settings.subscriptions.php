<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
    <link rel="icon" href="../Logo2.png">
</head>

<body>

    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("settings.profile");
    createsettingsnavbar('settings.subscriptions');
    include_once("../Libraries/currency_converter.php");
 ?>
    <!--jquery-->
    <div class="innormalcontentnavbar">
        <script src="../Libraries/jquery/jquery-3.6.0.min.js"></script>
        <script>
            if (window.innerWidth < 768) {
                $(".innavbar").hide();
            }
        </script>

            <h1>Subscribed to:</h1>
            <?php
            /* Show subscriptions */
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "Company";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $displaysubscriptionsstmt = $conn->prepare("SELECT * FROM subscriptions WHERE subscriber = ?");
            $displaysubscriptionsstmt->bind_param("s", $_SESSION['username']);
            $displaysubscriptionsstmt->execute();
            $displaysubscriptionsstmtresult = $displaysubscriptionsstmt->get_result();
            if ($displaysubscriptionsstmtresult->num_rows == 0) {
                die("<div class='subscription'><p>You are not subscribed to any creators</p></div>");
            }
            if ($displaysubscriptionsstmtresult->num_rows > 0) {
                while ($row = $displaysubscriptionsstmtresult->fetch_assoc()) {
                    echo "<div class='subscription'>";
                    echo "<p>" . $row['creator'] . "  <small>since {$row['createdat']}</small></p>";
                    echo "</div>";
                }
            }

            // Show payments
            $paymentstmt = $conn->prepare("SELECT * FROM monthlypaymentchart WHERE subscriber = ?");
            $paymentstmt->bind_param("s", $_SESSION["username"]);
            $paymentstmt->execute();
            $paymentstmtrslt = $paymentstmt->get_result();
            if ($paymentstmtrslt->num_rows == 0) {
                die("<div class='subscriptions><p>No payments made yet</p></div>'");
            } else { ?>
                <table>
                    <tr>
                        <th><p>Descriptions</p></th>
                        <th><p>Payment</p></th>
                        <th><p>Status</p></th>
                    </tr>
                    <?php while ($row = $paymentstmtrslt->fetch_assoc()) {
                        $convertedamopunt = getexchangerate($row["creator"], $_SESSION["username"]);
                        ?>
                        <tr>
                            <td><p>Contract fee<br><small> <?=$row["createdat"]?></small></p></td>
                            <td><p><?= $convertedamopunt ?></p></td>
                            <td><p><?=$row["status"]?></p></td>
                        </tr>
                        <?php

                    }
                echo "</table>";
            }
            ?>
    </div>
</body>

</html>