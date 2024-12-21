<?php
/* Create an entry at the beginning of each month at 00:00:00 UTC */
if (date('d') == 1 && date('H') == 0 && date('i') == 0 && date('s') == 0) {
    $servername = "localhost";
    $dbusername = "root";
    $password = "";
    $dbname = "Company";
    // Establish database connection
    $conn = new mysqli($servername, $dbusername, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: {$conn->connect_error}");
    }
    $getfollowcountstmt = $conn->prepare("SELECT followers, username FROM users");
    $getfollowcountstmt->execute();
    $getfollowcountstmt->store_result();
    $getfollowcountstmt->bind_result($followers, $username);
    while ($getfollowcountstmt->fetch()) {
        // Process each username and followers count
        $followerstatstmt = $conn->prepare("INSERT INTO followerchartmonthlystats (followers, username) VALUES (?, ?)");
        $followerstatstmt->bind_param("is", $followers, $username);
        $followerstatstmt->execute();
    }
    $conn->close();
}
?>