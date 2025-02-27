<?php
include_once '../Libraries/navbar.php';
include '../Libraries/subscription_plan.php';
include '../Libraries/currency_converter.php';
createnavbar("startpage");
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
    <link rel="icon" href="../Logo2.png">
</head>

<body>
    <div class="normalcontentnavbar">
        <!-- Search form -->

        <?php
        // Get random posts from the posts table which are available to the public
        $poststmt = $conn->prepare("SELECT * FROM posts WHERE visibility = 'everyone' ORDER BY RAND() LIMIT 10");
        $poststmt->execute();
        $posts = $poststmt->get_result();
        /* If subscriptions is valid, display content of creator */
        if ($posts->num_rows > 0) {
            echo "<div class='postgrid' style='margin-left:0vw !important;'>";
            while ($post = $posts->fetch_assoc()) {
                echo "<div class='postgriditem'><a href='search.postid.php?postid=$post[id]'>"; // 
                echo "<h4>" . htmlspecialchars($post["accountname"]) . " <small>". timeelapsed($post["createdat"]) . "</small></h4>";
                echo "<h4>" . htmlspecialchars($post["title"]) . "</h4>";
                echo "<p>" . htmlspecialchars($post["comment"]) . "</p>";
                if ($post["file"]) {
                    $fileExtension = strtolower(pathinfo($post["file"], PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ["mp3", "mp4", "wav"])) {
                        echo "<video width='400' controls><source src='../uploads/{$_GET['username']}/posts/{$post["file"]}' type='video/mp4'></video>";
                    } elseif (in_array($fileExtension, ["jpg", "jpeg", "png", "gif"])) {
                        echo "<img src='../uploads/{$post['accountname']}/posts/{$post["file"]}' width='400' /><br>";
                    }
                }
                // get number of comments
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
                uibuttons($post["id"], 'post', $post["likes"], $post["dislikes"], $comments + $replies);
                ?>
            </div></a> <!-- Close postgriditem -->
            <?php
            }
         } ?>
        </div><br> <!-- Close postgrid -->
        <?php
        // Process form submissions
        handleCommentSubmission($conn);
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


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {

        $('.likebutton, .dislikebutton').on('click', function () {
            let button = $(this);
            let action = button.data('action');
            let id = button.data('id');
            let type = button.data('type');
            let likeButton = $(`button[data-id='${id}'][data-action='like']`);
            let dislikeButton = $(`button[data-id='${id}'][data-action='dislike']`);
            let likeCountSpan = likeButton.find('span');
            let dislikeCountSpan = dislikeButton.find('span');
            $.post('../Libraries/search.resultsdislikeandlikelib.php', {
                action: action,
                contenttype: type,
                id: id
            }, function (response) {
                try {
                    let data = JSON.parse(response);

                    if (data.status === "success") {
                        // Update like and dislike counts
                        likeCountSpan.text(data.likes);
                        dislikeCountSpan.text(data.dislikes);

                        // Toggle active states
                        if (action === "like") {
                            likeButton.toggleClass('active', data.user_action === 'like');
                            dislikeButton.removeClass('active');
                        } else if (action === "dislike") {
                            dislikeButton.toggleClass('active', data.user_action === 'dislike');
                            likeButton.removeClass('active');
                        }
                    } else {
                        console.error("Error: " + data.message);
                    }
                } catch (e) {
                    console.error("Invalid JSON response");
                }
            }).fail(function () {
                console.error("Error processing request.");
            });
        });
    });
</script>




<?php
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
        <button type='button' style='display: inline;' class='$likeActive likebutton' data-action='like' data-id='{$id}' data-type='{$type}'><img class='likedislike' src='../Images/Posts-comments-replies/black/hollow/like.png'> <span>$likes</span></button>
        <button type='button' style='display: inline;' class='$dislikeActive dislikebutton' data-action='dislike' data-id='{$id}' data-type='{$type}'><img class='likedislike' src='../Images/Posts-comments-replies/black/hollow/dislike.png'> <span>$dislikes</span></button>
        <button type='button' style='display: inline;' class='commentbutton' data-action='comment' data-id='{$id}' data-type='{$type}'><img class='likedislike' src='../Images/Posts-comments-replies/black/hollow/comment.png'><span>$comments</span></button>
        <button class='report-button'>Report</button>

    ";
}
function timeelapsed($datetime)
{
    $timestamp = strtotime($datetime); // Convert the date string to a timestamp in seconds since the Unix epoch
    $time = time();
    $timeelapsed = $time - $timestamp; // Calculate the difference in seconds

    switch ($timeelapsed) {
        case ($timeelapsed < 60): // Less than a minute
            return "$timeelapsed second" . ($timeelapsed == 1 ? "" : "s") . " ago";
        case ($timeelapsed < 3600): // Less than an hour
            $minutes = floor($timeelapsed / 60);
            return "$minutes minute" . ($minutes == 1 ? "" : "s") . " ago";
        case ($timeelapsed < 86400): // Less than a day
            $hours = floor($timeelapsed / 3600);
            return "$hours hour" . ($hours == 1 ? "" : "s") . " ago";
        case ($timeelapsed < 604800): // Less than a week
            $days = floor($timeelapsed / 86400);
            return "$days day" . ($days == 1 ? "" : "s") . " ago";
        case ($timeelapsed < 2592000): // Less than a month
            $weeks = floor($timeelapsed / 604800);
            return "$weeks week" . ($weeks == 1 ? "" : "s") . " ago";
        case ($timeelapsed < 31536000): // Less than a year
            $months = floor($timeelapsed / 2592000);
            return "$months month" . ($months == 1 ? "" : "s") . " ago";
        case ($timeelapsed >= 31536000): // More than a year
            $years = floor($timeelapsed / 31536000);
            return "$years year" . ($years == 1 ? "" : "s") . " ago";
    }
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