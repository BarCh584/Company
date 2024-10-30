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
    function createnavbar($buttontohighlight)
    {
        ?>
        <ul class="outnavbar">
            <li><a <?php if($buttontohighlight == "startpage") print ("class='active'"); ?> href="startpage.php"><img src="../Images/Navbar/home.png" alt="Logo">Home</a></li>
            <li><a <?php if($buttontohighlight == "search") print ("class='active'"); ?> href="search.php"><img src="../Images/Navbar/search.png" alt="Logo">Search</a></li>
            <li><a <?php if($buttontohighlight == "add") print ("class='active'"); ?> href="add.php"><img src="../Images/Navbar/add.png" alt="Logo">Add</a></li>
            <li><a <?php if($buttontohighlight == "settings.profile") print ("class='active'"); ?> href="settings.profile.php"><img src="../Images/Navbar/Settings.png" alt="Logo">Settings</a></li>
            <li><a <?php if($buttontohighlight == "message") print ("class='active'"); ?> href="message.php"><img src="../Images/Navbar/message.png" alt="Logo">Messages</a></li>
            <li><a <?php if($buttontohighlight == "account") print ("class='active'"); ?> href="account.php"><img src="../Images/Navbar/user.png" alt="Logo">Account</a></li>
        </ul>



        <?php
    }
    ?>


    <?php
    function createsettingsnavbar($buttontohighlightin) {
        ?>
    
    <ul class="outnavbar" id="innavbar" style="margin-left: 15vw; width: 25vw; border-right: 1px solid gray; border-left: 1px solid gray;">
                <li><a <?php if($buttontohighlightin == "settings.profile") print("class='active'");?>class="item" href="settings.profile.php">Profile</a></li>
                <li><a <?php if($buttontohighlightin == "settings.notifications") print("class='active'");?>class="item" href="settings.notifications.php">Notifications</a></li>
                <li><a <?php if($buttontohighlightin == "settings.privacy") print("class='active'");?>class="item" href="settings.privacy.php">Privacy</a></li>
                <li><a <?php if($buttontohighlightin == "settings.subscriptions") print("class='active'");?>class="item" href="settings.subscriptions.php">Subscriptions</a></li>
                <li><a <?php if($buttontohighlightin == "settings.paymentinformation") print("class='active'");?>class="item" href="settings.paymentinformation.php">Payment & finances</a></li>
                <li><a <?php if($buttontohighlightin == "settings.preferences") print("class='active'");?>class="item" href="settings.preferences.php">Preferences</a></li>
                <li><a <?php if($buttontohighlightin == "settings.languages") print("class='active'");?>class="item" href="settings.languages.php">Language</a></li>
            </ul>
    <?php
    }
    
    ?>
    <style>
        ul.outnavbar>li>a {
            color: white;
            display: flex;
            text-decoration: none;
        }

        ul#innavbar>li>a {
            text-align: left;
            justify-content: space-between;
            border-radius: 0.75rem;
        }
        ul#innavbar>li>a.item {
            padding-top: 2vh;
            padding-bottom: 2vh;
            padding-left: 2vw;
            padding-right: 2vw;
        }
        ul#innavbar>li>a.item:hover {
            background-color: #3f3f3f;
        }
        ul.outnavbar {
            width: 15vw;
            list-style-type: none;
            position: fixed;
            margin: 0;
            padding: 0;
            height: 100vh;
            /* Ensure the ul stretches across the entire height */
        }
        /*ul.outnavbar li a:hover, ul.outnavbar li a::selection {
            filter: brightness(0) saturate(100%) invert(88%) sepia(12%) saturate(771%) hue-rotate(11deg) brightness(106%) contrast(91%);
        }*/

        ul.outnavbar>li:hover {
            background-color: rgb(64, 64, 64);
        }

        .outnavbar a img {
            margin-top: 1vh;
            margin-bottom: 1vh;
            width: 5vw;
            height: 5vh;
        }

        .outnavbar {
            left: 0vw;
            top: 0vh;
            /* Ensure the navbar container stretches across the entire height */
            display: flex;
            flex-direction: column;
            /* Distribute items evenly */
            height: 100vh;
            background-color: #282828;
            transition: transform 5s ease;
        }

        .outnavbar li {
            /*Make each list item take up equal space */
            background-color: #282828;
        }
        .active {
            filter: brightness(0) saturate(100%) invert(88%) sepia(12%) saturate(771%) hue-rotate(11deg) brightness(106%) contrast(91%);
        }
        ul#innavbar>li>a.active {
            padding-top: 2vh;
            padding-bottom: 2vh;
            padding-left: 2vw;
            padding-right: 2vw;
        }
    </style>
</body>

</html>