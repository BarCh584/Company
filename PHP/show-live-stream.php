<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../Logo.png">
</head>

<body>
    <div style="background-color: #1f1f1f; margin-left: 15vw;">
        <video id="video" autoplay controls>
            <source src="C:\Users\Christian\AppData\Local\Temp\livestream\livestream_sfcep68d1d3j5bsukkiecgmkgk.webm" type="video/webm">
        </video>
    </div>
    <?php
    include('../Libraries/navbar.php');
    createnavbar("live-stream");

    // Display the live stream
    header(("Content-Type: video/webm"));
    header("Cache-Control: no-cache, must-revalidate");
    header("Connection: keep-alive");
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        // check if directory exists
        if(!file_exists(sys_get_temp_dir() . "\livestream")) {
            mkdir(sys_get_temp_dir() . "\livestream");
        }
        $data = file_get_contents("php://input");
        $tempfiledir = sys_get_temp_dir() . "\livestream\livestream_" . session_id() . ".webm";
        file_put_contents($tempfiledir, $data, FILE_APPEND); // Append means that the data is added to the end of the file

        echo "Frame received" . $data;
        exit;
    }
    $tempfile = sys_get_temp_dir() . "\livestream\livestream_" . session_id() . ".webm";
    if(file_exists($tempfile)) {
        $filehandle = fopen($tempfile, "rb");
        while(!feof($filehandle)) {
            echo fread($filehandle, 8192);
            ob_flush();
            flush();
        }
        fclose($filehandle);
    } else {
        http_response_code(404);
        echo "Stream not found";
    }
    ?>
</body>

</html>