<?php
header('Content-Type: text/html; charset=utf-8');


include_once("workers/dbConn.php");
$conn = new DBConn();
//var_dump($_POST);
$routeID = $_GET['routeID'];
//if($routeID == "") $routeID = 1;        //TODO tento riadok zmazat sluzi iba na test
if($_POST['selectUser_0'] != "" || $_POST['selectUser_1'] != "" || $_POST['selectUser_2'] != "" || $_POST['selectUser_3'] != "" || $_POST['selectUser_4'] != "" || $_POST['selectUser_5'] != ""){
    if($_GET['teamID'] == "")
        $teamID = $conn->createTeam($routeID);
    else{
        $teamID = $_GET['teamID'];
        $conn->dropTeamMembers($_GET['teamID']);       //nebudem sa s tym srat a rovno dropnem clenov teamu a nahram novych
    }
    if($_POST['selectUser_0'] != "")
        $conn->addToTeam($_POST['selectUser_0'], $teamID);
    if($_POST['selectUser_1'] != "")
        $conn->addToTeam($_POST['selectUser_1'], $teamID);
    if($_POST['selectUser_2'] != "")
        $conn->addToTeam($_POST['selectUser_2'], $teamID);
    if($_POST['selectUser_3'] != "")
        $conn->addToTeam($_POST['selectUser_3'], $teamID);
    if($_POST['selectUser_4'] != "")
        $conn->addToTeam($_POST['selectUser_4'], $teamID);
    if($_POST['selectUser_5'] != "")
        $conn->addToTeam($_POST['selectUser_5'], $teamID);
}

$result = $conn->getAssoc("SELECT ID, FIRSTNAME, SURNAME FROM users ORDER BY SURNAME ASC");
//$result = json_encode($result, JSON_UNESCAPED_UNICODE);
?>
<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link href="https://code.jquery.com/ui/1.12.1/themes/black-tie/jquery-ui.css" rel="stylesheet">
    <style>.ui-autocomplete {max-height: 100px;overflow-y: auto;overflow-x: hidden;text-align:left;}
        .custom-combobox {
            position: relative;
            display: inline-block;
        }
        .custom-combobox-toggle {
            position: absolute;
            top: 0;
            bottom: 0;
            margin-left: -1px;
            padding: 0;
        }
        .custom-combobox-input {
            margin: 0;
            padding: 5px 10px;
        }</style>
</head>
<body class="bg-dark text-white text-center">
<?php //require("includes/navbar.php"); ?>
<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Nový tím</h2>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8 col-md-offset-3 bg-light text-dark rounded p-5">
            <?php //if ($alreadyExists) echo "<div class='row form-group'><div class='btn btn-block btn-danger disabled'>E-mail sa už používa</div></div>"?>
            <form method="post" action="#">

                <?php
                for($memberNum = 0; $memberNum < 6; $memberNum++) {
                    echo "<div class=\"form-group row\">
                            <label for=\"selectUser" . $memberNum . "\" class=\"col-sm-3 col-form-label text-right\">Člen " . ($memberNum + 1) . ":</label>
                            <div class=\"dropdown\">
                                <select id=\"selectUser " . $memberNum . "\" class=\"form-control\" name=\"selectUser " . $memberNum . "\">
                                    <option></option>";
                    $i = 0;

                    if($_GET['teamID'] != ""){  //máme na vstupe ID teamu, používateľ ho chce teda editovať miesto tvorenia nového tímu
                        $teamMembers = $conn->getAssoc("SELECT users.ID AS ID FROM users JOIN users_teams ON users_teams.USER_ID=users.ID WHERE users_teams.TEAM_ID = " . $_GET['teamID']);
                        while ($result[$i]) {
                            echo "<option value=\"" . $result[$i]['ID'] . "\"";
                            if($teamMembers[$memberNum] && $teamMembers[$memberNum]['ID'] == $result[$i]['ID']){
                                echo " selected ";
                            }
                            echo ">" . $result[$i]['SURNAME'] . " " . $result[$i]['FIRSTNAME'] . "</option>";
                            $i++;
                        }
                    }else{
                        while ($result[$i]) {
                            echo "<option value=\"" . $result[$i]['ID'] . "\">" . $result[$i]['SURNAME'] . " " . $result[$i]['FIRSTNAME'] . "</option>";
                            $i++;
                        }
                    }
                    echo "</select></div></div>";
                }
                ?>

                <div class="form-group row">
                    <button type="submit" class="btn btn-dark btn-lg btn-block"><?php if($_GET['teamID'] == "") echo "Vytvor tím"; else echo "Edituj tím"; ?></button>
                </div>
                <div class="form-group row">
                    <a href="route.php?routeId=<?php echo $routeID; ?>" class="btn btn-block btn-default bg-white">Naspäť k podrobnostiam trasy</a>
                </div>
            </form>
        </div>
    </div>
</div>
<!--<script src="scripts/register.js"></script>-->
</body>
</html>