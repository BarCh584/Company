<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/default.css">
</head>

<body>
    <div class="container">
    <nav class="outnavbar">
        <ul style="list-style-type: none; padding: 0;">
            <li><a href="startpage.php"><img src="../Images/Navbar/home.png" alt="Logo" style="width: 5vw; height: 5vh;"></a></li>
            <li><a href="startpage.php"><img src="../Images/Navbar/search.png" alt="Logo" style="width: 5vw; height: 5vh;"></a></li>
            <li><a href="add.php"><img src="../Images/Navbar/add.png" alt="Logo" style="width: 5vw; height: 5vh;"></a></li>
            <li><a href="settings.php"><img src="../Images/Navbar/Settings.png" alt="Logo" style="width: 5vw; height: 5vh;"></a></li>
            <li><a href="message.php"><img src="../Images/Navbar/message.png" alt="Logo" style="width:5vw; height: 5vh" ></a></li>
            <li><a href="account.php"><img src="../Images/Navbar/user.png" alt="Logo" style="width: 5vw; height: 5vh" ></a></li>
        </ul>
    </nav>
        <div class="form">
            <form>
                <label>Type of post</label><br>
                <input type="radio" name="checkbox"><label>Post</label><br>
                <input type="radio" name="checkbox"><label>Video</label><br>
                <input type="radio" name="checkbox"><label>Image</label><br><br>
                <label>Upload your file</label>
                <input type="file" name="file" id="file"><br><br>
                <label>Add a description (Optional)</label><br><br>
                <textarea placeholder="Description" rows="10" maxlength="" style="width: 25%;"></textarea><br>
                <input type="submit" class="submitbutton" action="">
            </form>
        </div>
    </div>
</body>

</html>