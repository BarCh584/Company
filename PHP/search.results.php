<?php
include_once '../Libraries/navbar.php';
include '../Libraries/subscription_plan.php';
include '../Libraries/currency_converter.php';
createnavbar("search");
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
$currencystmt = $conn->prepare("SELECT priceforcontentint, priceforcontentcurrency FROM users WHERE email = ?");
$currencystmt->bind_param("s", $_SESSION['email']);
if ($currencystmt->execute()) {
    $currencystmt->bind_result($priceforcontentint, $priceforcontentcurrency);
    $currencystmt->fetch();
    $preferencedcurrency = $priceforcontentcurrency;
    $price = $priceforcontentint;
    $currencystmt->close(); // Close the statement to prevent data leaks
    //createSubscriptionplan($preferencedcurrency, $price); sandbox account not created yet for testing
}
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="icon" href="../Logo.png">
</head>

<body>
    <div class="postscontainer">
        <!-- Search form -->
        <?php
        if (isset($_GET["username"])) {
            $searchedusername = htmlspecialchars($_GET["username"]);
            $user = getUserIdByUsername($conn, $searchedusername);
            if ($user) {
                $creatorstmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $creatorstmt->bind_param("s", $searchedusername);
                $creatorstmt->execute();
                $creatorstmtresult = $creatorstmt->get_result();
                $creatorstmtfinances = $creatorstmtresult->fetch_assoc();
                if ($user) {
                    $userid = $user["id"];
                } else {
                    echo "<p>User not found.</p>";
                    exit();
                }
                $consumerstmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                $consumerstmt->bind_param("s", $_SESSION['email']);
                $consumerstmt->execute();
                $consumerstmtresult = $consumerstmt->get_result();
                $consumerstmtfinances = $consumerstmtresult->fetch_assoc();
                $creatoramount = getexchangerate($searchedusername, $_SESSION['username']);
                $subscriptionstmtbuybutton = $conn->prepare("SELECT * FROM subscriptions WHERE subscriber=? AND creator=?");
                $subscriptionstmtbuybutton->bind_param("ss", $_SESSION['username'], $searchedusername);
                $subscriptionstmtbuybutton->execute();
                $subscriptionstmtbuybutton->store_result(); ?>
                <div class='contentuser'>
                    <h3>Username:<?= $searchedusername ?></h3>
                    <a href='message.php?username=<?= $searchedusername ?>'>Message</a>
                    <a href='show-live-stream.php?username=<?= $searchedusername ?>'>Show live-stream</a>
                    <br><br>
                    <a href="search.results.php?username=<?= $searchedusername ?>&show=posts">Posts</a>
                    <a href="search.results.php?username=<?= $searchedusername ?>&show=images">Images</a>
                    <a href="search.results.php?username=<?= $searchedusername ?>&show=videos">Videos</a>
                    <a href="search.results.php?username=<?= $searchedusername ?>&show=projects">Projects</a>
                </div>
                <?php
                if ($subscriptionstmtbuybutton->num_rows == 0 && $_GET["username"] != $_SESSION['username'])
                    echo "<form method='POST'><input type='submit' value='Buy content for: {$creatoramount}'></form>";  // Only show content buy button if user is not subscribed
                $posts = getPostsByUserId($conn, $userid);
                $currency = userlocationcurrency();
                //print ("<h3>Preferred currency:" . $currency . "</h3>");
                // Check if session user is subscribed to that creator 
                $subscriptionstmt = $conn->prepare("SELECT * FROM subscriptions WHERE subscriber=? AND creator=?");
                $subscriptionstmt->bind_param("ss", $_SESSION['username'], $searchedusername);
                $subscriptionstmt->execute();
                $subscriptionstmt->store_result();
                if ($subscriptionstmt->num_rows == 0 && $_GET["username"] != $_SESSION['username']) {
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
                    

                </script>";
                    die("<p>You are not subscribed to this creator. Please subscribe to view their content.</p>");
                } else {
                    $subscriptionstmt->close(); // Close the prepared statement to prevent data leaks
                    /* If subscriptions is valid, display content of creator */
                    if ($posts->num_rows > 0 && isset($_GET["show"])) {
                        if ($_GET["show"] == "posts") {
                            echo "<div class='postgrid' style='margin-left:0vw !important;'>";
                            while ($post = $posts->fetch_assoc()) {
                                echo "<div class='postgriditem'>";
                                echo "<h4>" . htmlspecialchars($post["accountname"]) . "</h4>";
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
                                uibuttons($post["id"], 'post', $post["likes"], $post["dislikes"]);
                                // Comment form
                                echo "<form method='POST' style='margin-left:2.5vw;' class='postcommentform'>
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

                                if ($comments->num_rows > 0) { ?>
                                    <div style='margin-left:2.5vw;' class='comments'>
                                        <?php while ($comment = $comments->fetch_assoc()) { ?>
                                            <div class='comment'>
                                                <p><strong><?= htmlspecialchars($comment["username"]) ?></strong>:
                                                    <?= htmlspecialchars($comment["comment"]) ?>
                                                </p>
                                                <small>Commented on: <?= htmlspecialchars($comment["createdat"]) ?></small>
                                                <?php uibuttons($comment["id"], 'comment', $comment["likes"], $comment["dislikes"]); ?>
                                                <!-- Reply button and form -->
                                                <form method='POST' style='margin-left: 2.5vw' class='replyform'>
                                                    <input type='hidden' name='commentid' value='<?= $comment["id"] ?>'>
                                                    <input type='text' class='textinpfld' placeholder='Reply' name='reply' required>
                                                    <input type='submit' name='submit_reply' value='Reply' class='submitbutton'>
                                                </form>
                                                <!-- Fetch and display replies for this comment -->
                                                <?php
                                                $repliesstmt = $conn->prepare("
                                                    SELECT replies.id, replies.reply, replies.likes, replies.dislikes, replies.createdat, users.username 
                                                    FROM replies JOIN users ON replies.userid = users.id 
                                                    WHERE replies.commentid = ? ORDER BY replies.createdat DESC
                                                ");
                                                $repliesstmt->bind_param("i", $comment["id"]);
                                                $repliesstmt->execute();
                                                $replies = $repliesstmt->get_result();

                                                if ($replies->num_rows > 0) { ?>
                                                    <div style='margin-left: 2.5vw' class='replies'>
                                                        <?php while ($reply = $replies->fetch_assoc()) { ?>
                                                            <p><strong><?= htmlspecialchars($reply["username"]) ?></strong>:
                                                                <?= htmlspecialchars($reply["reply"]) ?>
                                                            </p>
                                                            <small>Replied on: <?= htmlspecialchars($reply["createdat"]) ?></small>
                                                            <?php uibuttons($reply["id"], 'reply', $reply["likes"], $reply["dislikes"]); ?>
                                                        <?php } ?>
                                                    </div>
                                                <?php }
                                                $repliesstmt->close(); // Close the prepared statement to prevent data leaks
                                                ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div> <!-- Close postgriditem -->
                            <?php
                            } ?>
                        </div><br> <!-- Close postgrid -->
                    <?php } else if ($_GET["show"] == "images") { ?>
                            <div class='imggrid'> <?php
                            $filedir = "../uploads/{$searchedusername}/posts/";
                            $images = glob($filedir . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                            foreach ($images as $image) {
                                echo "<img class='imggridchild' src='{$image}' />"; // Display the image
                                echo "<script>console.log('{$image}');</script>";
                            }
                        } else if ($_GET["show"] == "videos") { ?>
                                    <div class='imggrid'> <?php
                                    $filedir = "../uploads/{$searchedusername}/posts";
                                    $videos = glob("{$filedir}*.{mp4,webm,ogg,mkv}", GLOB_BRACE);
                                    foreach ($videos as $video) {
                                        echo "<video width='400' controls>
                                <source src='{$video}' type='video/mp4'>
                                </video>";
                                    }
                        } else if ($_GET["show"] == "projects") {
                            // Fetch and display projects
                        } ?>
                        </div> <!-- Close imggrid -->
                    <?php }
                }
            } else {
                echo "<p>No posts found for this user.</p>";
            }
        } else {
            echo "<p>User not found.</p>";
        }
        // Process form submissions
        handleCommentSubmission($conn);
        handleLikesDislikes($conn);
        handleReplySubmission($conn);
        // Close database connection
        $conn->close();
        ?>
    </div>
    <div class='report-banner'>
        <p>Report content for:</p><br>
        <div class='reasons'>
            <form method="POST">
                <label>
                    <input type="radio" name="reason" value="Nudity or pornography">Nudity or pornography
                </label>
                <label>
                    <input type="radio" name="reason" value="Hate Speech">Hate speech
                </label>
                <label>
                    <input type="radio" name="reason" value="Harassment or bullying">Harassment or bullying
                </label>
                <label>
                    <input type="radio" name="reason" value="False information">False information
                </label>
                <label>
                    <input type="radio" name="reason" value="Promotes and/or sells illegal activities">Promotes and/or
                    sells illegal activities
                </label>
                <label>
                    <input type="radio" name="reason" value="Harmful content">Harmful content
                </label>
                <label>
                    <input type="radio" name="reason" value="Impersonation">Impersonation
                </label>
        </div>
        <div class='buttons'>
            <button type="submit" name="reportsubmit" class="reportsubmit">Submit</button>
            </form>
            <button class="">Imprint</button>
            <button class="">Privacy policy</button>
        </div>
    </div>
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
<?php
/**
 * Handle comment submission
 */

/**
 * Handle like or dislike actions for posts and comments
 */
function handleLikesDislikes($conn)
{
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
        $user_id = $_SESSION['id']; // Assuming the user is logged in
        $action = $_POST["action"];
        $content_type = isset($_POST["postid"]) ? 'post' : 'comment';
        $content_id = $content_type === 'post' ? ($_POST["postid"] ?? null) : ($_POST["commentid"] ?? null);
        if ($content_id === null) {
            return;
        }
        // Check if the user has already interacted with this item
        $stmt = $conn->prepare("
            SELECT action FROM user_interactions 
            WHERE user_id = ? AND content_type = ? AND content_id = ?
        ");
        $stmt->bind_param("isi", $user_id, $content_type, $content_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingActionRow = $result->fetch_assoc();
        $existingAction = $existingActionRow ? $existingActionRow['action'] : null;
        if ($existingAction) {
            if ($existingAction === $action) {
                // Deselect: Remove the interaction
                $deleteStmt = $conn->prepare("
                    DELETE FROM user_interactions 
                    WHERE user_id = ? AND content_type = ? AND content_id = ?
                ");
                $deleteStmt->bind_param("isi", $user_id, $content_type, $content_id);
                $deleteStmt->execute();
                // Decrease the corresponding count
                $updateStmt = $conn->prepare("
                    UPDATE {$content_type}s SET {$action}s = {$action}s - 1 WHERE id = ?
                ");
                $updateStmt->bind_param("i", $content_id);
                $updateStmt->execute();
            } else {
                // Toggle: Update the action
                $updateInteractionStmt = $conn->prepare("
                    UPDATE user_interactions SET action = ? 
                    WHERE user_id = ? AND content_type = ? AND content_id = ?
                ");
                $updateInteractionStmt->bind_param("sisi", $action, $user_id, $content_type, $content_id);
                $updateInteractionStmt->execute();
                // Decrease the count for the previous action
                $oppositeAction = ($action === 'like') ? 'dislike' : 'like';
                $decreaseStmt = $conn->prepare("
                    UPDATE {$content_type}s SET {$oppositeAction}s = {$oppositeAction}s - 1 WHERE id = ?
                ");
                $decreaseStmt->bind_param("i", $content_id);
                $decreaseStmt->execute();
                // Increase the count for the new action
                $increaseStmt = $conn->prepare("
                    UPDATE {$content_type}s SET {$action}s = {$action}s + 1 WHERE id = ?
                ");
                $increaseStmt->bind_param("i", $content_id);
                $increaseStmt->execute();
            }
        } else {
            // No existing interaction: Add the new action
            $insertStmt = $conn->prepare("
                INSERT INTO user_interactions (user_id, content_type, content_id, action) 
                VALUES (?, ?, ?, ?)
            ");
            $insertStmt->bind_param("isis", $user_id, $content_type, $content_id, $action);
            $insertStmt->execute();

            // Increase the corresponding count
            $increaseStmt = $conn->prepare("
                UPDATE {$content_type}s SET {$action}s = {$action}s + 1 WHERE id = ?
            ");
            $increaseStmt->bind_param("i", $content_id);
            $increaseStmt->execute();
        }
    }
}
function getUserIdByUsername($conn, $username)
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}
function getPostsByUserId($conn, $userid)
{
    $stmt = $conn->prepare("SELECT * FROM posts WHERE accountid=?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    return $stmt->get_result();
}
function uibuttons($id, $type, $likes, $dislikes)
{
    global $conn;
    $user_id = $_SESSION['id'];

    // Check the user's current interaction
    $stmt = $conn->prepare("
        SELECT action FROM user_interactions 
        WHERE user_id = ? AND content_type = ? AND content_id = ?
    ");
    $stmt->bind_param("isi", $user_id, $type, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userAction = $result->fetch_assoc()['action'] ?? null; // Check if the user has interacted with this item

    $likeActive = $userAction === 'like' ? 'active' : '';
    $dislikeActive = $userAction === 'dislike' ? 'active' : '';
    echo "
        <form method='post' style='display: inline;'>
            <input type='hidden' name='action' value='like'>
            <input type='hidden' name='{$type}id' value='{$id}'>
            <button type='submit' style='display: inline;' class='$likeActive'><img class='likedislike' src='../Images/Posts-comments-replies/black/hollow/like.png'> <span>$likes</span></button>
        </form>
        <form method='post' style='display: inline;'>
            <input type='hidden' name='action' value='dislike'>
            <input type='hidden' name='{$type}id' value='{$id}'>
            <button type='submit' style='display: inline;' class='$dislikeActive'><img class='likedislike' src='../Images/Posts-comments-replies/black/hollow/dislike.png'> <span>$dislikes</span></button>
        </form>
        <button class='report-button'>Report</button>

    ";
}

// Report submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reportsubmit"], $_POST["reason"])) {
    print ("<script>alert('Report reason: " . $_POST["reason"] . "');</script>");

    /*
    $reason = $conn->real_escape_string($_POST["reason"]);
    $content_id = $conn->real_escape_string($_POST["content_id"]);
    $content_type = $conn->real_escape_string($_POST["content_type"]);
    $user_id = $conn->real_escape_string($_SESSION['id']);

    $reportstmt = $conn->prepare("INSERT INTO reports (user_id, content_id, content_type, reason) VALUES (?, ?, ?, ?)");
    $reportstmt->bind_param("iiss", $user_id, $content_id, $content_type, $reason);
    $reportstmt->execute();
    $reportstmt->close();
    echo "<script>alert('Report submitted successfully.');</script>";*/
}
?>
<script>
    $(document).ready(function () {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            $(".likedislike").each(function () {
                this.src = this.src.replace("black", "white"); // White icons for dark mode
            });
        }
        else {
            $(".likedislike").each(function () {
                this.src = this.src.replace("white", "black") // black icons for dark mode
            });
        }
        /*$("#hollow").each(function () {
            this.src = this.src.replace("filled", "hollow");
        });

        $("#filled").each(function () {
            this.src = this.src.replace("hollow", "filled");
        });*/
        // Function to open the report popup
        // Close the popup when the close button is clicked
        $('.popup .close').click(function () {
            $('#reportPopup').remove();
        });
        // Handle form submission
        $('#reportForm').submit(function (event) {
            event.preventDefault();
            const formData = $(this).serialize();
            $.post('report.php', formData, function (response) {
                alert('Report submitted successfully.');
                $('#reportPopup').remove();
            }).fail(function () {
                alert('Failed to submit report.');
            });
        });
        // Close the popup when the close button is clicked
        $('.popup .close').click(function () {
            $('#reportPopup').remove();
        });
        // Report functionality
        $(".report-banner").css("visibility", "hidden");
        $(".report-button").click(function () {
            // Blur the background and make it clickable per default 
            $(".report-banner").css("visibility", "visible");
            $('body > *:not(.report-banner)').css('filter', 'blur(4px)');
            $('body > *:not(.report-banner)').css('pointer-events', 'all');
        });
        $('.reportsubmit').click(function () {
            // Closes the banner if the user clicks on a button with the class "close"
            $(".report-banner").css("visibility", "hidden");
            $('body > *:not(.report-banner)').css('filter', 'blur(0px)');
            $('body > *:not(.report-banner)').css('pointer-events', 'all');
        });
    });
</script>

</html>