<?php
function loadtranslation($lang)
{
    $filepath = "../languages/" . $lang . ".json";
    if(!file_exists($filepath))
    {
        $filepath = "../languages/en.json"; // Default to English if the language file is not found
        echo "Language file not found";
        return;
    }
    $tranlation = json_decode(file_get_contents($filepath), true);
    if(json_last_error() != JSON_ERROR_NONE)
    {
        throw new Exception("Error loading translation file");
    }
    return $tranlation;
}

function t($key, $translation, $fallbacktranslation = null) {
    if(isset($translation[$key]))
    {
        return $translation[$key];
    }
    if($fallbacktranslation != null && isset($fallbacktranslation[$key]))
    {
        return $fallbacktranslation[$key];
    }
    return $key;
}

function detectlanguage() {
    session_start();
    if(isset($_GET["lang"])) return $_GET["lang"];
    if(isset($_SESSION["lang"])) return $_SESSION["lang"];
    if(isset($_COOKIE["lang"]))  return $_COOKIE["lang"];
    
    if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
    {
        $lang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
        $supportedelanguages = ["en", "fr", "de", "es", "it"];
        return in_array($lang, $supportedelanguages) ? $lang : "en";
    }
    return "en";
}
$lang = detectlanguage();
session_start();
$_SESSION["lang"] = $lang;
setcookie("lang", $lang, time() + (86400 * 30), "/"); // store for 1 month
$translation = loadtranslation($lang);
$fallbacktranslation = loadtranslation("en");
?>