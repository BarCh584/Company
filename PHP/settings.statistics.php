<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("settings.profile");
    createsettingsnavbar('settings.statistics');

    $servername = "localhost";
    $dbusername = "root";
    $password = "";
    $dbname = "Company";
    $conn = new mysqli($servername, $dbusername, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: {$conn->connect_error}");
    }
    $followerchartarray = array();
    $followerstmt = $conn->prepare("SELECT followers FROM followerchartmonthlystats WHERE username = ?");
    $followerstmt->bind_param("s", $_SESSION['username']);
    $followerstmt->execute();
    $followerstmt->bind_result($followers);
    while ($followerstmt->fetch()) {
        array_push($followerchartarray, $followers);
    }
    $followerstmt->close();

    ?>
    
    <div class="normalcontentnavbar">
        <h1>Follower count</h1>
        <canvas id="followerchart"></canvas>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        if (window.innerWidth < 768) {
            $(".innavbar").hide();
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script>
        let xValues = [];
        const yValues = <?php echo json_encode($followerchartarray); ?>;
        if (yValues.every(value => typeof value === 'number')) {
            console.log(Math.max(...yValues));
        } else {
            console.error('yValues contains non-numeric values');
        }

        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        for (let i = 0; i < yValues.length; i++) {
            xValues[i] = monthNames[i % 12];
        }
        new Chart("followerchart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    fill: false,
                    lineTension: 0,
                    backgroundColor: "rgb(255, 255, 255)",
                    borderColor: "rgb(255, 255, 255)",
                    data: yValues
                }]
            },
            options: {
                legend: { display: false },
                scales: {
                    // Round to the nearest 100 for cleaner look
                    yAxes: [{ ticks: { min: 0, max: Math.ceil(Math.max(...yValues) / 100) * 100 } }], 
                }
            }
        });
    </script>

</body>

</html>