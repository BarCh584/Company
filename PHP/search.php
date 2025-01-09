<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../Logo.png">
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("search");
    ?>
    <ul class="innavbar">
        <input type="text" oninput="search();" class="searchbar" placeholder="Search for a username" id="username">
        <div id="search-results"></div>
    </ul>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function search() {
            const username = document.getElementById("username").value;
            if (username.length >= 0) {
                $.ajax({
                    url: '../Libraries/searchcode.php',
                    type: 'POST',
                    data: { username: username },
                    success: function (response) {
                        $('#search-results').html(response);
                    },
                    error: function () {
                        alert('An error occurred while processing your request.');
                    }

                });
            }
        }

    </script>
</body>

</html>