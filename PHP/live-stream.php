<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../Logo2.png">
</head>

<body>
    <?php
    include('../Libraries/navbar.php');
    createnavbar("live-stream");
    ?>
    <div class="container">
        <div style="background-color: #f1f1f1; margin-left: 15vw;">
            <video id="video" autoplay></video>
        </div>
        <div style="margin-left: 15vw;">
            <button id="startbutton">Start</button>
            <button id="stopbutton">Stop</button>
        </div>
    </div>
    <script>

        const videoelement = document.getElementById("video");
        const startbutton = document.getElementById("startbutton");
        const stopbutton = document.getElementById("stopbutton");
        var displaymediaoption = {
            video: {
                cursor: "always",
                height: 1000,
                width: 1000
            },
            audio: true
        };
        startbutton.addEventListener("click", function (event) {
            startcapture();
        }, false);
        stopbutton.addEventListener("click", function (event) {
            stopcapture();
        }, false);
        async function startcapture() {
            try {
                videoelement.srcObject = await navigator.mediaDevices.getDisplayMedia(displaymediaoption);
                dumpoptionsinfo();
                sendStreamToServer(videoelement.srcObject);
            } catch (err) {
                console.error("Error: " + err);
            }
        }
        function stopcapture() {
            let tracks = videoelement.srcObject.getTracks();
            tracks.forEach(track => track.stop());
            videoelement.srcObject = null;
        }
        function dumpoptionsinfo() {
            const videotrack = videoelement.srcObject.getVideoTracks()[0];
            console.log("Track");
            console.log(JSON.stringify(videotrack.getSettings(), null, 2));
            console.info("Track constraints:");
            console.info(JSON.stringify(videotrack.getConstraints(), null, 2));
        }
        function sendStreamToServer(stream) {
            const mediaRecorder = new MediaRecorder(stream);
            mediaRecorder.ondataavailable = function (event) {
                if (event.data.size > 0) {
                    sendData(event.data);
                }
            };
            mediaRecorder.start(2000); // Send data in chunks every second
        }
        function sendData(data) {
            const xhr = new XMLHttpRequest();
            const username = "<?php echo $_SESSION['username']; ?>";
            xhr.open("POST", "show-live-stream.php", true);
            xhr.setRequestHeader("Content-Type", "application/octet-stream");
            xhr.send(data);
        }
    </script>
</body>

</html>