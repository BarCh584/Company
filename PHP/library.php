<?php
    function sendcode() {
        $receiver = $_POST('email');
        $verification_code = rand(100000, 999999);
        $subject = "Your verfication code";
        $message = "You are currently trying to log-in to your account <br><br> your email verification code is: " . $verification_code;
        $header = "From: exampleemail@example.com";

        if(mail($receiver, $subject, $message, $header)) {
            echo "Verification code send to " . $receiver;
        }
        else {
            echo "Failed to send email verification code to " . $receiver;
        }
        print($receiver);
    }