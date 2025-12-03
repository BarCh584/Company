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
    createsettingsnavbar('settings.privacy');
    ?>
    <div class="normalcontentnavbar">
        <script src="../Libraries/jquery/jquery-3.6.0.min.js"></script>
        <script>
            if (window.innerWidth < 768) {
                $(".innavbar").hide();
            }
        </script>
    </div>
    </div>
</body>

</html>