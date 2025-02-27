<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../Logo2.png">
</head>

<body>
    <form class='form' method='POST'>
        <div class='content'>
            <h3>Reset Password</h3>
            <input type='password' name='password' class='twotextinpfld' placeholder='Reset Password' minlength='8'><br>
            <input type='password' name='confirmpassword' class='twotextinpfld' placeholder='Confirm Password' minlength='8'><br>
            <input type='submit' class='submitbutton' value='Save'>
        </div>
    </form>
</body>

</html>