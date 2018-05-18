<?php
header('Content-Type: text/html; charset=utf-8');
include_once("dbConn.php");
$db = new DBConn();
$userData = $db->getUserData();
$sql = "SELECT DISTINCT users.ID, users.FIRSTNAME, users.SURNAME FROM users 
JOIN routes ON routes.OWNER=users.ID";
$result = $db->getResult($sql); //aby hodil error ked je chyba?>


<form class="form-horizontal" action="workers/routes.php" method="post" >
    <div class="form-group">
        <label for="sel1">Užívateľ: </label>
        <select class="form-control" id="sell" name="selec">
            <?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user):?>
            <option value="<?php echo $user["ID"]?>"><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></option>
                <?php endforeach; ?>
        </select>
    </div>
</form>

<script>
    function User() {
        $("#sell").load("workers/routes.php",{routeId: "<?php echo $_POST["selec"];?>"},fixSortOnAjax);
    }

    $(document).ready(function(){
        User();
    });
</script>







