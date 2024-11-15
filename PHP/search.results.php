<?php
session_start(); // Start session to get logged-in user's data

// Ensure the user is logged in before allowing comment posting
if (!isset($_SESSION['id'])) {
    die("You must be logged in to post comments.");
}

// Database connection details
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "Company";

// Create a new database connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check if the connection failed
if ($conn->connect_error) {
    die("Connection failed: {$conn->connect_error}");
}

// Handle comment submission for any logged-in user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_comment"]) && isset($_POST["postid"])) {
    print ($_SESSION['id']);
    $comment = $conn->real_escape_string($_POST["comment"]);
    $postid = $conn->real_escape_string($_POST["postid"]);
    $userid = $conn->real_escape_string($_SESSION['id']); // Get the logged-in user's ID from session
    // Insert the comment into the database with the user's ID
    $commentstmt = $conn->prepare("INSERT INTO comments (postid, userid, comment) VALUES (?, ?, ?)");
    $commentstmt->bind_param("iis", $postid, $userid, $comment);
    $commentstmt->execute();

    header("Location: {$_SERVER['REQUEST_URI']}"); // Redirect to the same page to avoid form resubmission
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <?php
    include_once '../Libraries/navbar.php';
    include_once '../Libraries/navbar.php';
    createnavbar("search");
    ?>

    <!-- Search form for username -->
    <div class="content">
        <form method="GET">
            <input type="text" class="textinpfld" placeholder="Search for a username" name="username" required
                autocomplete="off">
            <input type="submit" name="submit" value="Search" class="submitbutton">
        </form>
        <form>
            <a href="message.php">Message</a>
            <input type="submit" name="submit" value="Follow" class="submitbutton">
        </form>
    </div>
    <?php
    // Check if the username is set in the GET request
    if (isset($_GET["username"])) {
        $searchedusername = $_GET["username"];

        echo "<div class='contentuser'>
                <h3>Username: " . htmlspecialchars($searchedusername) . "</h3>

              </div>";

        // Find the user ID by username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
        $stmt->bind_param("s", $searchedusername);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"], $_POST["postid"])) {
            $postid = $conn->real_escape_string($_POST["postid"]);
            if ($_POST["action"] == "like") {
                $likestmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
                $likestmt->bind_param("i", $postid);
                $likestmt->execute();
            } else if ($_POST["action"] == "dislike") {
                $dislikestmt = $conn->prepare("UPDATE posts SET dislikes = dislikes + 1 WHERE id = ?");
                $dislikestmt->bind_param("i", $postid);
                $dislikestmt->execute();
            }
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"], $_POST["commentid"])) {
            $commentid = $conn->real_escape_string($_POST["commentid"]);
            if ($_POST["action"] == "like") {
                $likestmt = $conn->prepare("UPDATE comments SET likes = likes + 1 WHERE id = ?");
                $likestmt->bind_param("i", $commentid);
                $likestmt->execute();
            } else if ($_POST["action"] == "dislike") {
                $dislikestmt = $conn->prepare("UPDATE comments SET dislikes = dislikes + 1 WHERE id = ?");
                $dislikestmt->bind_param("i", $commentid);
                $dislikestmt->execute();
            }
        }
        if ($result->num_rows > 0) {
            while ($user = $result->fetch_assoc()) {
                $userid = $user["id"];

                // Find posts by user ID
                $poststmt = $conn->prepare("SELECT * FROM posts WHERE accountid=?");
                $poststmt->bind_param("i", $userid);
                $poststmt->execute();
                $postresults = $poststmt->get_result();

                if ($postresults->num_rows > 0) {
                    echo "<div class='postgrid'>";
                    // Loop through each post and display it
                    while ($post = $postresults->fetch_assoc()) {
                        echo "<div class='postgriditem'>";
                        echo "<h4>" . htmlspecialchars($post["title"]) . "</h4>";
                        echo "<p>" . htmlspecialchars($post["comment"]) . "</p>";
                        echo "<p><small>Posted on: " . htmlspecialchars($post["createdat"]) . "</small></p>";
                        // Handle like and dislike actions
                        ?>


                        <?php
                        $videoformats = ["mp3", "mp4", "wav"];
                        $imageformats = ["jpg", "jpeg", "png", "gif"];
                        $fileextension = strtolower(pathinfo($post["file"], PATHINFO_EXTENSION));
                        if ($post["file"] != null) {
                            if (in_array($fileextension, $videoformats)) {
                                echo "<video width='400' height='400' controls><source src='$post[file]' type='video/mp4'></video>";
                            } else if (in_array($fileextension, $imageformats)) {

                                echo "<img src='" . $post['file'] . "' width='400' height='400' />";
                            }
                        }
                        // Display the comment form for each post
                        ?>
                        <br>
                        <form name="like" class="likeanddislike" method="post">
                            <input type="hidden" name="action" value="like">
                            <input type="hidden" name="postid" value="<?= $post["id"] ?>">
                            <input type="image" name="like" value="like" src="../Images/Posts-comments-replies/hollow/like.png" />
                            <?= $post["likes"] ?>
                        </form>
                        <form name="dislike" class="likeanddislike" method="post">
                            <input type="hidden" name="action" value="dislike">
                            <input type="hidden" name="postid" value="<?= $post["id"] ?>">
                            <input type="image" name="dislike" value="dislike" src="../Images/Posts-comments-replies/hollow/dislike.png" />
                            <?= $post["dislikes"] ?>
                        </form>
                        <form method='POST' class='postcommentform'>
                            <input type='hidden' name='postid' value='<?= $post["id"] ?>'>
                            <input type='text' class='textinpfld' placeholder='Comment' name='comment' required>
                            <input type='submit' name='submit_comment' value='Comment' class='submitbutton'>
                        </form>

                        <?php
                        // Fetch and display comments with usernames for the current post
                        $commentstmt = $conn->prepare("
                        SELECT comments.comment, comments.createdat, comments.id,comments.likes, comments.dislikes, users.username
                        FROM comments 
                        JOIN users ON comments.userid = users.id 
                        WHERE comments.postid = ? 
                        ORDER BY comments.createdat DESC
                        ");
                        $commentstmt->bind_param("i", $post["id"]);
                        $commentstmt->execute();
                        $comments = $commentstmt->get_result();
                        if ($comments->num_rows > 0) {
                            echo "<div class='comments'>";
                            while ($comment = $comments->fetch_assoc()) {
                                echo "<p><strong>" . htmlspecialchars($comment["username"]) . "</strong>: " . htmlspecialchars($comment["comment"]) . "</p>";
                                echo "<small>Commented on: " . htmlspecialchars($comment["createdat"]) . "</small><br>"; ?>
                                <form class="likeanddislike" method="post">
                                    <input type="hidden" name="action" value="like">
                                    <input type="hidden" name="commentid" value="<?= $comment["id"] ?>">
                                    <input type="image" id="<?= $comment['id'] . 'like' ?>" onclick="like('<?= $comment['id'] ?>')"
                                        src="../Images/Posts-comments-replies/hollow/like.png" />
                                    <?= $comment["likes"] ?>
                                </form>
                                <form class="likeanddislike" method="post">
                                    <input type="hidden" name="action" value="dislike">
                                    <input type="hidden" name="commentid" value="<?= $comment["id"] ?>">
                                    <input type="image" id="<?= $comment['id'] . 'dislike' ?>" onclick="dislike('<?= $comment['id'] ?>')"
                                        src="../Images/Posts-comments-replies/hollow/dislike.png" />
                                    <?= $comment["dislikes"] ?>
                                </form>
                            <?php }
                            echo "</div>";
                        }

                        echo "</div>"; // Close postgriditem div
                    }
                    echo "</div>"; // Close postgrid div
                } else {
                    echo "<p>No posts found for this user.</p>";
                }
            }
        } else {
            echo "<p>User not found.</p>";
        }
    }

    // Close the database connection
    $conn->close();
    ?>
    <script>
        function like(commentId) {
            document.getElementById(commentId + 'like').src = "../Images/Posts-comments-replies/filled/like.png";
            $.post("like_action.php", { id: commentId, action: 'like' });
        }
        function dislike(commentId) {
            document.getElementById(commentId + 'dislike').src = "../Images/Posts-comments-replies/filled/dislike.png";
            $.post("dislike_action.php", { id: commentId, action: 'dislike' });
        }
    </script>
    <style>
        .likeanddislike {
            display: inline-block;
            text-align: left;
            border: none;
            background: none;
            padding: 0;
            width: 5vw;
            height: 5vh;
        }

        .likeanddislike:active {
            display: inline-block;
            text-align: left;
            border: none;
            background: none;
            padding: 0;
            width: 5vw;
            height: 5vh;
        }

        .likeanddislike>input {
            width: 2.5vw;
            height: 2.5vh;
        }

        .likeanddislike>input:active {
            width: 2.5vw;
            height: 2.5vh;
        }
    </style>
</body>

</html>