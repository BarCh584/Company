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
        createnavbar();
        ?>
            <ul class="outnavbar" style="margin-left: 15vw; width: 20vw; border-right: 1px solid gray; border-left: 1px solid gray;">
                <li><a href="settings.profile.php">Profile</a></li>
                <li><a href="settings.paymentinformation.php">Payment & finances</a></li>
                <li><a href="settings.preferences.php">Preferences</a></li>
                <li><a href="settings.languages.php">Language</a></li>
                <li><a href="settings.privacy.php">Privacy</a></li>
            </ul>
        </div>
    </div>
</body>

</html>