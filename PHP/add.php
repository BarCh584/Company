<?php
session_start();
$emailaddress = $_SESSION['email'];
// Check if the session variables exist before using them
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Company";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = $conn->prepare("SELECT * FROM users WHERE email =?");
    $account->bind_param("s", $emailaddress);
    $account->execute();
    $result = $account->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            print ("Welcome " . $row['username']);
            if ($_POST['comment'] != null || $_POST['file'] != null) {
                $title = $conn->real_escape_string($_POST['title']);
                $comment = $conn->real_escape_string($_POST['comment']);
                $file = $conn->real_escape_string($_POST['file']);
                $accountid = $conn->real_escape_string($row['id']);
                $username = $conn->real_escape_string($row['username']);
                $stmt = $conn->prepare("INSERT INTO posts (accountid, comment, title, file, accountname) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $accountid, $comment, $title, $file, $username);
                $stmt->execute() or die("	" . $conn->error);
                if ($stmt->execute()) {
                    print ("<h3>Your post was successfully created</h3>");
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css">
</head>

<body>
    <div class="container">
        <?php
        include_once('../Libraries/navbar.php');
        createnavbar("add");
        ?>
        <form method="POST" class="content">
            <label>Title</label><br>
            <input type="text" class="textinpfld" name="title" spellcheck="true" placeholder="Title" required><br>
            <label>Add a comment (optional)</label><br>
            <textarea class="textinpfld" name="comment" id="comment" rows="3" onload="autoresizetextinputfield(this)"
                oninput="autoresizetextinputfield(this)" spellcheck="true" placeholder="Comment"
                required></textarea><br>
            <label>Upload your file (optional)</label><br>
            <input type="file" name="file" id="file"><br>
            <input type="submit" class="submitbutton">
        </form>
    </div>
    <script>
        document.getElementById("comment").onload = autoresizetextinputfield(document.getElementById("comment"));

        function autoresizetextinputfield(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    </script>

</body>

</html>