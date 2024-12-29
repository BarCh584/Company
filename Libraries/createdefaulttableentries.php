<?php
/* Create an entry at the beginning of each day at 00:00:00 UTC */
if (date('H') == 0 && date('i') == 0 && date('s') == 0) {
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
        $followerstatstmt = $conn->prepare("INSERT INTO dailyfollowerchart (followers, username) VALUES (?, ?)");
        $followerstatstmt->bind_param("is", $followers, $username);
        $followerstatstmt->execute();
    }

    // Insert the likes and dislikes count
    $getliksanddislikesstmt = $conn->prepare("SELECT likes, dislikes FROM posts");
    $getliksanddislikesstmt->execute();
    $getliksanddislikesstmt->store_result();
    $getliksanddislikesstmt->bind_result($likes, $dislikes);
    while($getliksanddislikesstmt->fetch()) {
        $likesanddislikescount = $conn->prepare("INSERT INTO dailylikesdislikeschart (likes, dislikes, username) VALUES (?, ?, ?)");
        $likesanddislikescount->bind_param("iis", $likes, $dislikes, $username);
        $likesanddislikescount->execute();
    }
    $conn->close();
}
?>