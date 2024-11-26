<?php
function lang()
{
    // Save language preference to database if selected
    $dbname = "localhost";
    $dbuname = "root";
    $dbpwrd = "";
    $db = "Company";
    $conn = new mysqli($dbname, $dbuname, $dbpwrd, $db);
    if ($conn->connect_error) {
        die("Connection failed: {$conn->connect_error}");
    }
    $langstmt = $conn->prepare("SELECT language FROM users WHERE username = ?");
    $langstmt->bind_param("s", $_SESSION['username']);
    $langstmt->execute();
    $langstmtresult = $langstmt->get_result();
    if ($langstmtresult->num_rows > 0) {
        while ($row = $langstmtresult->fetch_assoc()) {
            return $row['language'];
        }
    } else {
        return substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2); // Return the browser language if no language preference is found
    }
}

function t($text)
{
    $lang = lang();
    if($lang == null) {
        $lang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2); // Return the browser language if no language preference is found
    }
    $langfilepath = "../languages/" . $lang . ".json";
    if (!file_exists($langfilepath)) {
        print $text; // Return the original text if the language file does not exist
    }
    $langfile = file_get_contents($langfilepath);
    $langjson = json_decode($langfile, true);
    if (isset($langjson[$text])) {
        print $langjson[$text];

    } else {
        print $text; // Return the original text if the translation is not found
    }

}
