<?php
require 'vendor/autoload.php';
use Money\Currency;
use Money\Money;
use GuzzleHttp\Client;

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
    $creatorcurrency = $creatorpriceforcontentcurrency;
    $creatorintcurrency = $creatorpriceforcontentint;
    $creatorstmt->close();
    // $moneyorigincurrency = new Money($creatorintcurrency, new Currency($creatorcurrency));

    $userstmt = $conn->prepare("SELECT priceforcontentcurrency FROM users WHERE username = ?");
    $userstmt->bind_param("s", $username);
    $userstmt->execute();
    $userstmt->bind_result($userpriceforcontentcurrency);
    $userstmt->fetch();
    $moneydestinationcurrency = $userpriceforcontentcurrency;
    $userstmt->close();
    // Use of fixer.io API
    $apikey = "d3219689da2f01c4b316b9e326de14e1";
    $fixerio = "http://data.fixer.io/api/latest?access_key={$apikey}&base={$creatorpriceforcontentcurrency}&symbols={$userpriceforcontentcurrency}";

    $client = new Client();
    $response = $client->request('GET', $fixerio, [
        'headers' => [
            'apikey' => $apikey,
        ]
    ]);
    
    $data = json_decode($response->getBody(), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("<p>Failed to parse JSON response</p>");
    }
    if (isset($data['rates'][$moneydestinationcurrency])) {
        return round($data['rates'][$moneydestinationcurrency]*10, 2) . $moneydestinationcurrency; // return the amount of money and the currency
    } else {
        error_log("API response: " . print_r($data, true));
        die("<p>Failed to get currency conversion rate</p>");
    }
}
?>