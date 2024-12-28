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
    include("../Libraries/createdefaulttableentries.php");
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
    // for followerchart
    $followerchartarray = array();
    $followerstmt = $conn->prepare("SELECT followers FROM dailyfollowerchart WHERE username = ?");
    $followerstmt->bind_param("s", $_SESSION['username']);
    $followerstmt->execute();
    $followerstmt->bind_result($followers);
    while ($followerstmt->fetch()) {
        array_push($followerchartarray, $followers);
    }
    $followerstmt->close();
    // for likes and dislikes
    $likesanddislikeschartarraylikes = array();
    $likesanddislikeschartarraydislikes = array();
    $likesanddislikesstmt = $conn->prepare("SELECT likes, dislikes FROM dailylikesdislikeschart WHERE username = ?");
    $likesanddislikesstmt->bind_param("s", $_SESSION['username']);
    $likesanddislikesstmt->execute();
    $likesanddislikesstmt->bind_result($likes, $dislikes);
    while ($likesanddislikesstmt->fetch()) {
        array_push($likesanddislikeschartarraylikes, $likes);
        array_push($likesanddislikeschartarraydislikes, $dislikes);
    }
    ?>

    <div class="normalcontentnavbar">
        <h1>Analytics</h1>
        <select id="charttimeoptions" onchange="changechart();">
            <option value="week">Weekly</option>
            <option value="month">Monthly</option>
            <option value="Q1">3 Months</option>
            <option value="half">6 Months</option>
            <option value="Q3">9 Months</option>
            <option value="year">Yearly</option>
            <option value="all">All time</option>
        </select>
        <select id="chartoptions" onchange="changechart();">
            <option value="follower">Follower count</option>
            <option value="likes">Likes count</option>
            <option value="dislikes">Dislikes count</option>
        </select>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        if (window.innerWidth < 768) {
            $(".innavbar").hide();
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script>
        function changechart() {
            // yvalues
            let selectchartoption = document.getElementById("chartoptions").value;
            let optionselected = document.getElementById("charttimeoptions").value;
            let yValues;
            if (selectchartoption == "follower") {
                yValues = <?php echo json_encode($followerchartarray); ?>;
            } else if (selectchartoption == "likes") {
                yValues = <?php echo json_encode($likesanddislikeschartarraylikes); ?>;
            } else if (selectchartoption == "dislikes") {
                yValues = <?php echo json_encode($likesanddislikeschartarraydislikes); ?>;
            } else {
                yValues = <?php echo json_encode($followerchartarray); ?>; // if not defined because the user has not selected an option, default to follower count
            }
            // xvalues
            // Weekly data
            let xValues = [];
            if (optionselected === "week") {
                let Weekdays = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
                for (let i = 0; i < Weekdays.length; i++) {
                    xValues[i] = Weekdays[i];
                }
            } else if (optionselected === "month") {
                let monthdays;
                const month = new Date().getMonth() + 1; // getMonth() returns 0-11, so add 1 to get from 1-12
                const year = new Date().getFullYear(); // getFullYear() returns the current year
                // check if the year is a leap year by checking if it is divisible by 4 and not divisible by 100, or if it is divisible by 400
                const isLeapYear = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
                if (month === 2) {
                    monthdays = isLeapYear ? 29 : 28; // If it is a leap year, February has 29 days, otherwise it has 28 days
                } else if ([4, 6, 9, 11].includes(month)) {
                    monthdays = 30;
                } else {
                    monthdays = 31;
                }

                let Monthdays = [];
                for (let i = 1; i <= monthdays; i++) {
                    Monthdays.push(i);
                }
                xValues = Monthdays;
            } else if (optionselected == "Q1") {
                let Quarter = ["Jan", "Feb", "Mar"];
                for (let i = 0; i < Quarter.length; i++) {
                    xValues[i] = Quarter[i];
                }
            } else if (optionselected == "half") {
                let Half = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
                for (let i = 0; i < Half.length; i++) {
                    xValues[i] = Half[i];
                }
            } else if (optionselected == "Q3") {
                let Quarter = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep"];
                for (let i = 0; i < Quarter.length; i++) {
                    xValues[i] = Quarter[i];
                }
            } else if (optionselected === "year") {
                // Yearly data
                const year = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                for (let i = 0; i < year.length; i++) {
                    xValues[i] = year[i % 12];
                }
            } else if (optionselected == "all") {
                let All = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                for (let i = 0; i < All.length; i++) {
                    xValues[i] = All[i];
                }
            }
            if (selectchartoption != null) {
                createchart(xValues, yValues, selectchartoption);
            }
        }
        function createchart(xValues, yValues, diagram) {
            // disable the previous chart
            if (diagram != null) {
                if (document.getElementById("chart") != null) document.getElementById("chart").remove();
                let monitors = document.getElementsByClassName("chartjs-size-monitor");
                while (monitors.length > 0) {
                    monitors[0].parentNode.removeChild(monitors[0]);
                }
                let canvas = document.createElement("canvas");
                canvas.id = "chart";
                document.getElementsByClassName("normalcontentnavbar")[0].appendChild(canvas);
            }
            console.log(yValues);
            new Chart("chart", {
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
                        yAxes: [{
                            ticks: {
                                min: 0,
                                max: (yValues != null && Math.max(...yValues) > 0)
                                    ? Math.ceil(Math.max(...yValues) / 100) * 100
                                    : 1
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                const currentIndex = tooltipItem.index; // Current data point index
                                const currentValue = yValues[currentIndex]; // Current value
                                const previousValue = currentIndex > 0 ? yValues[currentIndex - 1] : null; // Previous value
                                let comparison = "";

                                if (previousValue !== null) {
                                    if (currentValue > previousValue) {
                                        comparison = `(+${currentValue - previousValue})`; // Current is greater
                                    } else if (currentValue < previousValue) {
                                        comparison = `(${currentValue - previousValue})`; // Current is less
                                    } else {
                                        comparison = `(same)`; // No change
                                    }
                                }

                                return `${currentValue} ${comparison}`; // Display value with comparison
                            }
                        }
                    }
                }
            });

        }
        changechart();
    </script>


</body>

</html>