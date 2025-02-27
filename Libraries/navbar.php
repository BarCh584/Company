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
        $directory = "../uploads/" . $_SESSION['username'] . "/profileimg/profile_picture.";
        $imgformats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'];
        $filesfound = [];
        foreach ($imgformats as $format) {
            $pattern = $directory . $format;
            $filesfound = array_merge($filesfound, glob($pattern)); // Append found files to $filesfound
        }
        if (count($filesfound) == 0) {
            $filesfound = ["../Images/Navbar/black/hollow/settings.profile.png"];
        }
        $buttons = [
            "startpage" => ["startpage.php", "home.png", "Home"],
            "search" => ["search.php", "search.png", "Search"],
            "add" => ["add.php", "add.png", "Add"],
            "message" => ["message.php", "message.png", "Messages"],
            "settings.profile" => ["settings.navbar.php", "settings.png", "Settings"],
            "live-stream" => ["live-stream.php?username=$_SESSION[username]", "live-streaming.png", "Livestream"],
            "profile" => ["search.results.php?username=$_SESSION[username]&show=posts", $filesfound[0], "$_SESSION[username]"]
        ];
        ?>
        <ul class="outnavbar">
            <!--Logo-->
            <?php foreach ($buttons as $key => $value) {
                if ($key != "profile") { ?>
                    <li><a class="<?php echo ($buttontohighlight == $key) ? 'active ' . $key : 'not-active'; ?>"
                            href="<?php echo $value[0]; ?>">
                            <img class="imagesrc <?php echo ($buttontohighlight == $key) ? 'filled' : 'hollow'; ?>"
                                src="../Images/Navbar/black/hollow/<?php echo $value[1]; ?>" alt="Logo">
                            <h5><?php t($value[2]); ?></h5>
                        </a></li>
                <?php } else {
                    ?>
                    <li>
                        <a href="<?php echo $value[0]; ?>">
                            <img id="imagesrc" class="imagesrc" src="<?php
                            if (count($filesfound) == 0) {
                                print ("../Images/Navbar/black/hollow/profile.png");
                            } else
                                print ($filesfound[0]); ?>" alt="Profile picture">
                            <h5><?php t($value[2]); ?></h5>
                        </a>
                    </li>
                    <?php
                }
            } ?>
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
    ?>


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
            <button id="imprint">Imprint</button>
            <button id="privacypolicy">Privacy policy</button>
            <button id="generaltermsandconditions">General terms & conditions</button>
        </div>
    </div>
</body>
<!--Include jquery libary-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById("imprint").addEventListener("click", function () {
        window.location.href = "impress.php";
    });
    document.getElementById("privacypolicy").addEventListener("click", function () {
        window.location.href = "privacypolicy.php";
    });
    document.getElementById("generaltermsandconditions").addEventListener("click", function () {
        window.location.href = "generaltermsandconditions.php";
    });
    $(document).ready(function () {
        // Set screenY in session via AJAX

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

        // change image src to the filled or hollow directory to change the icon´s appearance

        changeicon();
        $("button").click(function () {
            changeicon();
            $(this).children("img").each(function () {
                if (this.src && this.src.includes("hollow")) {
                    this.src = this.src.replace("hollow", "filled");
                } else if (this.src && this.src.includes("filled")) {
                    this.src = this.src.replace("filled", "hollow");
                }
            });
        });
        function changeicon() {
            $(".active").each(function () {
                $(this).children("img").each(function () {
                    this.src = this.src.replace("hollow", "filled");
                });
            });
            if (!$("button").hasClass("active")) {
                $(this).children("img").each(function () {
                    this.src = this.src.replace("filled", "hollow");
                });
            }
        }
        if (window.innerWidth >= 650) {
            $(".innavbar").show();
        }
        // cookie banner script

        // Blur the background and make it clickable per default 
        $('body > *:not(.cookie-banner)').css('filter', 'blur(4px)');
        $('body > *:not(.cookie-banner)').css('pointer-events', 'all');
        $(".cookie-banner").css("visibility", "hidden");
        // check with a cookie if the cookie banner has been denied or accepted
        if (localStorage.getItem('cookieSeen') == 'shown') {
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