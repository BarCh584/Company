<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WYSIWYG Editor</title>
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../Logo2.png">
</head>

<body>
    <?php
    include_once('../Libraries/navbar.php');
    createnavbar("add"); ?>
    <div class="normalcontentnavbar">
        <form method="POST" enctype="multipart/form-data" class="content" onsubmit="prepareSubmission()">
            <label>Title</label><br>
            <div id="titleEditor" class="textinpfld" contenteditable="true" spellcheck="true"></div>
            <input type="hidden" name="title" id="titleInput"><br>

            <label>Add a comment (optional)</label><br>
            <div id="commentEditor" class="textinpfld" contenteditable="true" spellcheck="true"></div>
            <input type="hidden" name="comment" id="commentInput"><br>

            <label>Upload your file (optional)</label><br>
            <input type="file" name="file" id="file"><br><br>

            <label>Post visibility</label><br>
            <div class="visibility">
                <div>
                    <input id="subvisibility" type="radio" name="visibility" value="subscriber">
                    <label for="subvisibility">Subscribers</label>
                </div>
                <div style="margin-left: 2.5vw;">
                    <input id="linkvisibilitysub" type="checkbox" name="linkvisibility" value="link">
                    <label for="linkvisibilitysub" id="linkvisibilitysublabel">And people with link</label>
                </div>
                <div>
                    <input id="everyvisibility" type="radio" name="visibility" value="everyone">
                    <label for="everyvisibility">Everyone</label>
                </div>
                <div>
                    <input id="privvisibility" type="radio" name="visibility" value="private">
                    <label for="privvisibility">Private</label>
                </div>
                <div style="margin-left: 2.5vw;">
                    <input id="linkvisibilitypri" type="checkbox" name="linkvisibility" value="link">
                    <label for="linkvisibilitypri" id="linkvisibilityprilabel">Except people with link</label>
                </div>
                <input type="submit" name="submitbutton" class="submitbutton">
            </div>
        </form>

        <div class="toolbar">
            <button onclick="formatText('bold')"><b>B</b></button>
            <button onclick="formatText('italic')"><i>I</i></button>
            <button onclick="formatText('underline')"><u>U</u></button>
            <button onclick="addLink()">🔗 Link</button>
            <button onclick="formatText('subscript')"><sub>Sub</sub></button>
            <button onclick="formatText('superscript')"><sup>Sup</sup></button>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>

        function updateVisibility() {
            var subvisibility = document.getElementById("subvisibility");
            var privvisibility = document.getElementById("privvisibility");
            var everyvisibility = document.getElementById("everyvisibility"); // Added reference
            var linkvisibilitysub = document.getElementById("linkvisibilitysub");
            var linkvisibilitysublabel = document.getElementById("linkvisibilitysublabel");
            var linkvisibilitypri = document.getElementById("linkvisibilitypri");
            var linkvisibilityprilabel = document.getElementById("linkvisibilityprilabel");

            if (subvisibility.checked) {
                linkvisibilitysub.style.display = "inline";
                linkvisibilitysublabel.style.display = "inline";
            } else {
                linkvisibilitysub.style.display = "none";
                linkvisibilitysublabel.style.display = "none";
            }

            if (privvisibility.checked) {
                linkvisibilitypri.style.display = "inline";
                linkvisibilityprilabel.style.display = "inline";
            } else {
                linkvisibilitypri.style.display = "none";
                linkvisibilityprilabel.style.display = "none";
            }

            // Hide checkboxes if "Everyone" is selected
            if (everyvisibility.checked) {
                linkvisibilitysub.style.display = "none";
                linkvisibilitysublabel.style.display = "none";
                linkvisibilitypri.style.display = "none";
                linkvisibilityprilabel.style.display = "none";
            }
        }

        // Set the initial state on page load
        document.addEventListener("DOMContentLoaded", updateVisibility);

        // Add event listeners to update visibility when a radio button is clicked
        document.getElementById("subvisibility").addEventListener("change", updateVisibility);
        document.getElementById("privvisibility").addEventListener("change", updateVisibility);
        document.getElementById("everyvisibility").addEventListener("change", updateVisibility); // Added listener


        // WYSIWYG Editor

        let activeEditor = null;

        document.querySelectorAll(".textinpfld").forEach(textinpfld => {
            textinpfld.addEventListener("focus", () => {
                activeEditor = textinpfld;
            });
            textinpfld.addEventListener("input", updateToolbar);
        });
        document.addEventListener("selectionchange", () => {
            if (activeEditor && activeEditor.contains(window.getSelection().anchorNode)) {
                updateToolbar();
            }
        });
        function formatText(command) {
            if (activeEditor) {
                document.execCommand(command, false, null);
                updateToolbar();
            }
        }

        function addLink() {
            if (activeEditor) {
                let selection = window.getSelection().toString().trim();

                if (!selection) {
                    alert("Please select a valid link.");
                    return;
                }

                // Basic URL validation before sending a request
                let urlPattern = /^(https?:\/\/[^\s/$.?#].[^\s]*)$/i;
                if (!urlPattern.test(selection)) {
                    alert("Selected text is not a valid URL.");
                    return;
                }

                // Check if the URL exists
                let xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function () {
                    if (this.readyState == 4) {
                        if (this.status == 200) { // If the URL is valid, convert it into a hyperlink
                            document.execCommand("createLink", false, selection);
                            updateToolbar();
                        } else if (this.status == 404) {
                            alert("The selected URL does not exist.");
                        }
                    }
                };

                xmlhttp.open("HEAD", selection, true); // Use HEAD request to check if the URL exists
                xmlhttp.send();
            } else {
                alert("No active text editor selected.");
            }
        }

        function updateToolbar() {
            document.querySelectorAll(".toolbar button").forEach(button => {
                button.style.backgroundColor = "";
            });

            if (document.queryCommandState("bold")) {
                document.querySelector("button[onclick=\"formatText('bold')\"]").style.backgroundColor = "grey";
            }
            if (document.queryCommandState("italic")) {
                document.querySelector("button[onclick=\"formatText('italic')\"]").style.backgroundColor = "grey";
            }
            if (document.queryCommandState("underline")) {
                document.querySelector("button[onclick=\"formatText('underline')\"]").style.backgroundColor = "grey";
            }
            if (document.queryCommandState("subscript")) {
                document.querySelector("button[onclick=\"formatText('subscript')\"]").style.backgroundColor = "grey";
            }
            if (document.queryCommandState("superscript")) {
                document.querySelector("button[onclick=\"formatText('superscript')\"]").style.backgroundColor = "grey";
            }
        }

        function prepareSubmission() {
            document.getElementById("titleInput").value = document.getElementById("titleEditor").innerHTML;
            document.getElementById("commentInput").value = document.getElementById("commentEditor").innerHTML;
        }
    </script>

</body>
<?php
$conn = new mysqli("localhost", "root", "", "Company");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitbutton"], $_POST["visibility"])) {
    if ($_POST["visibility"] == "private" && isset($_POST["linkvisibility"])) {
        $_POST["visibility"] = "private & link";
    } else if ($_POST["visibility"] == "subscriber" && isset($_POST["linkvisibility"])) {
        $_POST["visibility"] = "subscribers & link";
    }
    $poststmt = $conn->prepare("INSERT INTO posts (title, comment, accountname, accountid, file, visibility) VALUES (?, ?, ?, ?, ?, ?)");
    $poststmt->bind_param("ssssss", $_POST["title"], $_POST["comment"], $_SESSION["username"], $_SESSION["id"], $_FILES["file"]["name"], $_POST["visibility"]);
    $poststmt->execute();
}
?>

</html>