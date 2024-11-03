<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css">
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("search");
    ?>
    <form method="POST" class="content">
        <input type="text" class="textinpfld" placeholder="Search for a username" name="username">
        <input type="submit" name="submit" value="Search" class="submitbutton">
    </form>
    <?php
    if (isset($_GET["username"])) {
        print ("<div class='contentresultnavbar'>
            <h3>Username: " . $_GET['username'] . "</h3>
            <button>Follow</button>
            <button>Message</button>
            </div>");
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $searchedusername = $_GET["username"];
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $searchedusername);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $userid = $row["id"];
                $poststmt = $conn->prepare("SELECT * FROM posts WHERE accountid=?");
                $poststmt->bind_param("s", $userid);
                $poststmt->execute();
                $postresults = $poststmt->get_result();
                if ($postresults->num_rows > 0) {
                    print("<div class='postgrid'>");
                    while ($row = $postresults->fetch_assoc()) {
                        print ("<div class='postgriditem'>Title: " . $row["title"] . 
                        "<br>Content: " . $row["comment"] . 
                        "<br>Created at: " . $row["createdat"] .
                        "<br>Commentid " . $row["commentid"] . 
                        "<br>Uploaded file: " . $row["file"] . "</div>");
                    }
                    print("</div>");
                }
            }
        }
    }

    ?>
</body>

</html>