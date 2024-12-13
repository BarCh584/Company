<?php
require 'vendor/autoload.php';
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\FixedExchange;
use Money\Converter;
use Money\Parser\DecimalMoneyParser;

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
    // $moneyorigincurrency = new Money($creatorintcurrency, new Currency($creatorcurrency));

    $userstmt = $conn->prepare("SELECT priceforcontentcurrency FROM users WHERE username = ?");
    $userstmt->bind_param("s", $username);
    $userstmt->execute();
    $userstmt->bind_result($userpriceforcontentcurrency);
    $userstmt->fetch();
    $destinationcurrency = $userpriceforcontentcurrency;
    $userstmt->close();
    // Use moneyphp to convert the currency
    $url = "https://api.exchangerate-api.com/v4/latest/{$origincurrency}";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    if(!isset($data['rates'][$destinationcurrency])){
        throw new Exception("Invalid currency conversion");
    }
    $exchangerate = $data['rates'][$destinationcurrency];
    $currency = new ISOCurrencies();
    $exchange = new FixedExchange([
        "{$origincurrency}/{$destinationcurrency}" => $exchangerate
    ]);
    $moneyparser = new DecimalMoneyParser($currency);
    $money = $moneyparser->parse($originmoney, new Currency($origincurrency));
    $converter = new Converter($currency, $exchange);
    $convertedmoney = $converter->convert($money, new Currency($destinationcurrency));

    return $convertedmoney;
}
?>