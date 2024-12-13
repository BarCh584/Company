<?php
require 'vendor/autoload.php';
use CurrencyApi\CurrencyApi\CurrencyApiClient;
use PayPal\Api\Amount;

function userlocationcurrency()
{
    $ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);

    switch ($ip) {
        case "::1": // "::1" is the localhost IP address
            $ip = "localhost";
        default:
            $ip = filter_var($ip, FILTER_VALIDATE_IP);
    }
    $detailrequest = json_decode(file_get_contents("http://ip-api.com/{$ip}/json/"), true);
    if ($detailrequest === null) {
        die("<p>Invalid ip request</p>");
    } else if ($detailrequest['status'] == 'fail') {
        die("<p>Failed to get user location</p>");
    } else {
        $userlocation = $detailrequest['countryCode'];
        return $userlocation;
    }
}



function getexchangerate($creator, $username)
{
    // Get all necessary data
    $servername = "localhost";
    $dbusername = "root";
    $password = "";
    $dbname = "Company";
    $conn = new mysqli($servername, $dbusername, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: {$conn->connect_error}");
    }
    $creatorstmt = $conn->prepare("SELECT priceforcontentint, priceforcontentcurrency FROM users WHERE username = ?");
    $creatorstmt->bind_param("s", $creator);
    $creatorstmt->execute();
    $creatorstmt->bind_result($creatorpriceforcontentint, $creatorpriceforcontentcurrency);
    $creatorstmt->fetch();
    $origincurrency = $creatorpriceforcontentcurrency;
    $originmoney = $creatorpriceforcontentint;
    $creatorstmt->close();

    $userstmt = $conn->prepare("SELECT priceforcontentcurrency FROM users WHERE username = ?");
    $userstmt->bind_param("s", $username);
    $userstmt->execute();
    $userstmt->bind_result($userpriceforcontentcurrency);
    $userstmt->fetch();
    $destinationcurrency = $userpriceforcontentcurrency;
    $userstmt->close();

    //Get currency symbol from symbols.json
    $symbolsjsonfilepath = __DIR__ . "/symbols.json";
    $jsondata = json_decode(file_get_contents($symbolsjsonfilepath), true);
    if($jsondata === null)
    {
        die("Failed to get currency symbols");
    }

    // Use exchange rate API to get exchange rate
    $apikey = "7fef6d78105346ffecb0af5e";
    $apiurl = "https://v6.exchangerate-api.com/v6/{$apikey}/latest/{$origincurrency}";
    $response = file_get_contents($apiurl);
    $data = json_decode($response, true);

    if($data && $data['result'] == 'success')
    {
        // Extract the exchange rate
        $exchangerate = $data['conversion_rates'][$destinationcurrency];
        return round($exchangerate*$originmoney) . (array_key_exists($destinationcurrency, $jsondata) ? $jsondata[$destinationcurrency] : $destinationcurrency);
    }
    else
    {
        die("Failed to get exchange rate");
    }
}
?>