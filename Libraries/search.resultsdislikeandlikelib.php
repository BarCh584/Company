<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "Company";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $action = $_POST["action"] ?? null;
    $content_id = $_POST["id"] ?? null;
    $content_type = $_POST["contenttype"] ?? null;

    if (!$action || !$content_id || !$content_type) {
        echo json_encode(["status" => "error", "message" => "Invalid input."]);
        exit;
    }

    $table = ($content_type === 'post') ? 'posts' : 'comments';

    $conn->begin_transaction();

    // Check existing action
    $stmt = $conn->prepare("SELECT action FROM user_interactions WHERE user_id = ? AND content_type = ? AND content_id = ?");
    $stmt->bind_param("isi", $user_id, $content_type, $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingAction = $result->fetch_assoc()['action'] ?? null;

    if ($existingAction === $action) {
        // Remove interaction
        $stmt = $conn->prepare("DELETE FROM user_interactions WHERE user_id = ? AND content_type = ? AND content_id = ?");
        $stmt->bind_param("isi", $user_id, $content_type, $content_id);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE $table SET {$action}s = {$action}s - 1 WHERE id = ? AND {$action}s > 0");
        $stmt->bind_param("i", $content_id);
        $stmt->execute();

        $newUserAction = null;
    } else {
        if ($existingAction) {
            $oppositeAction = ($action === 'like') ? 'dislike' : 'like';

            $stmt = $conn->prepare("UPDATE user_interactions SET action = ? WHERE user_id = ? AND content_type = ? AND content_id = ?");
            $stmt->bind_param("sisi", $action, $user_id, $content_type, $content_id);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE $table SET {$oppositeAction}s = {$oppositeAction}s - 1 WHERE id = ? AND {$oppositeAction}s > 0");
            $stmt->bind_param("i", $content_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO user_interactions (user_id, content_type, content_id, action) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $user_id, $content_type, $content_id, $action);
            $stmt->execute();
        }

        $stmt = $conn->prepare("UPDATE $table SET {$action}s = {$action}s + 1 WHERE id = ?");
        $stmt->bind_param("i", $content_id);
        $stmt->execute();

        $newUserAction = $action;
    }

    // Get updated counts
    $stmt = $conn->prepare("SELECT likes, dislikes FROM $table WHERE id = ?");
    $stmt->bind_param("i", $content_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $conn->commit();
    echo json_encode([
        "status" => "success",
        "likes" => $row['likes'],
        "dislikes" => $row['dislikes'],
        "user_action" => $newUserAction
    ]);
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Unauthorized request."]);
}
?>
