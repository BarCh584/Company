<?php
function createSubscriptionplan($currency, $amount) {
    require('vendor/autoload.php');
    
    use Paypal\Api\Plan;
    use Paypal\Api\PaymentDefinition;
    use Paypal\Api\MerchantPreferences;
    use Paypal\Api\OAuthTokenCredential;
    use Paypal\Rest\ApiContext;

    $client_id="AX3Uu6n2ZthFq8bzmqyqK0YSiOYB9FR6igJjmEyAestmzAVw7Htar3yuD195uBDQu2psbQHvUFmwTwfq";
    $client_secret="EBya05pNrCAph5uWDD311alSsQU34_HzUn5h_9zOeUSB9Qg0TXq4Qp9zrRQLfUP4P0T4-ZUN8s4145X8";

    $apicontext = new ApiContext(
        new OAuthTokenCredential(
            $client_id,
            $client_secret
        )
    );
    $apicontext->setconfig([
        'mode' => 'sandbox' // Change to 'live' for production when ready
    ]);

    $plan = new Plan();
    $plan->setName('Accessframe subscription')
        ->setDescription('Monthly Accessframe subscription plan')
        ->setType('fixed');

    $paymentdefinition = new PaymentDefinition();
    $paymentdefinition->setName('Regular Payments')
        ->setType('REGULAR')
        ->setFrequency('Month')
        ->setFrequencyInterval('1')
        ->setCycles('12')
        ->setAmount([
            'value' => $amount,
            'currency' => $currency
        ]);
    $merchantpreferences = new MerchantPreferences();
    $merchantpreferences->setReturnUrl('http://localhost/return.php?success=true') // Change to your return URL
        ->setCancelUrl('http://localhost/return.php?success=false')
        ->setAutoBillAmount('yes')
        ->setInitialFailAmountAction('CONTINUE')
        ->setMaxFailAttempts('1');
    }

    // Assign the payment definition and merchant preferences to the plan
    $plan->setPaymentDefinitions([$paymentdefinition]);
    $plan->setMerchantPreferences($merchantpreferences);
    try {
        $createdplan = $plan->create($apicontext);
        $patch->setOp('replace')
            ->setPath('/')
            ->setValue([
                'state' => 'ACTIVE'
            ]);
        $patchrequest = new Paypal\Api\PatchRequest();
        $patchrequest->addPatch($patch);
        $createdplan->update($patchrequest, $apicontext);
        $activeplan = Plan::get($createdplan->getId(), $apicontext);
        echo "Plan activated: " . $activeplan->getState();
    } catch (Exception $e) {
        echo "Failed to create plan: " . $e->getMessage();
    }
?>