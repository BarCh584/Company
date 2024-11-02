<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css">
</head>

<body>
    <?php
    function creditcardform()
    {
        ?>
        <form class='paymentform' method="POST">
            <label>Provide your personal details</label><br>


            <input type='text' class='twotextinpfld' placeholder='1234 5678 9012 3456' maxlength='19'
                oninput='formatCreditCard(this)' required>
            <br>
            <input type='text' class='twotextinpfld' placeholder='Card name holder' required>
            <br>
            <input type='text' class='twotextinpfld' placeholder='CVC' required>
            <br>
            <input type='text' class='twotextinpfld' placeholder='Expiration date: YYYY/MM' pattern='\d{4,4} \d{2,2}'
                required>
                <br>
            <input type='submit' class='submitbutton'>
        </form>
        <?php
    }
    ?>
    <?php
    function paypalform()
    {
        ?>
        <form class='paymentform' method="POST">
            <label>Provide your personal details</label><br>
            <input type='text' class='twotextinpfld' placeholder='example@email.com' required><br>
            <input type='password' class='twotextinpfld' placeholder='Password' required><br>
            <input type='submit' class='submitbutton'>
            <?php
    }
    ?>
        <script>
            function formatCreditCard(input) {
                // Remove all non-digit characters
                let value = input.value.replace(/\D/g, '');

                // Insert a space after every 4 digits
                value = value.replace(/(.{4})/g, '$1 ');

                // Remove any trailing space
                value = value.trim();

                // Set the formatted value back to the input
                input.value = value;
            }
        </script>
</body>

</html>