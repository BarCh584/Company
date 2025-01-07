<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
    <link rel="icon" href="../Logo.png">
</head>

<body>

    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("settings.profile");
    createsettingsnavbar('settings.subscriptions'); ?>
    <!--jquery-->
    <div class="normalcontentnavbar">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>
            if (window.innerWidth < 768) {
                $(".innavbar").hide();
            }
        </script>

        <div class="contentnavbar">
            <?php
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
                    echo "<p>Subscribed to creator: " . $row['creator'] . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</body>

</html>