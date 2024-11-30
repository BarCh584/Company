<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>

<body>
    <?php
    session_start();
    include("translation.php");
    function createnavbar($buttontohighlight)
    {
        $buttons = [
            "startpage" => ["startpage.php", "home.png", "Home"],
            "search" => ["search.php", "search.png", "Search"],
            "add" => ["add.php", "add.png", "Add"],
            "settings.profile" => ["settings.navbar.php", "user.png", "Account"],
            "message" => ["message.php", "message.png", "Messages"],
            "live-stream" => ["live-stream.php", "live-streaming.png", "Livestream"]
        ];
        ?>
        <ul class="outnavbar">
            <?php foreach ($buttons as $key => $value) { ?>
                <li><a class="<?php echo ($buttontohighlight == $key) ? 'active ' . $key : 'not-active'; ?>" href="<?php echo $value[0]; ?>">
                    <img src="../Images/Navbar/hollow/<?php echo $value[1]; ?>" alt="Logo">
                    <p><?php t($value[2]); ?></p></a></li>
            <?php } ?>
        </ul>
        <?php
    }

    function createsettingsnavbar($buttontohighlightin)
    {
        $settingsButtons = [
            "settings.profile" => ["settings.profile.php", "user.png", "Account details"],
            "settings.subscriptions" => ["settings.subscriptions.php", "subscription.png", "Subscriptions"],
            "settings.paymentinformationpaypal" => ["settings.paymentinformationpaypal.php", "wallet.png", "Payment & finances"],
            "settings.preferences" => ["settings.preferences.php", "preferences.png", "Preferences"],
            "settings.languages" => ["settings.languages.php", "language.png", "Language"],
            "settings.about" => ["settings.about.php", "link.png", "About"]
        ];
        ?>
        <ul class="innavbar">
            <?php foreach ($settingsButtons as $key => $value) { ?>
                <li><a class="not-active" href="<?php echo $value[0]; ?>">
                    <img src="../Images/Navbar/hollow/<?php echo $value[1]; ?>" alt="Logo">
                    <p class="navbartext"><?php t($value[2]); ?></p><p class="navbararrow">></p></a></li>
            <?php } ?>
        </ul>
        <?php
    }
?>

</body>

</html>