<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?= time(); ?>">
    <title>Chat</title>
    <link rel="icon" href="../Logo.png">
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("message");
    $currentUser = $_SESSION["username"];
    $chatUser = htmlspecialchars($_GET["username"] ?? '', ENT_QUOTES, 'UTF-8');

    showdmaccountlist($currentUser);

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
                echo "<li><a class='dmitem' href='message.php?username=" . htmlspecialchars($contact, ENT_QUOTES, 'UTF-8') . "'><h4>$contact</h4></a></li>";
            }
        }
        echo "</ul>";

        $stmt->close();
        $conn->close();
    }

    function showchatmessages($currentUser, $chatUser)
    {
        if (!$chatUser)
            return;

        echo "<h1>Chat with $chatUser</h1>";

        $conn = connectDatabase();
        $stmt = $conn->prepare("
            SELECT * FROM messages 
            WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)
            ORDER BY createdat ASC
        ");
        $stmt->bind_param("ssss", $currentUser, $chatUser, $chatUser, $currentUser);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<div id='chatWindow' class='postgrid'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='postgriditem'>
                <h3>" . htmlspecialchars($row['sender'], ENT_QUOTES, 'UTF-8') . " 
                <small style='color: #3f3f3f'>" . htmlspecialchars($row['createdat'], ENT_QUOTES, 'UTF-8') . "</small></h3>
                <p>" . htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8') . "</p>
            </div><br>";
        }
        echo "</div>";

        $stmt->close();
        $conn->close();
    }

    if ($chatUser) {
        showchatmessages($currentUser, $chatUser);
    }
    ?>

    <div class="normalcontentnavbar">
        <?php if ($chatUser): ?>
            <form id="messageForm">
                <input class="textinpfld" id="dmtextinpfld" type="text" name="message" placeholder="Message" required>
            </form>
        <?php endif; ?>
    </div>

    <script>
        const currentUser = "<?= $currentUser ?>";
        const chatUser = "<?= $chatUser ?>";
        const chatWindow = document.getElementById('chatWindow');
        const messageInput = document.getElementById('dmtextinpfld');

        let ws = new WebSocket('ws://localhost:8080/chat');

        ws.onopen = () => {
            console.log('WebSocket connection established.');
        };

        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                //console.log('Received message:', data);

                if (data.type === 'message' &&
                    ((data.sender === currentUser && data.receiver === chatUser) ||
                        (data.sender === chatUser && data.receiver === currentUser))) {
                    //console.log('Displaying message:', data);
                    displayMessage(data);
                } else {
                    console.log('Message not relevant to this chat:', data);
                }
            } catch (error) {
                console.error('Error processing WebSocket message:', error);
            }
        };

        ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        ws.onclose = () => {
            console.log('WebSocket connection closed. Attempting to reconnect...');
            setTimeout(() => {
                ws = new WebSocket('ws://localhost:8080/chat');
            }, 5000);
        };

        document.getElementById('messageForm').addEventListener('submit', (e) => {
            e.preventDefault();

            const message = messageInput.value.trim();
            if (message) {
                const data = {
                    type: 'message',
                    sender: currentUser,
                    receiver: chatUser,
                    message: message
                };
                //console.log('Sending message:', data);

                if (ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify(data));
                    messageInput.value = '';
                } else {
                    console.error('WebSocket is not open. Cannot send message.');
                }
            } else {
                console.log('Empty message. Skipping send.');
            }
        });

        function displayMessage({ sender, message, createdat }) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'postgriditem';
            messageDiv.innerHTML = `
            <h3>${sender} 
            <small style="color: #3f3f3f">${createdat}</small></h3>
            <p>${message}</p><br>
        `;
            chatWindow.appendChild(messageDiv);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }
    </script>

</body>

</html>