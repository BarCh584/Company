<?php
    $servername = "localhost";
    $dbusername = "root";
    $password = "";
    $dbname = "Company";
    // Establish database connection
    $conn = new mysqli($servername, $dbusername, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: {$conn->connect_error}");
    }
    $userstmt = $conn->prepare("SELECT users.priceforcontentcurrency, users.priceforcontentint, subscriptions.creator, subscriptions.subscriber FROM users INNER JOIN subscriptions ON users.username = subscriptions.creator");
    $userstmt->execute();
    $userstmt->store_result();
    $userstmt->bind_result($currency, $amount, $creator, $subscriber);
    while($userstmt->fetch()) {
        // Check if the last payment was exactly 1 month ago
        $checkstmt = $conn->prepare("SELECT MAX(payment_date) FROM monthlypaymentchart WHERE creator = ? AND subscriber = ?");
        $checkstmt->bind_param("ss", $creator, $subscriber);
        $checkstmt->execute();
        $checkstmt->bind_result($last_payment_date);
        $checkstmt->fetch();
        $checkstmt->close();
        
        $one_month_ago = date('Y-m-d', strtotime('-1 month'));
        if ($last_payment_date == $one_month_ago) {
            $monthlypaymentstmt = $conn->prepare("INSERT INTO monthlypaymentchart (currency, amount, creator, subscriber) VALUES (?, ?, ?, ?)");
            $monthlypaymentstmt->bind_param("siss", $currency, $amount, $creator, $subscriber);
            $monthlypaymentstmt->execute();
            $monthlypaymentstmt->close();
        }
    }
    $userstmt->close();
    $conn->close();
?>