<?php
session_start();
if (!isset($_SESSION['id'])) {
    die("You must be logged in to post comments.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $uname = $_POST["username"];
        if (strlen($uname) == 0 || $uname == "") {
            $stmt = $conn->prepare("SELECT username FROM users ORDER BY username DESC");
            $stmt->execute();
            $result = $stmt->get_result();
        }
        $stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE CONCAT(?, '%')");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<div>";
            while ($row = $result->fetch_assoc()) {
                $directory = "../uploads/" . $row["username"] . "/profileimg/profile_picture.";
                $imgformats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'];
                $filesfound = [];
                foreach ($imgformats as $format) {
                    $pattern = $directory . $format;
                    $filesfound = array_merge($filesfound, glob($pattern)); // Append found files to $filesfound
                }
                if (count($filesfound) == 0) {
                    $filesfound = ["../Images/Navbar/black/hollow/settings.profile.png"];
                }
                echo "<li><a href='search.results.php?username=" . htmlspecialchars($row["username"]) ."&show=posts". "'><img class='imagesrc' id='imagesrc' src='$filesfound[0]'>" . htmlspecialchars($row["username"]) . "</a></li><br>";
            }
            echo "</div>";
        } else {
            echo "<div><p>User not found</p></div>";
        }
        $stmt->close();
        $conn->close();
    } else {
        echo "No username entered";
    }
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
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
</script>