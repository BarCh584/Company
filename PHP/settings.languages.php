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
        createsettingsnavbar('settings.languages');
        ?>
        <div class="normalcontentnavbar">
            <form action="settings.preferences.php" method="post">
                <br>
                <div class="contentnavbar">
                <h1>Change your Language:</h1>
                    <li><a href="?lang=en" value="english">English</a></li>
                    <li><a href="?lang=de" value="german">Deutsch</a></li>
                    <li><a href="?lang=fr" value="french">Français</a></li>
                    <li><a href="?lang=es" value="spanish">Español</a></li>
                    <li><a href="?lang=ru" value="russian">Русский</a></li>
                    <li><a href="?lang=po" value="portuguese">Português</a></li>
                    <li><a href="?lang=it" value="italian">Italiano</a></li>
                    <li><a href="?lang=in" value="indonesian">Indonesian</a></li>
                </div>
                </select>
        </div>
    </div>
    <script>
        if (window.innerWidth < 768) {
            $(".innavbar").hide();
        }
        </script>
    <?php
    if (isset($_GET['lang'])) {
        updatelang($_GET['lang']);
    }
    function updatelang($lang)
    {
        print ($lang);
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $langstmt = $conn->prepare("UPDATE users SET language=? WHERE username=?");
        $langstmt->bind_param("ss", $lang, $_SESSION['username']);
        $langstmt->execute();
        header("Location: startpage.php"); // Refresh the page to initialize the new language without get parameters in the URL to prevent looping
    }

    ?>
</body>

</html>