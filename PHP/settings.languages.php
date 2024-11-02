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
        createsettingsnavbar("settings.languages");
        ?>
        <div class="content">
            <form action="settings.preferences.php" method="post">
                <h1>Change your Language:</h1>
                <br>
                <div class="contentnavbar">
                    <li><a href="eng" value="english">English</a></li>
                    <li><a href="deu" value="german">Deutsch</a></li>
                    <li><a href="fra" value="french">Français</a></li>
                    <li><a href="esp" value="spanish">Español</a></li>
                    <li><a href="rus" value="russian">Русский</a></li>
                    <li><a href="por" value="portuguese">Português</a></li>
                    <li><a href="ita" value="italian">Italiano</a></li>
                    <li><a href="ind" value="indonesian">Indonesian</a></li>
                    </div>
                </select>
        </div>
    </div>
</body>

</html>