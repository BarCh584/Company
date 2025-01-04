<!DOCTYPE html>
<html lang="en">
<?php
$submit = false;
if ($submit == true) {
    if ($stmt->execute()) {
        $submit = false;
        header("Location: search.results.php?username=" . $username);
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="container">
        <?php
        include_once('../Libraries/navbar.php');
        createnavbar("add"); ?>
        <div class="normalcontentnavbar">
            <form method="POST" enctype="multipart/form-data" class="content">
                <label>Title</label><br>
                <input type="text" class="textinpfld" name="title" spellcheck="true" placeholder="Title"><br>
                <label>Add a comment (optional)</label><br>
                <textarea class="textinpfld" name="comment" id="comment" rows="3"
                    onload="autoresizetextinputfield(this)" oninput="autoresizetextinputfield(this)" spellcheck="true"
                    placeholder="Comment"></textarea><br>
                <label>Upload your file (optional)</label><br>
                <input type="file" name="file" id="file"><br>
                <input type="submit" class="submitbutton">
            </form>
        </div>
    </div>
    <script>
        function autoresizetextinputfield(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    </script>
</body>
<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Company";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling post submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify user session

    $account = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $account->bind_param("s", $_SESSION['email']);
    $account->execute();
    $result = $account->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Welcome " . htmlspecialchars($row['username']);
            if (!empty($_POST['comment']) || !empty($_POST['file'])) {
                $title = $conn->real_escape_string($_POST['title']);
                $comment = $conn->real_escape_string($_POST['comment']);
                $accountid = $conn->real_escape_string($row['id']);
                $username = $conn->real_escape_string($row['username']);

                // Check if file is uploaded
                $file = null;
                if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                    $filename = basename($_FILES["file"]["name"]);
                    $targetdir = "../uploads/" . $username . "/";
                    if (!is_dir($targetdir)) {
                        mkdir($targetdir, 0777, true);
                    }
                    $targetfilepath = $targetdir . $filename;
                    $filetype = strtolower(pathinfo($targetfilepath, PATHINFO_EXTENSION));
                    $allowedtypes = array("jpg", "jpeg", "png", "gif", "mp3", "mp4", "wav");

                    if (in_array($filetype, $allowedtypes)) {
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetfilepath)) {
                            echo "The file " . htmlspecialchars($filename) . " has been uploaded.";
                            $file = $targetfilepath;
                        } else {
                            echo "Sorry, there was an error uploading your file.";
                        }
                    } else {
                        echo "Your file must be of type: " . implode(", ", $allowedtypes);
                    }
                }

                // Insert post into database
                if ($file != null) {
                    $stmt = $conn->prepare("INSERT INTO posts (accountid, comment, title, file, accountname) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $accountid, $comment, $title, $file, $username);
                } else {
                    $stmt = $conn->prepare("INSERT INTO posts (accountid, comment, title, accountname) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss", $accountid, $comment, $title, $username);
                }
                $submit = true;
            } else {
                echo "Please fill in the required fields.";
            }
        }
    } else {
        echo "User not found.";
    }
}
?>

</html>