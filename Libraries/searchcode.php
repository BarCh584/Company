<?php
session_start();
if (!isset($_SESSION['id'])) {
    die("You must be logged in to post comments.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $uname = $_POST["username"];
        if(strlen($uname) < 1) {
            $stmt = $conn->prepare("SELECT username FROM users ORDER BY username ASC");
        }
        $stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE CONCAT(?, '%')");
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<div>";
            while ($row = $result->fetch_assoc()) {
                echo "<li><a href='search.results.php?username=" . htmlspecialchars($row["username"]) . "'>" . htmlspecialchars($row["username"]) . "</a></li><br>";
            }
            echo "</div>";
        } else {
            echo "<div><p>User not found</p></div>";
        }
        $stmt->close();
        $conn->close();
    } else {
        echo "No username entered";
    }
}
?>
