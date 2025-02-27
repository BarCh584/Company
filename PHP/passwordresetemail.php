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
    <form class='form' method='POST'>
        <div class='content'>
            <h3>Please indicate your email address for password reset</h3>
            <input type='text' name='email' class='twotextinpfld' placeholder='example@gmail.com'><br>
            <input type='submit' class='submitbutton' value='Submit'>
        </div>
    </form>
    <?php
    global $verification_code;
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
        // Check for email in database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Company";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: {$conn->connect_error}");
        }

        include_once '../Libraries/randomcode.php';
        $emailstmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $emailstmt->bind_param("s", $_POST['email']);
        $emailstmt->execute();
        $emailstmtresult = $emailstmt->get_result();
        if ($emailstmtresult->num_rows > 0) {
            echo "<form method='POST'>
                    <input type='text' name='code' class='twotextinpfld'><br>
                    <input type='submit' class='submitbutton' value='Submit'>
                    </form>";
            $verification_code = sendcode($_POST['email']);
            if ($verification_code != null)
                print ($verification_code);
            if (isset($_POST["code"])) {
                $typed_code = $_POST['code'];
                if ($typed_code != $verification_code) {
                    print ("Invalid code. Please type in the correct one.");
                } else {
                    header('passwordreset.php');
                }
            }
        } else {
            echo "Email not found in database.";
        }

    }
    ?>
</body>

</html>