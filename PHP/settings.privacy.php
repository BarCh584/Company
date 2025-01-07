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
    <div class="normalcontentnavbar">
        <?php
        include_once('../Libraries/navbar.php');
        createnavbar("settings.profile");
        createsettingsnavbar('settings.privacy');
        ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>
            if (window.innerWidth < 768) {
                $(".innavbar").hide();
            }
        </script>
    </div>
    </div>
</body>

</html>