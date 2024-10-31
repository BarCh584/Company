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
            <li><a <?php if ($buttontohighlight == "startpage")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>
                    href="startpage.php"><img src="../Images/Navbar/home.png" alt="Logo">Home</a></li>
            <li><a <?php if ($buttontohighlight == "search")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>
                    href="search.php"><img src="../Images/Navbar/search.png" alt="Logo">Search</a></li>
            <li><a <?php if ($buttontohighlight == "add")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>
                    href="add.php"><img src="../Images/Navbar/add.png" alt="Logo">Add</a></li>
            <li><a <?php if ($buttontohighlight == "settings.profile")
                print ("class='active'");
            else
                print ("class='not-active'"); ?> href="settings.profile.php"><img src="../Images/Navbar/user.png"
                        alt="Logo">Account</a></li>
            <li><a <?php if ($buttontohighlight == "message")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>
                    href="message.php"><img src="../Images/Navbar/message.png" alt="Logo">Messages</a></li>
        </ul>
        <?php
    }
    ?>


    <?php
    function createsettingsnavbar($buttontohighlightin)
    {
        ?>

        <ul class="outnavbar" id="innavbar"
            style="margin-left: 15vw; width: 25vw; border-right: 1px solid gray; border-left: 1px solid gray;">
            <li><a <?php if ($buttontohighlightin == "settings.profile")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.profile.php"><img
                        src="../Images/Navbar/profile.png" alt="Logo">Profile</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.notifications")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.notifications.php"><img
                        src="../Images/Navbar/notification.png" alt="Logo">Notifications</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.privacy")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.privacy.php"><img
                        src="../Images/Navbar/privacy.png" alt="Logo">Privacy</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.subscriptions")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.subscriptions.php"><img
                        src="../Images/Navbar/subscription.png" alt="Logo">Subscriptions</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.paymentinformation")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.paymentinformation.php"><img
                        src="../Images/Navbar/wallet.png" alt="Logo">Payment & finances</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.preferences")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.preferences.php"><img
                        src="../Images/Navbar/preferences.png" alt="Logo">Preferences</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.languages")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.languages.php"><img
                        src="../Images/Navbar/language.png" alt="Logo">Language</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.about")
                print ("class='active'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.about.php"><img
                        src="../Images/Navbar/link.png" alt="Logo">About</a></li>
        </ul>
        <?php
    }

    ?>
    <style>
        ul.outnavbar>li>a {
            color: white;
            display: flex;
            text-decoration: none;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Colors and font for outnavbar */


        ul#innavbar>li>a.item:hover {
            background-color: #3f3f3f;
        }

        /* hovering in innavbar */

        ul.outnavbar>li:hover {
            background-color: rgb(64, 64, 64);
        }

        /* hovering in outnavbar */
        ul.outnavbar {
            width: 15vw;
            list-style-type: none;
            position: fixed;
            margin: 0;
            padding: 0;
            height: 100vh;
            /* Ensure the ul stretches across the entire height */
        }


        .outnavbar a img {
            margin-top: 2vh;
            margin-bottom: 2vh;
            width: 5vw;
            height: 5vh;
        }

        /* correct image resizing and more stretching between elements*/

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

        .active {
            filter: brightness(0) saturate(100%) invert(88%) sepia(12%) saturate(771%) hue-rotate(11deg) brightness(106%) contrast(91%);
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* highlight selected navbar element */
        .not-active {
            filter: brightness(0) saturate(100%) invert(100%) sepia(4%) saturate(643%) hue-rotate(272deg) brightness(114%) contrast(100%);
            font-weight: normal;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* unhighlight selected navbar element */
    </style>
</body>

</html>