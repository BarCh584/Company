<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?= time(); ?>">
    <title>Document</title>
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("message");

    $currentUser = $_SESSION["username"];
    $chatUser = $_GET["username"] ?? null;

    // Show DM account list
    showdmaccountlist($currentUser);

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $message = $_POST["message"] ?? '';
        if ($currentUser && $chatUser && !empty($message)) {
            createmessage($currentUser, $chatUser, $message);
            showchatmessages($currentUser, $chatUser);
        } else {
            echo "<p style='color:red;'>You can't send an empty message</p>";
        }
    }
    ?>
    <div class="normalcontentnavbar">

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function () {
                let lastMessageTimestamp = null; // Track the latest message timestamp
                console.log("Document ready");
                function scrolltobottom() {
                    const chatMessages = $('.postgrid');
                    if (chatMessages.length) {
                        chatMessages.scrollTop(chatMessages[0].scrollHeight);
                        console.log("Scrolled to bottom");
                    }
                }

                // Scroll to the bottom on page load
                scrolltobottom();

                // Send message via AJAX
                $("#messageform").submit(function (e) {
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "../Libraries/ajax_handler.php",
                        data: {
                            receiver: "<?= $chatUser ?>",
                            sender: "<?= $currentUser ?>",
                            message: $("input[name='message']").val()
                        },
                        success: function (response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                $(".textinpfld").val(''); // Clear the input field
                                pollMessages(() => scrolltobottom()); // Fetch new messages and then scroll
                            } else {
                                alert(data.error);
                            }
                        }
                    });
                });

                // Polling function to fetch new messages
                function pollMessages(callback) {
                    $.ajax({
                        type: "GET",
                        url: "../Libraries/ajax_handler.php",
                        data: {
                            sender: "<?= $currentUser ?>",
                            receiver: "<?= $chatUser ?>"
                        },
                        success: function (response) {
                            const messages = JSON.parse(response);

                            if (messages.length > 0) {
                                let newMessages = false; // Flag to detect new messages
                                messages.forEach(function (message) {
                                    const messageTimestamp = message.createdat;

                                    // Check if the message is new
                                    if (!lastMessageTimestamp || messageTimestamp > lastMessageTimestamp) {
                                        newMessages = true;
                                        $(".postgrid").append(
                                            `<div class='postgriditem'>
                                    <h3>${message.sender}: ${message.message} 
                                    <small style='color: #3f3f3f'>${message.createdat}</small></h3>
                                </div><br>`
                                        );

                                        // Update the lastMessageTimestamp to the latest one
                                        lastMessageTimestamp = messageTimestamp;
                                    }
                                });

                                // Call the callback (e.g., scrolltobottom) if new messages were added
                                if (newMessages && callback) {
                                    callback();
                                }
                            }
                        }
                    });
                }

                // Start polling when the page is ready
                setInterval(() => pollMessages(), 3000); // Poll every 3 seconds
            });


        </script>

        <script>
            /* Dont allow a resubmit of the form */
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            /* Always scroll to the bottom of the postgrid */
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth' // Optional, adds animation
            });
        </script>
        <?php
        function connectDatabase()
        {
            $conn = new mysqli("localhost", "root", "", "Company");
            if ($conn->connect_error) {
                die("Database connection failed: " . $conn->connect_error);
            }
            return $conn;
        }

        function showdmaccountlist($currentUser)
        {
            $conn = connectDatabase();
            $stmt = $conn->prepare("
            SELECT DISTINCT sender, receiver 
            FROM messages 
            WHERE sender = ? OR receiver = ?
        ");
            $stmt->bind_param("ss", $currentUser, $currentUser);
            $stmt->execute();
            $result = $stmt->get_result();

            $pairs = [];
            echo "<ul class='innavbar'>";
            while ($row = $result->fetch_assoc()) {
                $pair = [$row["sender"], $row["receiver"]];
                sort($pair);
                $pairKey = implode("-", $pair);

                if (!in_array($pairKey, $pairs)) {
                    $pairs[] = $pairKey;
                    $contact = $row["sender"] === $currentUser ? $row["receiver"] : $row["sender"];
                    echo "<li><a class='dmitem" . (isset($_GET["username"]) ? " hidden" : " visible") . "' href='message.php?username=$contact'><h4>$contact</h4></a></li>";
                }
            }
            echo "</ul>";

            $stmt->close();
            $conn->close();
        }

        function createmessage($sender, $receiver, $message)
        {
            $conn = connectDatabase();
            $stmt = $conn->prepare("
            INSERT INTO messages (sender, receiver, message) 
            VALUES (?, ?, ?)
        ");
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
            $conn->close();
        }

        function showchatmessages($currentUser, $chatUser)
        {
            if (!$chatUser)
                return;

            $conn = connectDatabase();
            $stmt = $conn->prepare("
            SELECT * FROM messages 
            WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)
            ORDER BY createdat ASC
        ");
            $stmt->bind_param("ssss", $currentUser, $chatUser, $chatUser, $currentUser);
            $stmt->execute();
            $result = $stmt->get_result();

            // Ensure only one postgrid
            echo "<div class='postgrid'>";
            while ($row = $result->fetch_assoc()) {
                echo "<div class='postgriditem'>
                <h3>{$row['sender']} 
                <small style='color: #3f3f3f'>{$row['createdat']}</small>
                <br>{$row['message']} 
                
            </h3>";
                echo "</div><br>";
            }
            echo "</div>";

            $stmt->close();
            $conn->close();
        }

        showchatmessages($currentUser, $chatUser);
        if ($chatUser): ?>
            <form method="POST" id="messageform">
                <input class="textinpfld" id="dmtextinpfld" type="text" name="message" placeholder="Message"><br>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>