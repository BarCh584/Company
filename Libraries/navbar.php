<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    function createnavbar()
    {
        ?>
        <nav class="outnavbar">
            <ul style="list-style-type: none; padding: 0;">
                <li><a href="startpage.php"><img src="../Images/Navbar/home.png" alt="Logo"
                            style="width: 5vw; height: 5vh;"></a></li>
                <li><a href="search.php"><img src="../Images/Navbar/search.png" alt="Logo"
                            style="width: 5vw; height: 5vh;"></a></li>
                <li><a href="add.php"><img src="../Images/Navbar/add.png" alt="Logo" style="width: 5vw; height: 5vh;"></a>
                </li>
                <li><a href="settings.php"><img src="../Images/Navbar/Settings.png" alt="Logo"
                            style="width: 5vw; height: 5vh;"></a></li>
                <li><a href="message.php"><img src="../Images/Navbar/message.png" alt="Logo"
                            style="width:5vw; height: 5vh"></a></li>
                <li><a href="account.php"><img src="../Images/Navbar/user.png" alt="Logo"
                            style="width: 5vw; height: 5vh"></a></li>
            </ul>
        </nav>
        <?php
    }
    ?>
</body>

</html>