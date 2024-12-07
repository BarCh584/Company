<?php
session_start(); // Include this if sessions are used
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Company";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["sender"]) && isset($_GET["receiver"])) {
    $sender = $_GET["sender"];
    $receiver = $_GET["receiver"];
    
    // Fetch the latest messages between sender and receiver
    $stmt = $conn->prepare("SELECT * FROM messages WHERE 
        (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?) ORDER BY createdat ASC");
    $stmt->bind_param("ssss", $sender, $receiver, $receiver, $sender);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'sender' => $row['sender'],
            'message' => $row['message'],
            'createdat' => $row['createdat']
        ];
    }

    echo json_encode($messages);
    $stmt->close();
    $conn->close();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sender = $_SESSION["username"] ?? null;
    $receiver = $_POST["receiver"] ?? null;
    $message = $_POST["message"] ?? null;

    if ($sender && $receiver && $message) {
        // Save the message in the database
        $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $sender, $receiver, $message);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => $message,
                "sender" => $sender,
                "createdat" => date("Y-m-d H:i:s")
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "Message failed to send"]);
        }
        
        $stmt->close();
    }
}

$conn->close();