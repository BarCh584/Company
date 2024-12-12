<?php
require 'vendor/autoload.php';
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\ExchangerExchange;
use Money\Money;
use Money\Converter;
use Money\Exchange;
use Money\Exchange\SwapExchange;
use Money\Currencies\ExchangerCurrencies;

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
    } else if($detailrequest['status'] == 'fail') {
        die("<p>Failed to get user location</p>");
    }else {
        $userlocation = $detailrequest['countryCode'];
        return $userlocation;
    }
}



function convertCurrency($creator, $username)
{
    // Get all necessary data
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Company";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $creatorstmt = $conn->prepare("SELECT priceforcontentint, priceforcontentcurrency FROM users WHERE username = ?");
    $creatorstmt->bind_param("s", $creator);
    $creatorstmt->execute();
    $creatorstmt->bind_result($creatorpriceforcontentint, $creatorpriceforcontentcurrency);
    $creatorstmt->fetch();
    $moneyorigincurrency = new Money($creatorpriceforcontentint, new Currency($creatorpriceforcontentcurrency));
    
    $userstmt = $conn->prepare("SELECT priceforcontentcurrency FROM users WHERE username = ?");
    $userstmt->bind_param("s", $username);
    $userstmt->execute();
    $userstmt->bind_result( $userpriceforcontentcurrency);
    $userstmt->fetch();

    // Use of fixer.io API
    $apikey = "d3219689da2f01c4b316b9e326de14e1";
    $fixerio = "https://data.fixer.io/api/latest?access_key={$apikey}&base={$creatorpriceforcontentcurrency}&symbols={$userpriceforcontentcurrency}";

    $client = new GuzzleHttp\Client();
}
?>