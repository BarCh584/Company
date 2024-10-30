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
        ?>
        <?php
                include_once('../Libraries/navbar.php');
                createsettingsnavbar("settings.subscriptions");
        ?>
        </div>
    </div>
</body>

</html>