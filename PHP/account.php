<?php
ini_set('session.gc_maxlifetime', 3600);
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css">
</head>

<body>
    <div class="containter">
        <?php
        include_once('../Libraries/navbar.php');
        createnavbar("account");
        ?>

        <?php


        ?>
    </div>
</body>

</html>