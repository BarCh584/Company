<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("message");
    showdmaccountlist($_SESSION["username"]);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_SESSION["username"] != null && $_GET["username"] != null && !empty($_POST["message"])) {
            createmessage($_SESSION["username"], $_GET["username"], $_POST["message"]);
            showchatmessages();
        } else {
            echo "<p style='color:red;'>You can't send an empty message</p>";
        }
    }
    ?>
    <form method="POST" style="margin-left: 30vw">
        <input class="textinpfld" type="text" name="message" placeholder="Message"><br>
        <input class="submitbutton" type="submit" value="Send message">
    </form>
    <?php // Show all direct messages between myself and everyone else I have messaged
    function showdmaccountlist($thisaccount)
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed, error code: " . $conn->connect_error);
        }
        
        // Fetch all relevant messages involving the user
        $accountlist = $conn->prepare("SELECT DISTINCT sender, receiver, message FROM messages WHERE sender = ? OR receiver = ?");
        $accountlist->bind_param("ss", $thisaccount, $thisaccount);
        $accountlist->execute();
        $accountlistresult = $accountlist->get_result();
    
        $processedPairs = []; // To keep track of unique pairs
    
        if ($accountlistresult->num_rows > 0) {
            echo "<ul class='outnavbar' id='innavbar' style='margin-left: 15vw; width: 15vw; border-right: 1px solid gray; border-left: 1px solid gray;'>";
            while ($row = $accountlistresult->fetch_assoc()) {
                // Normalize the pair to ensure uniqueness
                $pair = [$row["sender"], $row["receiver"]];
                sort($pair); // Sort alphabetically to normalize
                $pairKey = implode("-", $pair); // Create a unique string key for the pair
    
                if (!in_array($pairKey, $processedPairs)) {
                    // If this pair hasn't been processed, display it
                    $processedPairs[] = $pairKey;?>
                    <a href="message.php?username=<?php if($row["sender"] != $_SESSION["username"]) print($row["sender"]); else if($row["receiver"] != $_SESSION["username"]) print($row["receiver"]); ?>"> <?php if($row["sender"] != $_SESSION["username"]) print($row["sender"]); else if($row["receiver"] != $_SESSION["username"]) print($row["receiver"]); ?> </a>
                <?php }
            }
            echo "</ul>";
        } else {
            echo "No messages found.";
        }
        
        $accountlist->close();
        $conn->close();
    }
    function createmessage($sender, $receiver, $message)
    {

        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed, error code: " . $conn->connect_error);
        }



        $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $sender, $receiver, $message);
        if ($stmt->execute()) {
            header("Refresh:0");
            echo "Message sent";
        } else {
            echo "Message failed to send";
            header("Refresh:0");
        }// Redirect to the same page to avoid form resubmission
        exit();
    }
    ?>



    <?php
    /* Display chat messages */

    function showchatmessages()
    {

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        $usera = $_SESSION["username"];
        if(isset($_GET["username"])) $userb = $_GET["username"];
        if ($conn->connect_error) {
            die("Connection failed, error code: " . $conn->connect_error);
        }
        // If messages are found check if myself or the other person is the sender or receiver of the message for a complete chat history
        $sqlstmt = $conn->prepare("SELECT * FROM messages WHERE sender = ? AND receiver = ? OR sender = ? AND receiver = ?");
        $sqlstmt->bind_param("ssss", $usera, $userb, $userb, $usera);
        $sqlstmt->execute();
        $sqlresult = $sqlstmt->get_result();
        if ($sqlresult->num_rows > 0) {
            echo "<div class='postgrid' style='margin-left: 30vw'>";
            while ($row = $sqlresult->fetch_assoc()) {
                echo "<div class='postgriditem'>";
                echo "<p>$row[sender]</p>";
                echo "<br>" . $row["message"];
                echo "</div>";
            }
            echo "</div>";
        }
    }
    showchatmessages();
    ?>
</body>

</html>