<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/default.css?v=<?php echo time(); ?>">
    <title>Document</title>
</head>

<body>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <?php
    include_once '../Libraries/navbar.php';
    createnavbar("settings.profile");
    createsettingsnavbar('settings.paymentinformationpaypal');
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "Company";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed, error code: " . $conn->connect_error);
    }
    $mail = $_SESSION["email"] ?? null;
    $currencycode = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_SESSION["email"] != null && !empty($_POST["amount"])) {
            $stmt = $conn->prepare("UPDATE users SET priceforcontentint = ?, priceforcontentcurrency = ? WHERE email = ?");
            $stmt->bind_param("iss", $_POST["amount"], $_POST["currency"], $_SESSION["email"]);
            if ($stmt->execute()) {
                $currencycode = $_POST["currency"];
            }
            $stmt->close();
        }
    }
    $currencystmt = $conn->prepare("SELECT priceforcontentint, priceforcontentcurrency FROM users WHERE email = ?");
    $currencystmt->bind_param("s", $mail);
    if ($currencystmt->execute()) {
        $currencystmt->bind_result($priceforcontentint, $priceforcontentcurrency);
        $currencystmt->fetch();
        $preferencedcurrency = $priceforcontentcurrency;
        $price = $priceforcontentint;
    }
    ?>
    <script>
        if (window.innerWidth < 768) {
            $(".innavbar").hide();
        }
    </script>
    <h3>Change the price for your content</h3>
    <div class="normalcontentnavbar">
        <form method="POST">
            <select class="textinpfld" name="currency" value="currency">
                <script>
                    const selectedCurrency = "<?= $preferencedcurrency ?>";
                    const currency_list = [
                        { name: "Afghan Afghani", code: "AFA" },
                        { name: "Albanian Lek", code: "ALL" },
                        { name: "Algerian Dinar", code: "DZD" },
                        { name: "Angolan Kwanza", code: "AOA" },
                        { name: "Argentine Peso", code: "ARS" },
                        { name: "Armenian Dram", code: "AMD" },
                        { name: "Aruban Florin", code: "AWG" },
                        { name: "Australian Dollar", code: "AUD" },
                        { name: "Azerbaijani Manat", code: "AZN" },
                        { name: "Bahamian Dollar", code: "BSD" },
                        { name: "Bahraini Dinar", code: "BHD" },
                        { name: "Bangladeshi Taka", code: "BDT" },
                        { name: "Barbadian Dollar", code: "BBD" },
                        { name: "Belarusian Ruble", code: "BYR" },
                        { name: "Belgian Franc", code: "BEF" },
                        { name: "Belize Dollar", code: "BZD" },
                        { name: "Bermudan Dollar", code: "BMD" },
                        { name: "Bhutanese Ngultrum", code: "BTN" },
                        { name: "Bitcoin", code: "BTC" },
                        { name: "Bolivian Boliviano", code: "BOB" },
                        { name: "Bosnia-Herzegovina Convertible Mark", code: "BAM" },
                        { name: "Botswanan Pula", code: "BWP" },
                        { name: "Brazilian Real", code: "BRL" },
                        { name: "British Pound Sterling", code: "GBP" },
                        { name: "Brunei Dollar", code: "BND" },
                        { name: "Bulgarian Lev", code: "BGN" },
                        { name: "Burundian Franc", code: "BIF" },
                        { name: "Cambodian Riel", code: "KHR" },
                        { name: "Canadian Dollar", code: "CAD" },
                        { name: "Cape Verdean Escudo", code: "CVE" },
                        { name: "Cayman Islands Dollar", code: "KYD" },
                        { name: "CFA Franc BCEAO", code: "XOF" },
                        { name: "CFA Franc BEAC", code: "XAF" },
                        { name: "CFP Franc", code: "XPF" },
                        { name: "Chilean Peso", code: "CLP" },
                        { name: "Chilean Unit of Account", code: "CLF" },
                        { name: "Chinese Yuan", code: "CNY" },
                        { name: "Colombian Peso", code: "COP" },
                        { name: "Comorian Franc", code: "KMF" },
                        { name: "Congolese Franc", code: "CDF" },
                        { name: "Costa Rican Colón", code: "CRC" },
                        { name: "Croatian Kuna", code: "HRK" },
                        { name: "Cuban Convertible Peso", code: "CUC" },
                        { name: "Czech Republic Koruna", code: "CZK" },
                        { name: "Danish Krone", code: "DKK" },
                        { name: "Djiboutian Franc", code: "DJF" },
                        { name: "Dominican Peso", code: "DOP" },
                        { name: "East Caribbean Dollar", code: "XCD" },
                        { name: "Egyptian Pound", code: "EGP" },
                        { name: "Eritrean Nakfa", code: "ERN" },
                        { name: "Estonian Kroon", code: "EEK" },
                        { name: "Ethiopian Birr", code: "ETB" },
                        { name: "Euro", code: "EUR" },
                        { name: "Falkland Islands Pound", code: "FKP" },
                        { name: "Fijian Dollar", code: "FJD" },
                        { name: "Gambian Dalasi", code: "GMD" },
                        { name: "Georgian Lari", code: "GEL" },
                        { name: "German Mark", code: "DEM" },
                        { name: "Ghanaian Cedi", code: "GHS" },
                        { name: "Gibraltar Pound", code: "GIP" },
                        { name: "Greek Drachma", code: "GRD" },
                        { name: "Guatemalan Quetzal", code: "GTQ" },
                        { name: "Guinean Franc", code: "GNF" },
                        { name: "Guyanaese Dollar", code: "GYD" },
                        { name: "Haitian Gourde", code: "HTG" },
                        { name: "Honduran Lempira", code: "HNL" },
                        { name: "Hong Kong Dollar", code: "HKD" },
                        { name: "Hungarian Forint", code: "HUF" },
                        { name: "Icelandic Króna", code: "ISK" },
                        { name: "Indian Rupee", code: "INR" },
                        { name: "Indonesian Rupiah", code: "IDR" },
                        { name: "Iranian Rial", code: "IRR" },
                        { name: "Iraqi Dinar", code: "IQD" },
                        { name: "Israeli New Sheqel", code: "ILS" },
                        { name: "Italian Lira", code: "ITL" },
                        { name: "Jamaican Dollar", code: "JMD" },
                        { name: "Japanese Yen", code: "JPY" },
                        { name: "Jordanian Dinar", code: "JOD" },
                        { name: "Kazakhstani Tenge", code: "KZT" },
                        { name: "Kenyan Shilling", code: "KES" },
                        { name: "Kuwaiti Dinar", code: "KWD" },
                        { name: "Kyrgystani Som", code: "KGS" },
                        { name: "Laotian Kip", code: "LAK" },
                        { name: "Latvian Lats", code: "LVL" },
                        { name: "Lebanese Pound", code: "LBP" },
                        { name: "Lesotho Loti", code: "LSL" },
                        { name: "Liberian Dollar", code: "LRD" },
                        { name: "Libyan Dinar", code: "LYD" },
                        { name: "Litecoin", code: "LTC" },
                        { name: "Lithuanian Litas", code: "LTL" },
                        { name: "Macanese Pataca", code: "MOP" },
                        { name: "Macedonian Denar", code: "MKD" },
                        { name: "Malagasy Ariary", code: "MGA" },
                        { name: "Malawian Kwacha", code: "MWK" },
                        { name: "Malaysian Ringgit", code: "MYR" },
                        { name: "Maldivian Rufiyaa", code: "MVR" },
                        { name: "Mauritanian Ouguiya", code: "MRO" },
                        { name: "Mauritian Rupee", code: "MUR" },
                        { name: "Mexican Peso", code: "MXN" },
                        { name: "Moldovan Leu", code: "MDL" },
                        { name: "Mongolian Tugrik", code: "MNT" },
                        { name: "Moroccan Dirham", code: "MAD" },
                        { name: "Mozambican Metical", code: "MZM" },
                        { name: "Myanmar Kyat", code: "MMK" },
                        { name: "Namibian Dollar", code: "NAD" },
                        { name: "Nepalese Rupee", code: "NPR" },
                        { name: "Netherlands Antillean Guilder", code: "ANG" },
                        { name: "New Taiwan Dollar", code: "TWD" },
                        { name: "New Zealand Dollar", code: "NZD" },
                        { name: "Nicaraguan Córdoba", code: "NIO" },
                        { name: "Nigerian Naira", code: "NGN" },
                        { name: "North Korean Won", code: "KPW" },
                        { name: "Norwegian Krone", code: "NOK" },
                        { name: "Omani Rial", code: "OMR" },
                        { name: "Pakistani Rupee", code: "PKR" },
                        { name: "Panamanian Balboa", code: "PAB" },
                        { name: "Papua New Guinean Kina", code: "PGK" },
                        { name: "Paraguayan Guarani", code: "PYG" },
                        { name: "Peruvian Nuevo Sol", code: "PEN" },
                        { name: "Philippine Peso", code: "PHP" },
                        { name: "Polish Zloty", code: "PLN" },
                        { name: "Qatari Rial", code: "QAR" },
                        { name: "Romanian Leu", code: "RON" },
                        { name: "Russian Ruble", code: "RUB" },
                        { name: "Rwandan Franc", code: "RWF" },
                        { name: "Salvadoran Colón", code: "SVC" },
                        { name: "Samoan Tala", code: "WST" },
                        { name: "São Tomé and Príncipe Dobra", code: "STD" },
                        { name: "Saudi Riyal", code: "SAR" },
                        { name: "Serbian Dinar", code: "RSD" },
                        { name: "Seychellois Rupee", code: "SCR" },
                        { name: "Sierra Leonean Leone", code: "SLL" },
                        { name: "Singapore Dollar", code: "SGD" },
                        { name: "Slovak Koruna", code: "SKK" },
                        { name: "Solomon Islands Dollar", code: "SBD" },
                        { name: "Somali Shilling", code: "SOS" },
                        { name: "South African Rand", code: "ZAR" },
                        { name: "South Korean Won", code: "KRW" },
                        { name: "South Sudanese Pound", code: "SSP" },
                        { name: "Special Drawing Rights", code: "XDR" },
                        { name: "Sri Lankan Rupee", code: "LKR" },
                        { name: "St. Helena Pound", code: "SHP" },
                        { name: "Sudanese Pound", code: "SDG" },
                        { name: "Surinamese Dollar", code: "SRD" },
                        { name: "Swazi Lilangeni", code: "SZL" },
                        { name: "Swedish Krona", code: "SEK" },
                        { name: "Swiss Franc", code: "CHF" },
                        { name: "Syrian Pound", code: "SYP" },
                        { name: "Tajikistani Somoni", code: "TJS" },
                        { name: "Tanzanian Shilling", code: "TZS" },
                        { name: "Thai Baht", code: "THB" },
                        { name: "Tongan Pa'anga", code: "TOP" },
                        { name: "Trinidad & Tobago Dollar", code: "TTD" },
                        { name: "Tunisian Dinar", code: "TND" },
                        { name: "Turkish Lira", code: "TRY" },
                        { name: "Turkmenistani Manat", code: "TMT" },
                        { name: "Ugandan Shilling", code: "UGX" },
                        { name: "Ukrainian Hryvnia", code: "UAH" },
                        { name: "United Arab Emirates Dirham", code: "AED" },
                        { name: "Uruguayan Peso", code: "UYU" },
                        { name: "US Dollar", code: "USD" },
                        { name: "Uzbekistan Som", code: "UZS" },
                        { name: "Vanuatu Vatu", code: "VUV" },
                        { name: "Venezuelan BolÃvar", code: "VEF" },
                        { name: "Vietnamese Dong", code: "VND" },
                        { name: "Yemeni Rial", code: "YER" },
                        { name: "Zambian Kwacha", code: "ZMK" },
                        { name: "Zimbabwean dollar", code: "ZWL" }
                    ];
                    // Display the currency list and select the currency that the user has selected
                    for (let i = 0; i < currency_list.length; i++) {
                        if (currency_list[i].code === selectedCurrency) {
                            document.write(`<option id="${currency_list[i].name}" value="${currency_list[i].code}" selected>${currency_list[i].name}</option>`);
                        } else {
                            document.write(`<option id="${currency_list[i].name}" value="${currency_list[i].code}">${currency_list[i].name}</option>`);
                        }
                    }

                </script>
            </select>
            <h6>Note, that the currency set for the content will be the currency used to display the content of other creators</h6>
            <input type="text" name="amount" value="<?=$price?>" class="textinpfld" placeholder="Amount">
            
            <br>
            <input type="submit" value="Submit" class="submitbutton">
        </form>
    </div>
</body>

</html>