<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white">
<?php require("includes/navbar.php"); 
	$id = $_POST['id'];
	if ($id == "null") $id = null;
	if ($id!=null) {
		$sql = "SELECT routes.ID as ROUTE_ID, routes.NAME as ROUTE_NAME, routes.LENGTH, routes.TYPE, users.ID, users.FIRSTNAME, users.SURNAME FROM routes 
	JOIN users ON routes.OWNER=users.ID WHERE users.ID=" . $id;
	}
	else
		$sql = "SELECT routes.ID as ROUTE_ID, routes.NAME as ROUTE_NAME, routes.LENGTH, routes.TYPE, users.ID, users.FIRSTNAME, users.SURNAME FROM routes 
	JOIN users ON routes.OWNER=users.ID";
	$result = $db->getResult($sql); //aby hodil error ked je chyba
	
	$sql = "SELECT DISTINCT users.ID, users.FIRSTNAME, users.SURNAME FROM users 
	JOIN routes ON users.ID=routes.OWNER";
	$resultUser = $db->getResult($sql);
?>
<div class="container text-center">
    <div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Zoznam trás</h2>
        </div>
    </div>

	<div class='row'><div class='btn btn-block btn-danger disabled' style="display: none" id="activeWrong">Zvolenú trasu nie je možné nastaviť ako aktívnu.</div></div>
    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <div class="col">
        <?php if ($role == "admin") :?>
		<form class="form-inline">
			<div class="form-group">
				<label for="sel">Užívateľ: </label>
				<select class="form-control" id="sel">
					<option value=null>Všetky trasy</option>
					<?php foreach($resultUser->fetch_all(MYSQLI_ASSOC) as $user):?>
					<option value="<?php echo $user["ID"]?>"><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</form>
        <?php endif; ?>
            <div class='table-responsive'>
                <table class='table sortable table-hover'>
                    <thead>
                    <tr>
                        <th>Trasa</th>
                        <th class="sorttable_numeric">Dĺžka</th>
                        <th>Aktívna</th>
                        <th>Mód</th>
                        <?php if ($role == "admin") :?>
                        <th>Pridal</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody id="load">
                    <?php foreach($result->fetch_all(MYSQLI_ASSOC) as $user):
						if(($userData->ROLE == "admin") or (($userData->ROLE == "user") and (($user["TYPE"] != "Súkromná") or $user["ID"] == $userData->ID))): ?>
						<tr class="clickable-row" data-href="route.php?routeId=<?php echo $user["ROUTE_ID"]; ?>">
							<td><?php echo $user["ROUTE_NAME"]; ?></td>
							<td><?php echo number_format($user["LENGTH"]/1000,2,","," ")."km"; ?></td>
							<td sorttable_customkey="<?php
                            $routeactive = false;

							if ($userData->ACTIVE_ROUTE==$user["ROUTE_ID"])

							{$routeactive = true; echo 1;} else echo 2;
                            ?>">
                            <?php if (($user["ID"] == $userData->ID && $user["TYPE"] == "Súkromná") || ($user["TYPE"] == "Verejná")):?>
								<a class="<?php if($routeactive) echo "selected "; ?>routeSelector" data-id="<?php echo $user["ROUTE_ID"] ?>"></a>
                            <?php else:?>
                                <a class="<?php echo "disabled "; ?>routeSelector"></a>
                                <?php endif;?>
                            </td>
							<td><?php echo $user["TYPE"]; ?></td>
							<?php if($userData->ROLE == "admin"): ?>
								<td><?php echo $user["FIRSTNAME"]." ".$user["SURNAME"]; ?></td>
							<?php endif; ?>
						</tr>
					<?php endif; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
<script type="text/javascript">

//neda sa pouzit, kvoli moznosti zmeny pouzivatela
//selfLoad("#load",true,<?php echo $_SERVER['QUERY_STRING'];?>); 
var user = "null";
function reloadContent() {
	$("#load").load(document.URL + " #load tr",{id: user},fixSortOnAjax);
}

$("#sel").change(function() {
	user = this.value;
	reloadContent();
});

setInterval(reloadContent,5000);

$(document).on("click", '.routeSelector', function(event) {
    event.stopPropagation();
	if (!($(this).hasClass("disabled")))
		$.post("workers/selectRoute.php", {id : <?php echo $userData->ID;?>, route: $(this).data("id")}, function(data){
			if (data == "1") {
				reloadContent(); 
				document.getElementById("activeWrong").style.display = "none";} 
			else {
				document.getElementById("activeWrong").style.display = "block";}
		});
});
</script>
</body>
</html>
