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
    <div class="normalcontentnavbar">
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
                        if ($_GET["show"] == "posts" && !isset($_GET["postid"])) {
                            echo "<div class='postgrid' style='margin-left:0vw !important;'>";
                            while ($post = $posts->fetch_assoc()) {
                            
                                echo "<a href='search.postid.php?postid=$post[id]'><div class='postgriditem'>";
                                echo "<h4>" . htmlspecialchars($post["accountname"]) . "</h4>";
                                echo "<h4>" . htmlspecialchars($post["title"]) . "</h4>";
                                echo "<p>" . htmlspecialchars($post["comment"]) . "</p>";
                                echo "<p><small>Posted on: " . htmlspecialchars($post["createdat"]) . "</small></p>";
                                if ($post["file"]) {
                                    $fileExtension = strtolower(pathinfo($post["file"], PATHINFO_EXTENSION));
                                    if (in_array($fileExtension, ["mp3", "mp4", "wav"])) {
                                        echo "<video width='400' controls><source src='../uploads/{$_GET['username']}/posts/{$post["file"]}' type='video/mp4'></video>";
                                    } elseif (in_array($fileExtension, ["jpg", "jpeg", "png", "gif"])) {
                                        echo "<img src='../uploads/{$_GET['username']}/posts/{$post["file"]}' width='400' /><br>";
                                    }
                                }
                                // get number of comments^
                                $commentsonpoststmt = $conn->prepare("SELECT COUNT(*), id FROM comments WHERE postid = ?");
                                $commentsonpoststmt->bind_param("i", $post["id"]);
                                $commentsonpoststmt->execute();
                                $commentsonpoststmt->bind_result($comments, $commentid);
                                $commentsonpoststmt->fetch();
                                $commentsonpoststmt->close(); // Close the statement to prevent data leaks
                                $repliesonpoststmt = $conn->prepare("SELECT COUNT(*) FROM replies WHERE commentid = ?");    
                                $repliesonpoststmt->bind_param("i", $commentid);
                                $repliesonpoststmt->execute();
                                $repliesonpoststmt->bind_result($replies);
                                $repliesonpoststmt->fetch();
                                $repliesonpoststmt->close(); // Close the statement to prevent data leaks
                                uibuttons($post["id"], 'post', $post["likes"], $post["dislikes"], $comments+$replies);
                                ?>
                            </div></a> <!-- Close postgriditem -->
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
        if (!isset($_SESSION['id'])) {
            return; // User must be logged in
        }

        $user_id = $_SESSION['id'];
        $action = $_POST["action"];
        $content_type = isset($_POST["postid"]) ? 'post' : 'comment';
        $content_id = isset($_POST["postid"]) ? $_POST["postid"] : ($_POST["commentid"] ?? null);
        
        if (!$content_id) {
            return;
        }

        // Table name validation to prevent SQL injection
        $validTables = ['posts', 'comments'];
        $table = ($content_type === 'post') ? 'posts' : 'comments';

        $conn->begin_transaction(); // Start transaction

        // Check existing interaction
        $stmt = $conn->prepare("
            SELECT action FROM user_interactions 
            WHERE user_id = ? AND content_type = ? AND content_id = ?
        ");
        $stmt->bind_param("isi", $user_id, $content_type, $content_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingActionRow = $result->fetch_assoc();
        $existingAction = $existingActionRow['action'] ?? null;

        if ($existingAction === $action) {
            // Remove interaction
            $deleteStmt = $conn->prepare("
                DELETE FROM user_interactions 
                WHERE user_id = ? AND content_type = ? AND content_id = ?
            ");
            $deleteStmt->bind_param("isi", $user_id, $content_type, $content_id);
            $deleteStmt->execute();

            // Decrease count
            $updateStmt = $conn->prepare("
                UPDATE $table SET {$action}s = {$action}s - 1 WHERE id = ? AND {$action}s > 0
            ");
            $updateStmt->bind_param("i", $content_id);
            $updateStmt->execute();
        } else {
            // Toggle interaction
            if ($existingAction) {
                $oppositeAction = ($action === 'like') ? 'dislike' : 'like';

                // Update user interaction
                $updateInteractionStmt = $conn->prepare("
                    UPDATE user_interactions SET action = ? 
                    WHERE user_id = ? AND content_type = ? AND content_id = ?
                ");
                $updateInteractionStmt->bind_param("sisi", $action, $user_id, $content_type, $content_id);
                $updateInteractionStmt->execute();

                // Adjust counts
                $decreaseStmt = $conn->prepare("
                    UPDATE $table SET {$oppositeAction}s = {$oppositeAction}s - 1 WHERE id = ? AND {$oppositeAction}s > 0
                ");
                $decreaseStmt->bind_param("i", $content_id);
                $decreaseStmt->execute();
            } else {
                // New interaction
                $insertStmt = $conn->prepare("
                    INSERT INTO user_interactions (user_id, content_type, content_id, action) 
                    VALUES (?, ?, ?, ?)
                ");
                $insertStmt->bind_param("isis", $user_id, $content_type, $content_id, $action);
                $insertStmt->execute();
            }

            // Increase count for the new action
            $increaseStmt = $conn->prepare("
                UPDATE $table SET {$action}s = {$action}s + 1 WHERE id = ?
            ");
            $increaseStmt->bind_param("i", $content_id);
            $increaseStmt->execute();
        }

        $conn->commit(); // Commit transaction
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
function uibuttons($id, $type, $likes, $dislikes, $comments)
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
        <form method='post' style='display: inline'>
            <input type='hidden' name='action' value='comment'>
            <button type='submit' style='display: inline;'><img class='likedislike' src='../Images/Posts-comments-replies/black/hollow/comment.png'><span>$comments</span></button>
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