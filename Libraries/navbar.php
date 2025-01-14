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
            <a class="profile" href="search.results.php?username=<?= $_SESSION["username"] ?>&show=posts">
                <p><?= $_SESSION["username"] ?></p>
                <img id="profileimg" src="<?= $filesfound[0] ?>" alt="Profile picture">
            </a>
        </li>
    </ul>

    <!-- Create a cookie banner-->
</body>
<div class='cookie-banner'>
    <div>
        <p>This website uses cookies</p>
        <p>We use cookies to:</p>
        <br>
        <p>1. Store and/or access information on a device</p>
        <p>2. Create profiles for statistical purposes</p>
        <p>3. Operate the website to function properly</p>
        <select>
            <option>Deutsch</option>
            <option>English</option>
        </select>
    </div>
    <div class='cookies'>
        <div class='cookieselector'>
            <p>Strictly necessary</p>
            <input type='checkbox' name='essential' placeholder='Essential'>
        </div>
        <div class='cookieselector'>
            <p>Analytics</p>
            <input type='checkbox' name='analytical' placeholder='Analytics'>
        </div>
        <div class='cookieselector'>
            <p>Statistics</p>
            <input type='checkbox' name='statistics' placeholder='Statistics'>
        </div>
    </div>
    <div class='cookiebuttons'>
        <button class='manage'>Manage cookies</button>
        <button class='close'>Accept only essential cookies</button>
        <button class='close'>Accept all cookies</button>
        <button>Imprint</button>
        <button>Privacy policy</button>
    </div>
</div>
<!--Include jquery libary-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('.cookies').hide();
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
        // cookie banner script

        // Blur the background and make it clickable per default 
        $('body > *:not(.cookie-banner)').css('filter', 'blur(4px)');
        $('body > *:not(.cookie-banner)').css('pointer-events', 'all');
        $(".cookie-banner").css("visibility", "hidden");
        // check with a cookie if the cookie banner has been denied or accepted
        if(localStorage.getItem('cookieSeen') == 'shown') {
            $('.cookie-banner').css('display', 'none');
            $('body > *:not(.cookie-banner)').css('filter', '');
        }
        else {
            // Show the banner if we can't find the "cookieSeen" item in localStorage
            $('.cookie-banner').delay(2000).fadeIn();
            $('.cookies').hide(); // cookie buttons are hidden by default except if the user clicks on "manage"
            $(".cookie-banner").css("visibility", "visible");
            $('body > *:not(.cookie-banner)').css('pointer-events', 'none');
            localStorage.setItem('cookieSeen', 'shown');
        }
        $('.close').click(function () {
            // Closes the banner if the user clicks on a button with the class "close"
            $('.cookie-banner').delay(250).fadeOut();
            $('body > *:not(.cookie-banner)').css('filter', 'blur(0px)');
            $(".cookie-banner").css("visibility", "hidden");
            $('body > *:not(.cookie-banner)').css('pointer-events', 'all');
        });
        $('.manage').click(function () {
            // Toggles the cookies div if the user clicks on a button with the class "manage"
            $('.cookies').toggle();
        });
    });

</script>

</html>