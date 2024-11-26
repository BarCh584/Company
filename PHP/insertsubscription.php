<?php
define("PAYPAL_CLIENT_ID", "AX3Uu6n2ZthFq8bzmqyqK0YSiOYB9FR6igJjmEyAestmzAVw7Htar3yuD195uBDQu2psbQHvUFmwTwfq");
define("PAYPAL_SECRET", "EBya05pNrCAph5uWDD311alSsQU34_HzUn5h_9zOeUSB9Qg0TXq4Qp9zrRQLfUP4P0T4-ZUN8s4145X8");
define("PAYPAL_URL", "https://api-m.sandbox.paypal.com");

function getpaypalaccesstoken()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_URL . "v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Accept-Language: en_GB"
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("Error: " . curl_error($ch));
    }
    curl_close($ch);
    $responsedata = json_decode($response, true);
    return $responsedata['access_token'];
}

function createpaypalproduct($accesstoken)
{
    $data = [
        "name" => "Subscription",
        "description" => "AccessFrame subscription",
        "type" => "SERVICE",
        "category" => "SOFTWARE",
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_URL . "v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Accept-Language: en_GB"
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("Error: " . curl_error($ch));
    }
    curl_close($ch);
    $responsedata = json_decode($response, true);
    return $responsedata['id'];
}

function createpaypalplan($accesstoken, $productid)
{
    // Plan data for the subscription to send to the PayPal API
    $data = [
        "product_id" => $productid,
        "name" => "AccessFrame Subscription",
        "description" => "Premium AccessFrame subscription",
        "billing_cycles" => [
            [
                "frequency" => [
                    "interval_unit" => "MONTH",
                    "interval_count" => 1 // Get money every month once
                ],
                "tenure_type" => "REGULAR",
                "sequence" => 1,
                "total_cycles" => 12, // 1 year subscription
                "pricing_scheme" => [ // pricing scheme for the subscription
                    "fixed_price" => [
                        "value" => 10,
                        "currency_code" => "USD"
                    ]
                ]
            ]
        ],
        "payment_preferences" => [
            "auto_bill_outstanding" => true,
            "setup_fee" => [
                "value" => 0, // No fee for setting up the subscription
                "currency_code" => "USD"
            ],
            "setup_fee_failure_action" => "CONTINUE", // continue if setup fee fails
            "payment_failure_threshold" => 3 // 3 attempts to pay
        ]
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_URL . "v1/billing/plans");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $accesstoken
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("Error: " . curl_error($ch));
    }
    curl_close($ch);
    $responsedata = json_decode($response, true);
    return $responsedata['id'];
}

$accesstoken = getpaypalaccesstoken();
$productid = createpaypalproduct($accesstoken);
$planid=  createpaypalplan($accesstoken, $productid);
echo json_encode(["planId" => $planid]);
?>