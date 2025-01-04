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
        $monthlypaymentstmt = $conn->prepare("INSERT INTO monthlypaymentchart (currency, amount, creator, subscriber) VALUES (?, ?, ?, ?)");
        $monthlypaymentstmt->bind_param("siss", $currency, $amount, $creator, $subscriber);
        $monthlypaymentstmt->execute();
    }
?>