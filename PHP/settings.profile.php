<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
    <link rel="icon" href="../Logo.png">
</head>

<body>
    <?php
    include_once '../Libraries/navbar.php';
    createnavbar("settings.profile");
    createsettingsnavbar("settings.profile"); ?>
    <div class="normalcontentnavbar">
        <?php
        form();
        function deleteaccount() {
            header("Location: index.php");
        }
        function form()
        {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "Company";
            $conn = new mysqli($servername, $username, $password, $dbname);
            $sql = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $sql->bind_param("s", $_SESSION['email']);
            $sql->execute();
            $result = $sql->get_result();



            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
            <form class='form' method='POST' enctype='multipart/form-data'>
                <div class='content'>
                    <h1>Account</h1>
                    <input type='text' name='username' class='twotextinpfld' placeholder='Username: {$row['username']}' value='{$row['username']}'><br>
                    <h3>Reset Password</h3>
                    <input type='password' name='password' class='twotextinpfld' placeholder='Reset Password' minlength='8'><br>
                    <input type='password' name='confirmpassword' class='twotextinpfld' placeholder='Confirm Password' minlength='8'><br>
                    <input type='submit' class='submitbutton' value='Save'>
                </div>
            </form>";
                }
            }

            ?>
            <form class='form' method='POST' enctype='multipart/form-data' id='profileImageForm'>
                <input type='file' name='profileimg' id='profileimg'><br>
                <!--<input type='submit' class='submitbutton' value='Save'>-->
            </form>
            <form method="POST">
                <input type="submit" name="deleteacc" class="submitbutton" value="Delete Account">
            </form>
            <script>
                document.getElementById('profileimg').addEventListener('change', function(event) {
                    document.getElementById('profileImageForm').submit();
                });
            </script>
            <?php
            if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteacc'])){
                $sql = $conn->prepare("DELETE FROM users WHERE email = ?");
                $sql->bind_param("s", $_SESSION['email']);
                $sql->execute();
                session_destroy();
                deleteaccount();
            }
            $conn->close();
        }
        ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>
            if (window.innerWidth < 768) {
                $(".innavbar").hide();
            }
        </script>
    </div>
    </div>
</body>

</html>

<?php
if (!isset($_SESSION['email'])) {
    die("User not logged in.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Company";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["profileimg"])) {
        $targetdir = "../uploads/".$_SESSION['username']."/profileimg/";
        $targetfile = $targetdir . "profile_picture." . strtolower(pathinfo($_FILES["profileimg"]["name"], PATHINFO_EXTENSION));
        $imagefiletype = strtolower(pathinfo($targetfile, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profileimg"]["tmp_name"]);
        $arrayimgextensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];
        if ($check == false) {
            die("File is not a valid image format.");
        }
        if ($_FILES["profileimg"]["size"] > 5000000) {
            die("File is too large.");
        }
        if (!in_array($imagefiletype, $arrayimgextensions)) {
            die("File is not a valid image format. Valid formats are jpg, jpeg, png, gif, bmp, webp.");
        }
        if (!file_exists($targetdir)) {
            mkdir($targetdir, 0777, true);
        }
        foreach (glob("{$targetdir}*") as $file) {
            unlink($file); // Delete all files in the directory meaning all profile pictures will be deleted and replaced
        }
        move_uploaded_file($_FILES["profileimg"]["tmp_name"], $targetfile);
        header("Refresh:0"); // Refresh to initialize the new profile picture
    }
    if (isset($_POST["password"]) && isset($_POST["confirmpassword"])) {
        if ($_POST["password"] === $_POST["confirmpassword"]) {
            $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
            $pswrdstmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
            $pswrdstmt->bind_param("ss", $hashed_password, $_SESSION['email']);
            $hashedpassword = $hashed_password;
            $pswrdstmt->execute();
            $pswrdstmt->close();
            header("Refresh:0"); // Refresh the page to initialize the new language without get parameters in the URL to prevent looping
        } else {
            echo "<script>alert('Passwords do not match');</script>";
        }
    }
    if (isset($_POST["username"])) {
        $stmt = $conn->prepare("UPDATE users SET username=? WHERE email=?");
        $stmt->bind_param("ss", $_POST["username"], $_SESSION['email']);
        print ($_POST["username"]);
        $_SESSION["username"] = $_POST["username"];
        $stmt->execute();
        $stmt->close();
        header("Refresh:0"); // Refresh the page to initialize the new language without get parameters in the URL to prevent looping
    }
}


?>