<?php
    function sendcode($receiver) {
        $verification_code = rand(100000, 999999);
        $subject = "Your verfication code";
        $message = "You are currently trying to log-in to your account. <br><br> Your email verification code is: {$verification_code}";
        $header = "From: accessframe.gmbh@gmail.com";

        if(mail($receiver, $subject, $message, $header)) {
            echo "Verification code send to {$receiver}";
            return $verification_code;
        }
        else {
            echo "Failed to send email verification code to {$receiver}, error: " . error_get_last();
        }
    }