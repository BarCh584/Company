<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css">
    <script src="script.js"></script>
    <title>Document</title>
</head>

<body>
    <div class="index">
        <h1>
            Provide your personal details
        </h1>

        <?php
        // PHP prints out the input field and embeds JavaScript code
        echo '<form class="form"> 
        <input type="submit" class="submitbutton"> 
        <br> 
        <input type="text" class="twotextinpfld" placeholder="1234 5678 9012 3456" maxlength="19" oninput="formatCreditCard(this)" required>
        <br>
        <input type="text" class="twotextinpfld" placeholder="Card name holder" required>
        <br>
        <input type="text" class="twotextinpfld" placeholder="CVC: MM" pattern="\d{2,2}" required>
        <input type="text" class="twotextinpfld" placeholder="CVC: YYYY" pattern="\d{4,4}" required>
        </form>';

        // Embed the JavaScript code using PHP's echo
        echo '<script>
            function formatCreditCard(input) {
                // Remove all non-digit characters
                let value = input.value.replace(/\D/g, "");

                // Insert a space after every 4 digits
                value = value.replace(/(.{4})/g, "$1 ");

                // Remove any trailing space
                value = value.trim();

                // Set the formatted value back to the input
                input.value = value;
            }
        </script>';
        ?>
    </div>
</body>

</html>