<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>

<body>
    <?php
    session_start();
    include("translation.php");
    function createnavbar($buttontohighlight)
    {
        ?>
        <ul class="outnavbar">
            <li><a <?php if ($buttontohighlight == "startpage")
                print ("class='active startpage'");
            else
                print ("class='not-active'"); ?> href="startpage.php">
                    <img src="../Images/Navbar/hollow/home.png" alt="Logo">
                    <?php t("Home"); ?></a></li>
            <li><a <?php if ($buttontohighlight == "search")
                print ("class='active search'");
            else
                print ("class='not-active'"); ?> href="search.php">
                    <img src="../Images/Navbar/hollow/search.png" alt="Logo">
                    <?php t("Search"); ?></a></li>
            <li><a <?php if ($buttontohighlight == "add")
                print ("class='active add'");
            else
                print ("class='not-active'"); ?>
                    href="add.php">
                    <img src="../Images/Navbar/hollow/add.png" alt="Logo">
                    <?php t("Add"); ?></a></li>
            <li><a <?php if ($buttontohighlight == "settings.profile")
                print ("class='active settings-profile'");
            else
                print ("class='not-active'"); ?> href="settings.profile.php">
                    <img src="../Images/Navbar/hollow/user.png" alt="Logo">
                    <?php t("Account"); ?></a></li>
            <li><a <?php if ($buttontohighlight == "message")
                print ("class='active message'");
            else
                print ("class='not-active'"); ?> href="message.php">
                    <img src="../Images/Navbar/hollow/message.png" alt="Logo">
                    <?php t("Messages"); ?></a></li>
            <li><a <?php if ($buttontohighlight == "live-stream")
                print ("class='active live-stream'");
            else
                print ("class='not-active'"); ?> href="live-stream.php">
                    <img src="../Images/Navbar/hollow/live-streaming.png" alt="Logo">
                    <?php t("Livestream"); ?></a></li>
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
                print ("class='active settings-profile'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.profile.php"><img
                        src="../Images/Navbar/hollow/user.png" alt="Logo"><?php t("Account details"); ?></a></li>
            <li><a <?php if ($buttontohighlightin == "settings.subscriptions")
                print ("class='active settings-subscriptions'");
            else
                print ("class='not-active'"); ?>class="item"
                    href="settings.subscriptions.php"><img src="../Images/Navbar/hollow/subscription.png"
                        alt="Logo">Subscriptions</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.paymentinformationpaypal")
                print ("class='active settings-paymentinformationpaypal'");
            else
                print ("class='not-active'"); ?>class="item"
                    href="settings.paymentinformationpaypal.php"><img src="../Images/Navbar/hollow/wallet.png"
                        alt="Logo">Payment & finances</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.preferences")
                print ("class='active settings-preferences'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.preferences.php"><img
                        src="../Images/Navbar/hollow/preferences.png" alt="Logo">Preferences</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.languages")
                print ("class='active settings-languages'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.languages.php"><img
                        src="../Images/Navbar/hollow/language.png" alt="Logo">Language</a></li>
            <li><a <?php if ($buttontohighlightin == "settings.about")
                print ("class='active settings-about'");
            else
                print ("class='not-active'"); ?>class="item" href="settings.about.php"><img
                        src="../Images/Navbar/hollow/link.png" alt="Logo">About</a></li>
        </ul>
        <?php
    }

    ?>

</body>

</html>