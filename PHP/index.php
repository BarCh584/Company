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
    <div id="grid">
        <h1 class="title">
            Login into your account
        </h1>
        <form method="POST" action="startpage.php">
            <input type="text" class="textinpfld" name="uname" placeholder="example@email.com" required><br>
            <input type="password" class="textinpfld" name="pswrd" minlength="8" placeholder="Password" required><br>
            <label for="staysignin">Stay sign-in</label>
            <input type="checkbox" value="staysignin">
            <input type="submit" class="submitbutton">
        </form>
        <br>
        <a href="signup.php">
            Sign up
        </a>
        <br>
        <br>
        <h2>Or sign in with: </h2>
        <button class="googlesignin">Sign in with Google</button>
        <br>
        <button class="facebookesignin">Sign in with Facebook</button>
    </div>
</body>

</html>