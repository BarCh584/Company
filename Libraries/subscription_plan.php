<?php

use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\MerchantPreferences;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
function createSubscriptionplan($currency, $amount)
{
    $client_id = 'AX3Uu6n2ZthFq8bzmqyqK0YSiOYB9FR6igJjmEyAestmzAVw7Htar3yuD195uBDQu2psbQHvUFmwTwfq';
    $secret_id = 'EBya05pNrCAph5uWDD311alSsQU34_HzUn5h_9zOeUSB9Qg0TXq4Qp9zrRQLfUP4P0T4-ZUN8s4145X8';
    $apicontext = new ApiContext(new OAuthTokenCredential($client_id, $secret_id));
    $apicontext->setConfig(['mode' => 'sandbox']);

    // create actual plan
    $plan = new Plan();
    $plan->setName("Accessframe subscription")
        ->setDescription("Monthly subscription for Accessframe")
        ->setType('infinite');
    $paymentdefinition = new PaymentDefinition();
    $paymentdefinition->setName('Regular Payments')
        ->setType('REGULAR')
        ->setFrequency('Month')
        ->setFrequencyInterval('1')
        ->setCycles('0')
        ->setAmount(new PayPal\Api\Currency(['value' => $amount, 'currency' => $currency]));

    $merchantpreferences = new MerchantPreferences();
    $merchantpreferences->setReturnUrl('http://localhost/Accessframe/subscription_success.php?success=true') // change this to your success url
        ->setCancelUrl('http://localhost/Accessframe/subscription_success.php?success=false')
        ->setAutoBillAmount('yes')
        ->setInitialFailAmountAction('CONTINUE')
        ->setMaxFailAttempts('1');

    $plan->setPaymentDefinitions([$paymentdefinition]);
    $plan->setMerchantPreferences($merchantpreferences);
    try {
        $createdplan = $plan->create($apicontext);
        $patch = new PayPal\Api\Patch();
        $patch->setOp('replace')
            ->setPath('/')
            ->setValue([
                'state' => 'ACTIVE'
            ]);
        $patchrequest = new Paypal\Api\PatchRequest();
        $patchrequest->addPatch($patch);
        $createdplan->update($patchrequest, $apicontext);
        $activeplan = Plan::get($createdplan->getId(), $apicontext);
        echo "Plan created and activated" . $activeplan->getId();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
