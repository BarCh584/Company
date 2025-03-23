<?php
include_once '../Libraries/navbar.php';
include '../Libraries/subscription_plan.php';
include '../Libraries/currency_converter.php';
createnavbar("search");
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "Company";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    var datatype;
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
            datatype = this.getAttribute('data-type');
            $(".datatype").attr("name", "datatype");
            $(".datatype").val(datatype);
            console.log(datatype);
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

        // Dark and light mode
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
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="normalcontentnavbar">
        <div class="postgrid">
            <?php
            if (isset($_GET['postid'])) {
                $postid = $_GET['postid'];
                $poststmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
                $poststmt->bind_param("i", $postid);
                $poststmt->execute();
                $postresult = $poststmt->get_result();
                while ($post = $postresult->fetch_assoc()) { ?>
                    <div class="postgriditem">
                        <div style="display: flex;">
                            <?php print (getuserprofileimg($post["accountname"])); ?>
                            <h4><?= $post["accountname"] ?> <small><?php print (timeelapsed($post["createdat"])); ?></small>
                            </h4>
                        </div>
                        <p><?= $post["title"] ?></p>
                        <p><?= $post["comment"] ?></p>
                        <?php
                        if ($post["file"]) {
                            $fileExtension = strtolower(pathinfo($post["file"], PATHINFO_EXTENSION));
                            if (in_array($fileExtension, ["mp3", "mp4", "wav"])) {
                                echo "<video width='400' controls><source src='../uploads/{$_GET['username']}/posts/{$post["file"]}' type='video/mp4'></video>";
                            } elseif (in_array($fileExtension, ["jpg", "jpeg", "png", "gif"])) {
                                echo "<img src='../uploads/{$post['accountname']}/posts/{$post["file"]}' width='400' /><br>";
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
                                        <div style="display: flex;">
                                            <?php print (getuserprofileimg($comment["username"])); ?>
                                            <p><strong><?= htmlspecialchars($comment["username"]) ?>
                                                    <small><?php print (timeelapsed($comment["createdat"])); ?></small></strong></p>
                                        </div>
                                        <p><?= htmlspecialchars($comment["comment"]) ?></p>
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
                                                    <div style="display: flex;">
                                                        <?php print (getuserprofileimg($reply["username"])); ?>
                                                        <p><strong><?= htmlspecialchars($reply["username"]) ?></strong><small><?php print (timeelapsed($reply["createdat"])); ?></small>
                                                        </p>
                                                    </div>
                                                    <p><?= htmlspecialchars($reply["reply"]) ?></p>
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
                    </div></a> <!-- Close postgriditem -->
                    <?php
                }
            }
            handleCommentSubmission($conn);
            handleReplySubmission($conn);
            // Close database connection
            $conn->close();
            ?>
        </div>
    </div>
    <div class='report-banner'>
        <p>Report content for:</p><br>
        <div class='reasons'>
            <form method="POST">
                <input type="hidden" class='datatype' id="datatype" name="datatype" value="datatype">
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
                    <input type="radio" name="reason" value="Promotes and/or sells illegal activities">Promotes
                    and/or
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

</html>