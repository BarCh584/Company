<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Logo.png">
    <title>AccessFrame</title>
    <link rel="icon" href="../Logo.png">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("startpage");
    ?>
    <div class="normalcontentnavbar">
        <?php
        if (!isset($_SESSION['id'])) {
            die("You must be logged in to post comments.");
        }
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $stmt = $conn->prepare("SELECT * FROM posts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $directory = "../uploads/" . $row['accountname'] . "/profileimg/profile_picture.";
            $imgformats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'];
            $filesfound = [];
            foreach ($imgformats as $format) {
                $pattern = $directory . $format;
                $filesfound = array_merge($filesfound, glob($pattern)); // Append found files to $filesfound
            }
            if (count($filesfound) == 0) {
                $filesfound = ["../Images/Navbar/black/hollow/settings.profile.png"];
            }
            echo "<div class='postgriditem'>
                <h2><img class='postprofileimg' src='{$filesfound[0]}'>" . $row["accountname"] . "</h2>
                <h2>" . $row["comment"] . "</h2><br>
                <p>" . $row["file"] . "</p><br>
                </div>";
        }
        ?>
    </div>
</body>

</html>