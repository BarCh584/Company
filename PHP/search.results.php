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

?>
<script src="../Libraries/jquery/jquery-3.6.0.min.js"></script>
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
        $(".commentbutton").on("click", function() {
            let id = $(this).data("id");
            let type = $(this).data("type");
            window.location.href = `search.postid.php?postid=${id}`;
        });
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
        $(window).keydown(function (e) {
            if (e.key === 'Escape') {
                $('.report-banner').css('visibility', 'hidden');
                $('body > *:not(.report-banner)').css('filter', 'blur(0px)');
                $('body > *:not(.report-banner)').css('pointer-events', 'all');
            }
        });
        // Report functionality
        $(".report-banner").css("visibility", "hidden");
        $(".report-button").click(function () {
            // Blur the background and make it clickable per default 
            var datatype = this.getAttribute('data-type');
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




</html>