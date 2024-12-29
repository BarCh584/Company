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

    // get the right time for the chart from the options
    
    // for followerchart
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["charttimeoptions"])) {
            $selecttime = $_POST["charttimeoptions"];
            $dateused = "";
            switch ($selecttime) {
                case "week":
                    $dateused = "DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
                    break;
                case "month":
                    $dateused = "DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                    break;
                case "Q1":
                    $dateused = "DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                    break;
                case "half":
                    $dateused = "DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                    break;
                case "Q3":
                    $dateused = "DATE_SUB(CURDATE(), INTERVAL 9 MONTH)";
                    break;
                case "year":
                    $dateused = "DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
                case "all":
                    $dateused = "DATE_SUB(CURDATE(), INTERVAL 100 YEAR)";
                    break;
            }
            $followerchartarray = [];
            $followerstmt = $conn->prepare("SELECT followers FROM dailyfollowerchart WHERE username = ? AND createdat >= ?");
            $dateused_result = $conn->query("SELECT $dateused as filtered_date")->fetch_assoc()["filtered_date"]; // Get the actual date
            $followerstmt->bind_param("ss", $_SESSION['username'], $dateused_result); // Use the computed date
            $followerstmt->execute();
            $followerstmt->bind_result($followers);
            while ($followerstmt->fetch()) {
                array_push($followerchartarray, $followers);
            }
            $followerstmt->close();
            // for likes and dislikes
            $likesanddislikeschartarraylikes = array();
            $likesanddislikeschartarraydislikes = array();
            $likesanddislikesstmt = $conn->prepare("SELECT likes, dislikes FROM dailylikesdislikeschart WHERE username = ? AND createdat >= ?");
            $likesanddislikesstmt->bind_param("ss", $_SESSION['username'], $dateused_result);
            $likesanddislikesstmt->execute();
            $likesanddislikesstmt->bind_result($likes, $dislikes);
            while ($likesanddislikesstmt->fetch()) {
                array_push($likesanddislikeschartarraylikes, $likes);
                array_push($likesanddislikeschartarraydislikes, $dislikes);
            }
        }
    }
    ?>

    <div class="normalcontentnavbar">
        <h1>Analytics</h1>
        <form method="POST" id="chartForm">
            <select id="charttimeoptions" name="charttimeoptions" onchange="submitForm();">
                <option value="week" <?php if (isset($selecttime) && $selecttime === "week")
                    echo "selected"; ?>>Weekly
                </option>
                <option value="month" <?php if (isset($selecttime) && $selecttime === "month")
                    echo "selected"; ?>>Monthly
                </option>
                <option value="Q1" <?php if (isset($selecttime) && $selecttime === "Q1")
                    echo "selected"; ?>>3 Months
                </option>
                <option value="half" <?php if (isset($selecttime) && $selecttime === "half")
                    echo "selected"; ?>>6 Months
                </option>
                <option value="Q3" <?php if (isset($selecttime) && $selecttime === "Q3")
                    echo "selected"; ?>>9 Months
                </option>
                <option value="year" <?php if (isset($selecttime) && $selecttime === "year")
                    echo "selected"; ?>>Yearly
                </option>
                <option value="all" <?php if (isset($selecttime) && $selecttime === "all")
                    echo "selected"; ?>>All time
                </option>
            </select>

            <select id="chartoptions" name="chartoptions" onchange="submitForm();">
                <option value="follower">Follower count</option>
                <option value="likes">Likes count</option>
                <option value="dislikes">Dislikes count</option>
            </select>
        </form>
        <script>
            function submitForm() {
                document.getElementById('chartForm').submit();
                changechart();
            }
        </script>
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