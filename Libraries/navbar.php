<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css">
    <title>Document</title>
</head>

<body>
    <?php
    function createnavbar()
    {
        ?>
        <ul class="outnavbar">
            <li><a href="startpage.php"><img src="../Images/Navbar/home.png" alt="Logo">Home</a></li>
            <li><a href="search.php"><img src="../Images/Navbar/search.png" alt="Logo">Search</a></li>
            <li><a href="add.php"><img src="../Images/Navbar/add.png" alt="Logo">Add</a></li>
            <li><a href="settings.php"><img src="../Images/Navbar/Settings.png" alt="Logo">Settings</a></li>
            <li><a href="message.php"><img src="../Images/Navbar/message.png" alt="Logo">Messages</a></li>
            <li><a href="account.php"><img src="../Images/Navbar/user.png" alt="Logo">Account</a></li>
        </ul>



        <?php
    }
    ?>
    <style>
        ul.outnavbar>li>a {
            color: white;
            text-align: center;
            display: flex;
            text-decoration: none;
        }
        ul.outnavbar {
            width: 15vw;
            list-style-type: none;
            position: fixed;
            margin: 0;
            padding: 0;
            height: 100vh; /* Ensure the ul stretches across the entire height */
        }
        img {
            color: #efe69c;
        }
        img:hover {
            background-color: rgb(64, 64, 64);
        }

        .outnavbar a img {
            width: 5vw;
            height: 5vh;
        }

        .outnavbar {
            left: 0vw;
            top: 0vh;
            height: 100vh; /* Ensure the navbar container stretches across the entire height */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Distribute items evenly */
        }

        .outnavbar li {
            flex-grow: 1; /*Make each list item take up equal space */
            background-color: #24231b;
        }
    </style>
</body>

</html>