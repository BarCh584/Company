<?php

// Ensure the user is logged in before allowing comment posting
/*if (!isset($_SESSION['email'])) {
    die("You must be logged in to post comments.");
}*/

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

/**
 * Handle comment submission
 */
function handleCommentSubmission($conn)
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit_comment"], $_POST["postid"])) {
        $comment = $conn->real_escape_string($_POST["comment"]);
        $postid = $conn->real_escape_string($_POST["postid"]);
        $userid = $conn->real_escape_string($_SESSION['id']); // Get the logged-in user's ID from session

        // Insert the comment into the database
        $commentstmt = $conn->prepare("INSERT INTO comments (postid, userid, comment) VALUES (?, ?, ?)");
        $commentstmt->bind_param("iis", $postid, $userid, $comment);
        $commentstmt->execute();

        // Redirect to avoid form resubmission
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}
function handleReplySubmission($conn)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_reply"], $_POST["commentid"])) {
        $reply = $conn->real_escape_string($_POST["reply"]);
        $commentid = $conn->real_escape_string($_POST["commentid"]);
        $userid = $conn->real_escape_string($_SESSION['id']);

        $replystmt = $conn->prepare("INSERT INTO replies (commentid, userid, reply) VALUES (?, ?, ?)");
        $replystmt->bind_param("iis", $commentid, $userid, $reply);
        $replystmt->execute();

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}
/**
 * Handle like or dislike actions for posts and comments
 */
