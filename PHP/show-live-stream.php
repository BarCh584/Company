<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    include('../Libraries/navbar.php');
    createnavbar("live-stream");

    // Check if data is received
        echo json_encode(["status" => "success", "message" => $_SERVER["REQUEST_METHOD"]]);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = file_get_contents("php://input");
            $path = "../downloads/" . $_SESSION["username"] . "/streams";
            if (!is_dir($path)) {
                mkdir($path, 0777, true); // create directory where the stream will be stored with all permissions
            }
            $filename = $path . "/stream_" . date("Y-m-d_H-i-s") . uniqid() . ".webm";
            if (file_put_contents($filename, $data)) {
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Stream saved successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to save stream"]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Wrong request method! Server request method is " . $_SERVER["REQUEST_METHOD"]]);
        }
    ?>
</body>

</html>