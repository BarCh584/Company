<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    include('../Libraries/navbar.php');
    createnavbar("search");
    ?>
    <ul class="innavbar">
        <input type="text" oninput="search();" class="searchbar" placeholder="Search for a username" id="username">
        <div id="search-results"></div>
    </ul>
    <div class="innormalcontentnavbar">
        <?php

        if (isset($_GET["username"])) {
            $username = $_GET["username"];
            $servername = "localhost";
            $uname = "root";
            $password = "";
            $dbname = "Company";

            $conn = new mysqli($servername, $uname, $password, $dbname);
            ?>
            <h1><?= $username ?></h1>
            <form id='adminform' method="POST">
                <h2>Penalty</h2>
                <?php
                permissions();
                ?>
                <input type="submit" name="submit" value="Save">
            </form>
            <button onclick="clearform();">Clear</button>
            <?php
            showreports();
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $penalty = isset($_POST["penalty"]) ? $_POST["penalty"] : "";
                $reason = isset($_POST["reason"]) ? $_POST["reason"] : "";
                $otherreason = isset($_POST["otherreason"]) ? $_POST["otherreason"] : "";
                $creator = $_SESSION["username"];
                $creatorid = $_SESSION["id"];
                if (isset($_POST["shadowban"]) && $_POST["shadowban"] != "") {
                    $penalty = "shadowban";
                }
                if ($otherreason != "") {
                    $reason = $otherreason;
                }
                $userid = getUserIdByUsername($conn, $username);
                if ($userid) {
                    $penaltystmt = $conn->prepare("INSERT INTO penalties (username, userid, penalty, reason, creator, creatorid) VALUES (?, ?, ?, ?, ?, ?)");
                    $penaltystmt->bind_param("ssssss", $username, $userid, $penalty, $reason , $creator, $creatorid);
                    $penaltystmt->execute();
                    $penaltystmt->close();
                } else {
                    echo "User ID not found. ";
                }
            }
            $conn->close();
        }
        ?>

    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const otherReasonElement = document.getElementById("otherreason");
        if (otherReasonElement) {
            otherReasonElement.style.display = "none";
            document.querySelectorAll("input[name='reason']").forEach((radio) => {
                radio.addEventListener("change", function () {
                    if (document.getElementById("other").checked) {
                        otherReasonElement.style.display = "block";
                    } else {
                        otherReasonElement.style.display = "none";
                    }
                });
            });
        }
        function search() {
            const username = document.getElementById("username").value;
            if (username.length >= 0) {
                $.ajax({
                    url: '../Libraries/adminsearch.php',
                    type: 'POST',
                    data: { username: username },
                    success: function (response) {
                        $('#search-results').html(response);
                    },
                    error: function () {
                        alert('An error occurred while processing your request.');
                    }
                });
            }
        }
        $(document).ready(function () {
            const username = document.getElementById("username").value;
            $.ajax({
                url: '../Libraries/adminsearch.php',
                type: 'POST',
                data: { username: username },
                success: function (response) {
                    $('#search-results').html(response);
                },
                error: function () {
                    alert('An error occurred while processing your request.');
                }
            });
        });
        function clearform() {
            document.querySelectorAll("input[type='radio']").forEach((radio) => {
                radio.checked = false;
            });
            document.getElementById("shadowban").checked = false;
            document.querySelector("textarea").value = "";
            document.getElementById("otherreason").style.display = "none";
        }
    </script>
    <?php
    function checkpenalty($value)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT penalty FROM penalties WHERE username = ?");
        $stmt->bind_param("s", $_GET["username"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row != null) {
            if (str_contains($row["penalty"], $value)) {
                return "checked";
            }
        }
    }
    function checkpermissions($value)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT permissions FROM users WHERE username = ?");
        $stmt->bind_param("s", $_GET["username"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row != null) {
            if (str_contains($row["permissions"], $value)) {
                return "checked";
            }
        }
    }
    function permissions()
    {
        global $conn;
        $username = $_SESSION["username"];
        $permissionstmst = $conn->prepare("SELECT permissions FROM users WHERE username = ?");
        $permissionstmst->bind_param("s", $username);
        $permissionstmst->execute();
        $result = $permissionstmst->get_result();
        $row = $result->fetch_assoc();
        if ($row != null) {
            if (str_contains($row["permissions"], "*")) { ?>
                <input type="radio" <?= checkpenalty("*"); ?> name="penalty" value="strike" id="penalty_strike">
                <label for="penalty_strike">Strike user</label>
                <br>
                <input type="checkbox" <?= checkpenalty("*"); ?> style="display: inline !important;" name="ban" value="ban"
                    id="penalty_ban">
                <label for="penalty_ban">Ban</label>
                <br>
                <input style="display: inline !important;" type="checkbox" <?= checkpenalty(value: "*"); ?> name="shadowban"
                    value="test" id="penalty_shadowban">
                <label for="penalty_shadowban">Shadowban user</label>
                <br><br>
                <h3>Reason for penalty</h3>
                <input type="radio" name="reason" id="reason_hatespeech" value="Hate Speech">
                <label for="reason_hatespeech">Hate speech</label>
                <br>
                <input type="radio" name="reason" id="reason_harassmentorbullying" value="Harassment or bullying">
                <label for="reason_harassmentorbullying">Harassment or bullying</label>
                <br>
                <input type="radio" name="reason" id="reason_falseinformation" value="False information">
                <label for="reason_falseinformation">False information</label>
                <br>
                <input type="radio" name="reason" id="reason_illegalactivities" value="Promotes and/or sells illegal activities">
                <label for="reason_illegalactivities">Promotes and/or sells illegal activities</label>
                <br>
                <input type="radio" name="reason" id="reason_harmfullcontent" value="Harmful content">
                <label for="reason_harmfullcontent">Harmful content</label>
                <br>
                <input type="radio" name="reason" id="reason_impersonation" value="Impersonation">
                <label for="reason_impersonation">Impersonation</label>
                <br>
                <input type="radio" name="reason" id="reason_other" value="Other">
                <label for="reason_other">Other</label>
                <br>
                <textarea id="otherreason" name="otherreason"></textarea>
                <h2>Permissions</h2>
                <input <?= checkpermissions("*"); ?> style="display: inline !important;" type="checkbox" name="perm" value="view"
                    id="perm_view">
                <label for="perm_view">View</label>
                <br>
                <input <?= checkpermissions("*"); ?> style="display: inline !important;" type="checkbox" name="perm" value="banning"
                    id="perm_banning">
                <label for="perm_banning">Ban</label>
                <br>
                <input <?= checkpermissions("*"); ?> style="display: inline !important;" type="checkbox" name="perm" value="strike"
                    id="perm_strike">
                <label for="perm_strike">Strike</label>
                <br>
                <input <?= checkpermissions("*"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="shadowbanning" id="perm_shadowbanning">
                <label for="perm_shadowbanning">Shadowban</label>
                <br>
                <input <?= checkpermissions("*"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="penaltydist" id="perm_penaltydist">
                <label for="perm_penaltydist">Distribute penalties</label>
                <br>
                <input <?= checkpermissions("*"); ?> style="display: inline !important;" type="checkbox" name="perm" value="chgperm"
                    id="chgperm">
                <label for="chgperm">Change permission</label>
                <br>
                <?php
            }
            if (str_contains($row["permissions"], "ban")) { ?>
                <input <?= checkpermissions("ban"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="banning" id="perm_banning">
                <label for="perm_banning">Ban</label>
                <br>
                <?php
            }
            if (str_contains($row["permissions"], "strike")) { ?>
                <input <?= checkpermissions("strike"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="strike" id="perm_strike">
                <label for="perm_strike">Strike</label>
                <br>
                <?php
            }
            if (str_contains($row["permissions"], "shadowban")) { ?>
                <input <?= checkpermissions("shadowban"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="shadowbanning" id="perm_shadowbanning">
                <label for="perm_shadowbanning">Shadowban</label>
                <br>
                <?php
            }
            ?>
            <br>
            <?php
            if (str_contains($row["permissions"], "distributepenalties")) { ?>
                <h3>Reason for penalty</h3>
                <input type="radio" name="reason" id="reason_hatespeech" value="Hate Speech">
                <label for="reason_hatespeech">Hate speech</label>
                <br>
                <input type="radio" name="reason" id="reason_harassmentorbullying" value="Harassment or bullying">
                <label for="reason_harassmentorbullying">Harassment or bullying</label>
                <br>
                <input type="radio" name="reason" id="reason_falseinformation" value="False information">
                <label for="reason_falseinformation">False information</label>
                <br>
                <input type="radio" name="reason" id="reason_illegalactivities" value="Promotes and/or sells illegal activities">
                <label for="reason_illegalactivities">Promotes and/or sells illegal activities</label>
                <br>
                <input type="radio" name="reason" id="reason_harmfullcontent" value="Harmful content">
                <label for="reason_harmfullcontent">Harmful content</label>
                <br>
                <input type="radio" name="reason" id="reason_impersonation" value="Impersonation">
                <label for="reason_impersonation">Impersonation</label>
                <br>
                <input type="radio" name="reason" id="reason_other" value="Other">
                <label for="reason_other">Other</label>
                <br>
                <textarea id="otherreason" name="otherreason"></textarea>
                <?php
            }
            if (str_contains($row["permissions"], "changepermissions")) { ?>
                <h2>Permissions</h2>
                <input <?= checkpermissions("view"); ?> style="display: inline !important;" type="checkbox" name="perm" value="view"
                    id="perm_view">
                <label for="perm_view">View</label>
                <br>
                <input <?= checkpermissions("ban"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="banning" id="perm_banning">
                <label for="perm_banning">Ban</label>
                <br>
                <input <?= checkpermissions("strike"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="strike" id="perm_strike">
                <label for="perm_strike">Strike</label>
                <br>
                <input <?= checkpermissions("shadowban"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="shadowbanning" id="perm_shadowbanning">
                <label for="perm_shadowbanning">Shadowban</label>
                <br>
                <input <?= checkpermissions("penaltydist"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="penaltydist" id="perm_penaltydist">
                <label for="perm_penaltydist">Distribute penalties</label>
                <br>
                <input <?= checkpermissions("chgperm"); ?> style="display: inline !important;" type="checkbox" name="perm"
                    value="chgperm" id="chgperm">
                <label for="chgperm">Change permission</label>
                <br>
                <?php
            }
        } else {
            echo "User not found. ";
        }

    }
    function showreports()
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM reports WHERE reportedusername = ?");
        $stmt->bind_param("s", $_GET["username"]);
        $stmt->execute();
        $result = $stmt->get_result(); ?>
        <h2>Reports</h2>
        <table>
            <tr>
                <th>
                    <p>Applicant</p>
                </th>
                <th>
                    <p>Reported user</p>
                </th>
                <th>
                    <p>Reported type</p>
                </th>
                <th>
                    <p>Reported content id</p>
                </th>
                <th>
                    <p>Created at</p>
                </th>
                <th>
                    <p>Status</p>
                </th>
            </tr>
            <?php
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <p><?= $row["applicantusername"] . "[" . $row["applicantuserid"] . "]" ?></p>
                    </td>
                    <td>
                        <p><?= $row["reportedusername"] . "[" . $row["reporteduserid"] . "]" ?> </p>
                    </td>
                    <td>
                        <p><?= $row["reportedtype"]; ?></p>
                    </td>
                    <td>
                        <p><?= $row["reportedcontentid"]; ?></p>
                    </td>
                    <td>
                        <p><?= $row["createdat"]; ?></p>
                    </td>
                    <td>
                        <p><?= $row["status"]; ?></p>
                    </td>
                </tr>
                <?php
            } 
            if($result->num_rows == 0) { ?>
                <tr>
                    <td colspan="6">
                        <p>Nobody reported this user</p>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <br><br>
        <h2>Penalties</h2>
        <table>
            <tr>
                <th><p>Creator</p></th>
                <th><p>Penalty</p></th>
                <th><p>Reason</p></th>
                <th><p>Created at:</p></th>
            </tr>
            <?php
            $penstmt = $conn->prepare("SELECT * FROM penalties WHERE username = ?");
            $penstmt->bind_param("s", $_GET["username"]);
            $penstmt->execute();
            $penresult = $penstmt->get_result();
            while($row = $penresult->fetch_assoc()) { ?>
                <tr>
                    <td><p><?=$row["creator"] . "[". $row["creatorid"] . "]"?></p></td>
                    <td><p><?=$row["penalty"]?></p></td>
                    <td><p><?=$row["reason"]?></p></td>
                    <td><p><?=$row["createdat"]?></p></td>
                </tr>
                <?php
            }
            if($penresult->num_rows == 0) { ?>
                <tr>
                    <td colspan="4">
                        <p>This user has no penalties</p>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }


    ?>
</body>

</html>