<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection: ({$conn->resourceId})\n";
    }
    public function onMessage(ConnectionInterface $from, $msg) {
        echo "Received message from client {$from->resourceId}: $msg\n";

        $data = json_decode($msg, true);

        if (isset($data['type'], $data['sender'], $data['receiver'], $data['message']) && $data['type'] === 'message') {
            $sender = $data['sender'];
            $receiver = $data['receiver'];
            $message = $data['message'];

            echo "Parsed message: Sender=$sender, Receiver=$receiver, Message=$message\n";

            $conn = new mysqli("localhost", "root", "", "Company");
            if ($conn->connect_error) {
                echo "Database connection error: " . $conn->connect_error . "\n";
                return;
            }

            $stmt = $conn->prepare("INSERT INTO messages (sender, receiver, message, createdat) VALUES (?, ?, ?, NOW())");
            if ($stmt) {
                if ($stmt->bind_param("sss", $sender, $receiver, $message) && $stmt->execute()) {
                    echo "Message successfully saved to database.\n";
                } else {
                    echo "Failed to save message to database: " . $conn->error . "\n";
                }
                $stmt->close();
            } else {
                echo "Failed to prepare database statement: " . $conn->error . "\n";
            }
            $conn->close();

            foreach ($this->clients as $client) {
                $response = json_encode([
                    'type' => 'message',
                    'sender' => $sender,
                    'receiver' => $receiver,
                    'message' => $message,
                    'createdat' => date("Y-m-d H:i:s")
                ]);
                echo "Sending message to client {$client->resourceId}: $response\n";

                $client->send($response);
            }
        } else {
            echo "Invalid message format received.\n";
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = new \Ratchet\App('localhost', 8080);
$server->route('/chat', new ChatServer);
$server->run();
