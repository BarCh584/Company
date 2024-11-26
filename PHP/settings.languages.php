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
        include_once('../Libraries/navbar.php');
        createnavbar("settings.profile");
        createsettingsnavbar("settings.languages");
        ?>
        <div class="content">
            <form action="settings.preferences.php" method="post">
                <h1>Change your Language:</h1>
                <br>
                <div class="contentnavbar">
                    <li><a href="<?php updatelang("en"); ?>" value="english">English</a></li>
                    <li><a href="<?php updatelang("de"); ?>" value="german">Deutsch</a></li>
                    <li><a href="<?php updatelang("fr"); ?>" value="french">Français</a></li>
                    <li><a href="<?php updatelang("es"); ?>" value="spanish">Español</a></li>
                    <li><a href="<?php updatelang("ru"); ?>" value="russian">Русский</a></li>
                    <li><a href="<?php updatelang("po"); ?>" value="portuguese">Português</a></li>
                    <li><a href="<?php updatelang("it"); ?>" value="italian">Italiano</a></li>
                    <li><a href="<?php updatelang("in"); ?>" value="indonesian">Indonesian</a></li>
                </div>
                </select>
        </div>
    </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        function updatelang($lang)
        {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "Company";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $langstmt = $conn->prepare("UPDATE users SET language=? WHERE username=?");
            $langstmt->bind_param("ss", $_POST['language'], $_SESSION['username']);
        }
    }
    ?>
</body>

</html>