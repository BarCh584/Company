<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    
    <title>AccessFrame</title>
    
</head>

<body>
    <?php
    include("translation.php");
    //include('../Libraries/createdefaultdailytableentries.php');
    //include('../Libraries/createdefaultmonthlytableentries.php');
    function createnavbar($buttontohighlight)
    {
        $buttons = [
            "startpage" => ["startpage.php", "home.png", "Home"],
            "search" => ["search.php", "search.png", "Search"],
            "add" => ["add.php", "add.png", "Add"],
            "message" => ["message.php", "message.png", "Messages"],
            "settings.profile" => ["settings.navbar.php", "settings.png", "Settings"],
            "live-stream" => ["live-stream.php?username=$_SESSION[username]", "live-streaming.png", "Livestream"]
        ];
        ?>
        <ul class="outnavbar">
        <!--Logo-->
            <?php foreach ($buttons as $key => $value) { ?>
                <li><a class="<?php echo ($buttontohighlight == $key) ? 'active ' . $key : 'not-active'; ?>"
                        href="<?php echo $value[0]; ?>">
                        <img class="imagesrc <?php echo ($buttontohighlight == $key) ? 'filled' : 'hollow'; ?>"
                            src="../Images/Navbar/black/hollow/<?php echo $value[1]; ?>" alt="Logo">
                        <h5><?php t($value[2]); ?></h5>
                    </a></li>
            <?php } ?>
        </ul>
        <?php
    }

    function createsettingsnavbar($buttontohighlightin)
    {
        $settingsButtons = [
            "settings.profile" => ["settings.profile.php", "settings.profile.png", "Account details"],
            "settings.statistics" => ["settings.statistics.php", "settings.statistics.png", "Statistics"],
            "settings.subscriptions" => ["settings.subscriptions.php", "settings.subscription.png", "Subscriptions"],
            "settings.paymentinformationpaypal" => ["settings.paymentinformationpaypal.php", "settings.paymentinformationpaypal.png", "Payment & finances"],
            "settings.preferences" => ["settings.preferences.php", "settings.preferences.png", "Preferences"],
            "settings.languages" => ["settings.languages.php", "settings.language.png", "Language"],
            "settings.about" => ["settings.about.php", "settings.about.png", "About"]
        ];
        ?>
        <ul class="innavbar">
            <?php foreach ($settingsButtons as $key => $value) { ?>
                <li><a class="<?php echo ($buttontohighlightin == $key) ? 'active ' . $key : 'not-active'; ?>"
                        href="<?php echo $value[0]; ?>">
                        <img class="imagesrc <?php echo ($buttontohighlightin == $key) ? 'filled' : 'hollow'; ?>"
                            src="../Images/Navbar/black/hollow/<?php echo $value[1]; ?>" alt="Logo">
                        <p class="navbartext"><?php t($value[2]); ?></p>
                        <p class="navbararrow">></p>
                    </a></li>
            <?php } ?>
        </ul>
        <?php
    }
    // Get user profile picture
    $directory = "../uploads/" . $_SESSION['username'] . "/profileimg/profile_picture.";
    $imgformats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'];
    $filesfound = [];

    foreach ($imgformats as $format) {
        $pattern = $directory . $format;
        $filesfound = array_merge($filesfound, glob($pattern)); // Append found files to $filesfound
    }
    ?>
    <!-- Create a 3rd navbar for profile picture and name-->
    <ul class="profilenavbar">
        <li>
            <a class="profile" href="search.results.php?username=<?=$_SESSION["username"]?>&show=posts">
                <p><?= $_SESSION["username"] ?></p>
                <img id="profileimg" src="<?=$filesfound[0]?>" alt="Profile picture">
            </a>
        </li>
    </ul>


</body>
<!--Include jquery libary-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            $(".imagesrc").each(function () {
                this.src = this.src.replace("black", "white"); // White icons for dark mode
            })
        }
        else {
            $(".imagesrc").each(function () {
                this.src = this.src.replace("white", "black") // black icons for dark mode
            })
        }
        $(".hollow").each(function () {
            this.src = this.src.replace("filled", "hollow");
        })

        $(".filled").each(function () {
            this.src = this.src.replace("hollow", "filled");
        })
        if (window.innerWidth >= 768) {
            $(".innavbar").show();
        }
    });
</script>

</html>