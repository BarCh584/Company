<?php

    function convertCurrency($amount, $origincurrency, $targetcurrency){
        $exchangerateapiurl = "https://open.er-api.com/v6/latest/";
        $apikey = "7fef6d78105346ffecb0af5e";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $exchangerateapiurl ."?apikey=". $apikey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $chresponse = curl_exec($ch);
        curl_close($ch);
        if($chresponse === false){
            die("Failed to convert currency, curl error-code: " . curl_error($ch));
        }

        $data = json_decode($chresponse, true);
        if(isset($data['rates'][$targetcurrency])){
            $rate = $data['rates'][$targetcurrency];
            $convertedamount = $amount * $rate;
            return $convertedamount;
        } else {
            die("Failed to convert currency, invalid currency code");
        }
    }
?>