function handleLikesDislikes($conn)
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
        $id = $_POST["postid"] ?? $_POST["commentid"];
        $type = isset($_POST["postid"]) ? 'posts' : 'comments';
        $column = ($_POST["action"] === "like") ? 'likes' : 'dislikes';

        $stmt = $conn->prepare("UPDATE $type SET $column = $column + 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

/**
 * Fetch user ID by username
 */
function getUserIdByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Fetch posts by user ID
 */
function getPostsByUserId($conn, $userid)
{
    $stmt = $conn->prepare("SELECT * FROM posts WHERE accountid=?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Display likes/dislikes buttons
 */
function displayLikeDislikeButtons($id, $type, $likes, $dislikes)
{
    $inputName = ($type === 'post') ? 'postid' : 'commentid';
    echo "
        <form name='like' class='likeanddislike' method='post'>
            <input type='hidden' name='action' value='like'>
            <input type='hidden' name='$inputName' value='$id'>
            <input type='image' class='likeanddislike' src='../Images/Posts-comments-replies/hollow/like.png' />
            $likes
        </form>
        <form name='dislike' class='likeanddislike' method='post'>
            <input type='hidden' name='action' value='dislike'>
            <input type='hidden' name='$inputName' value='$id'>
            <input type='image' class='likeanddislike' src='../Images/Posts-comments-replies/hollow/dislike.png' />
            $dislikes
        </form>";
}

// Process form submissions
handleCommentSubmission($conn);
handleLikesDislikes($conn);
handleReplySubmission($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>-->
</head>

<body>
    <?php
    include_once '../Libraries/navbar.php';
    createnavbar("search");
    ?>

    <div class="normalcontentnavbar">
        <!-- Search form -->
        <?php
        if (isset($_GET["username"])) {
            $searchedusername = htmlspecialchars($_GET["username"]);
            $user = getUserIdByUsername($conn, $searchedusername);

            if ($user) {

                $userid = $user["id"];
                echo "<div class='contentuser'><h3>Username: $searchedusername</h3></div>";
                echo "<a href='message.php?username=$searchedusername'>Message</a>";
                $posts = getPostsByUserId($conn, $userid);

                /* Check if session user is subscribed to that creator */
                $subscriptionstmt = $conn->prepare(("SELECT * FROM subscriptions WHERE subscriber=? AND creator=?"));
                $subscriptionstmt->bind_param("ss", $_SESSION['username'], $searchedusername);
                $subscriptionstmt->execute();
                $subscriptionstmt->store_result();
                if ($subscriptionstmt->num_rows == 0) {
                    $subscriptionstmt->close(); // Close the prepared statement to prevent data leaks
                    echo "
                <div class='paymentform'>
                    <div id='paypalcontainer'>
                    </div>
                </div>
                <script
                    src='https://www.paypal.com/sdk/js?client-id=AX3Uu6n2ZthFq8bzmqyqK0YSiOYB9FR6igJjmEyAestmzAVw7Htar3yuD195uBDQu2psbQHvUFmwTwfq'></script>
                <script>
                fetch('insertsubscription.php')
                .then (response => response.json())
                .then(data => {
                    const planId = data.plan_id;
                    paypal.Buttons({
                        createSubscription: function(data, actions) {
                            return actions.subscription.create({
                                'plan_id': planId // Adjust planid to real planid for it to work
                            });
                        },
                        onApprove: function(data, actions) {
                            alert('Subscription successfull under ID: ' + data.subscriptionID);
                        },
                        onError: function(err) {
                            console.log('Error during subscription, error code: ', err);
                        },
                        onCancel: function(data) {
                            alert('Subscription cancelled');
                        }
                    }).render('#paypalcontainer');
                }).catch (error => console.error('Error:', error));
                    }); 

                </script>";
                    die("You are not subscribed to this creator. Please subscribe to view their content.");
                } else {
                    $subscriptionstmt->close(); // Close the prepared statement to prevent data leaks
                    /* If subscriptions is valid, display content of creator */
                    if ($posts->num_rows > 0) {
                        echo "<div class='postgrid'>";
                        while ($post = $posts->fetch_assoc()) {
                            echo "<div class='postgriditem'>";
                            echo "<h4>" . htmlspecialchars(($post["accountname"])) . "</h4>";
                            echo "<h4>" . htmlspecialchars($post["title"]) . "</h4>";
                            echo "<p>" . htmlspecialchars($post["comment"]) . "</p>";
                            echo "<p><small>Posted on: " . htmlspecialchars($post["createdat"]) . "</small></p>";

                            if ($post["file"]) {
                                $fileExtension = strtolower(pathinfo($post["file"], PATHINFO_EXTENSION));
                                if (in_array($fileExtension, ["mp3", "mp4", "wav"])) {
                                    echo "<video width='400' controls><source src='{$post["file"]}' type='video/mp4'></video>";
                                } elseif (in_array($fileExtension, ["jpg", "jpeg", "png", "gif"])) {
                                    echo "<img src='{$post["file"]}' width='400' />";
                                }
                            }

                            displayLikeDislikeButtons($post["id"], 'post', $post["likes"], $post["dislikes"]);

                            // Comment form
                            echo "<form method='POST' class='postcommentform'>
                            <input type='hidden' name='postid' value='{$post["id"]}'>
                            <input type='text' class='textinpfld' placeholder='Comment' name='comment' required>
                            <input type='submit' name='submit_comment' value='Comment' class='submitbutton'>
                          </form>";

                            // Fetch and display comments
                            $commentstmt = $conn->prepare("
                        SELECT comments.id, comments.comment, comments.likes, comments.dislikes, comments.createdat, users.username 
                        FROM comments 
                        JOIN users ON comments.userid = users.id 
                        WHERE comments.postid = ? ORDER BY comments.likes DESC
                    ");
                            $commentstmt->bind_param("i", $post["id"]);
                            $commentstmt->execute();
                            $comments = $commentstmt->get_result();

                            if ($comments->num_rows > 0) {
                                echo "<div class='comments'>";
                                while ($comment = $comments->fetch_assoc()) {
                                    echo "<div class='comment'>";
                                    echo "<p><strong>" . htmlspecialchars($comment["username"]) . "</strong>: " . htmlspecialchars($comment["comment"]) . "</p>";
                                    echo "<small>Commented on: " . htmlspecialchars($comment["createdat"]) . "</small>";
                                    displayLikeDislikeButtons($comment["id"], 'comment', $comment["likes"], $comment["dislikes"]);

                                    // Reply button and form
                                    echo "<form method='POST' class='replyform'>
                                <input type='hidden' name='commentid' value='{$comment["id"]}'>
                                <input type='text' class='textinpfld' placeholder='Reply' name='reply' required>
                                <input type='submit' name='submit_reply' value='Reply' class='submitbutton'>
                                </form>";

                                    // Fetch and display replies for this comment
                                    $repliesstmt = $conn->prepare("
                            SELECT replies.id, replies.reply, replies.createdat, users.username 
                            FROM replies JOIN users ON replies.userid = users.id 
                            WHERE replies.commentid = ? ORDER BY replies.createdat DESC
                            ");
                                    $repliesstmt->bind_param("i", $comment["id"]);
                                    $repliesstmt->execute();
                                    $replies = $repliesstmt->get_result();

                                    if ($replies->num_rows > 0) {
                                        echo "<div class='replies'>";
                                        while ($reply = $replies->fetch_assoc()) {
                                            echo "<p><strong>" . htmlspecialchars($reply["username"]) . "</strong>: " . htmlspecialchars($reply["reply"]) . "</p>";
                                            echo "<small>Replied on: " . htmlspecialchars($reply["createdat"]) . "</small>";
                                        }
                                        echo "</div>";
                                    }
                                    $repliesstmt->close(); // Close the prepared statement to prevent data leaks
                                }

                                echo "</div>"; // Close comment div
                            }
                            echo "</div>"; // Close comments div
                        }
                        echo "</div><br>"; // Close postgriditem
                    }
                    echo "</div>"; // Close postgrid
                }
            } else {
                echo "<p>No posts found for this user.</p>";
            }

        } else {
            echo "<p>User not found.</p>";
        }

        // Close database connection
        $conn->close();
        ?>
    </>
</body>
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

</html>