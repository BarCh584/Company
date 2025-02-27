<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?= time(); ?>">
    <title>Chat</title>
    <link rel="icon" href="../Logo2.png">
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

        echo "<h1 id='chattitle'>Chat with $chatUser</h1>";

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
            $directory = "../uploads/" . $row['sender'] . "/profileimg/profile_picture.";
            $imgformats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'];
            $filesfound = [];
            foreach ($imgformats as $format) {
                $pattern = $directory . $format;
                $filesfound = array_merge($filesfound, glob($pattern)); // Append found files to $filesfound
            }
            if (count($filesfound) == 0) {
                $filesfound = ["../Images/Navbar/black/hollow/settings.profile.png"];
            }
            echo "<div class='messagegriditem'>
                        <img src='{$filesfound[0]}' class='messageprofileimg' alt='Profile picture'>
                        <div>
                            <h3 style='margin: 0;'>" . htmlspecialchars($row['sender'], ENT_QUOTES, 'UTF-8') . " 
                            <small style='color: #3f3f3f'>" . htmlspecialchars($row['createdat'], ENT_QUOTES, 'UTF-8') . "</small></h3>
                            <p style='margin: 0;'>" . htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8') . "</p>
                        </div>
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

    <div class="innormalcontentnavbar">
        <?php if ($chatUser): ?>
            <div class="messageitems">
                <form id="messageForm">
                    <input class="textinpfld" id="dmtextinpfld" type="text" name="message" placeholder="Message" required>
                </form>
                <div id="emojidiv" style="position: relative;">
                    <button id="emojibutton"><img id="smileyimg" src="../Images/message/black/smiley.png"></button>
                    <emoji-picker></emoji-picker>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script type="module">
        import 'https://unpkg.com/emoji-picker-element';

        document.addEventListener("DOMContentLoaded", function () {
            const emojiPicker = document.querySelector("emoji-picker");
            const textInput = document.getElementById("dmtextinpfld");
            const emojiButton = document.getElementById("emojibutton");

            // Set initial state of emojiPicker to "none"
            emojiPicker.style.display = "none";

            emojiButton.addEventListener("click", () => {
                emojiPicker.style.display = emojiPicker.style.display === "none" ? "block" : "none";
            });

            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                $("#smileyimg").each(function () {
                    this.src = this.src.replace("black", "white"); // White icons for dark mode
                });
            } else {
                $("#smileyimg").each(function () {
                    this.src = this.src.replace("white", "black"); // Black icons for light mode
                });
            }

            emojiPicker.addEventListener("emoji-click", (event) => {
                textInput.value += event.detail.unicode;
            });

            document.addEventListener("click", (event) => {
                if (!emojiButton.contains(event.target) && !emojiPicker.contains(event.target)) {
                    emojiPicker.style.display = "none";
                }
            });
        });
    </script>
    <script>

        const currentUser = "<?= $currentUser ?>";
        const chatUser = "<?= $chatUser ?>";
        const chatWindow = document.getElementById('chatWindow');
        const messageInput = document.getElementById('dmtextinpfld');

        let ws = new WebSocket('ws://192.168.178.180:8080/chat');

        ws.onopen = () => {
            chatWindow.scrollTop = chatWindow.scrollHeight;
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
                ws = new WebSocket('ws://192.168.178.180:8080/chat');
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
            messageDiv.className = 'messagegriditem';

            const directory = `../uploads/${sender}/profileimg/`;
            const imgformats = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff'];
            let filesfound = [];
            for (const format of imgformats) {
                const pattern = `${directory}profile_picture.${format}`;
                const xhr = new XMLHttpRequest();
                xhr.open('HEAD', pattern, false);
                xhr.send();
                if (xhr.status !== 404) {
                    filesfound.push(pattern);
                    break; // Stop after finding the first image
                }
            }
            if (filesfound.length === 0) {
                filesfound = ["../Images/Navbar/black/hollow/settings.profile.png"];
            }

            messageDiv.innerHTML = `<img src='${filesfound[0]}' class='messageprofileimg' alt='Profile picture'><div><h3 style='margin: 0;'>${sender} <small style='color: #3f3f3f'>${createdat}</small></h3><p style='margin: 0;'>${message}</p></div>`;
            chatWindow.appendChild(messageDiv);
            chatWindow.appendChild(document.createElement('br'));
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }
    </script>


    </script>
</body>

</html>