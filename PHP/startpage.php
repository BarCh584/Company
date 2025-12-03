<?php
include_once '../Libraries/navbar.php';
include '../Libraries/subscription_plan.php';
include '../Libraries/currency_converter.php';
createnavbar("startpage");
// Database connection details
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "Company";

// Create a new database connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check if the connection failed
if ($conn->connect_error) {
    die("Connection failed: {$conn->connect_error}");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <script src="../Libraries/jquery/jquery-3.6.0.min.js"></script>
    <link rel="icon" href="../Logo.png">
</head>

<body>
</body>


</html>