<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <?php
        include_once('../Libraries/navbar.php');
        createnavbar("settings.profile");
        ?>
        <?php
        include_once('../Libraries/navbar.php');
        createsettingsnavbar("settings.preferences");
        ?>
        <div class="content">
            <h1>Preferences</h1>
            <form action="settings.preferences.php" method="post">
                <br>
                <label for="theme">Theme:</label>
                <span class="slider"></span>
                <br>
                <!--/* Parts of this site uses code from the project "smooth-frog-53" made by JkHuger (https://uiverse.io/JkHuger/smooth-frog-53), licensed under the MIT License*/-->
                <p>Select your theme</p>
                <div class="sliderbutton">
                    <label>
                        <input class="toggle-checkbox" type="checkbox">
                        <div class="toggle-slot">
                            <div class="sun-icon-wrapper">
                                <div class="iconify sun-icon" data-icon="feather-sun" data-inline="false"></div>
                            </div>
                            <div class="toggle-button"></div>
                            <div class="moon-icon-wrapper">
                                <div class="iconify moon-icon" data-icon="feather-moon" data-inline="false"></div>
                            </div>
                        </div>
                    </label>
                </div>
                <br>
                <input type="submit" class="submitbutton" value="Save">
            </form>
        </div>
    </div>
</body>

</html>