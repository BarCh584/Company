<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emoji Picker</title>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1"></script>
    <style>
        emoji-picker {
            position: absolute;
            bottom: 50px;
            display: none;
        }
    </style>
</head>
<body>
    <input type="text" id="textInput" placeholder="Type here...">
    <button id="emojiButton">😀</button>
    <emoji-picker></emoji-picker>
    <script type="module">
    import 'https://unpkg.com/emoji-picker-element';
    
    document.addEventListener("DOMContentLoaded", function () {
        const emojiPicker = document.querySelector("emoji-picker");
        const textInput = document.getElementById("textInput");
        const emojiButton = document.getElementById("emojiButton");

        emojiButton.addEventListener("click", () => {
            emojiPicker.style.display = emojiPicker.style.display === "none" ? "block" : "none";
        });

        emojiPicker.addEventListener("emoji-click", (event) => {
            textInput.value += event.detail.unicode;
        });

        document.addEventListener("click", (event) => {
            if (!emojiButton.contains(event.target) && !emojiPicker.contains(event.target)) {
                emojiPicker.style.display = "none";
            }
        });
    });
</script>

    

</body>
</html>
