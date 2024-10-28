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
    createnavbar();
?>
         <form method="POST">
        <label>Title</label><br>
        <input type="text" class="textinpfld" name="title" spellcheck="true" placeholder="Title" required><br>
        <label>Add a comment (optional)</label><br>
        <textarea class="textinpfld" name="comment" id="comment" rows="3" onload="autoresizetextinputfield(this)" oninput="autoresizetextinputfield(this)" spellcheck="true" placeholder="comment" required></textarea><br>
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
    <?php
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

        session_start();
        $account = "SELECT id, email, username, password FROM users WHERE email = '" . $_SESSION['email'] . "'";
        $result = $conn->query($account);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                print ("Welcome " . $row['username']);
                if ($_POST['comment'] != null || $_POST['file'] != null) {
                    $title = $conn->real_escape_string($_POST['title']);
                    $comment = $conn->real_escape_string($_POST['comment']);
                    $file = $conn->real_escape_string($_POST['file']);
                    $accountid = $conn->real_escape_string($row['id']);
                    $stmt = $conn->prepare("INSERT INTO posts (accountid, comment, title, file) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssss",$accountid, $title, $comment, $file);
                    $stmt->execute() or die("	". $conn->error);
                    if($stmt->execute()){
                        echo "New record created successfully";
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();

                }
            }
        }

    }
    ?>
</body>

</html>