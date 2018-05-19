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
	JOIN users ON routes.OWNER=users.ID WHERE users.ID=$id";
	}
	else
		$sql = "SELECT routes.ID as ROUTE_ID, routes.NAME as ROUTE_NAME, routes.LENGTH, routes.TYPE, users.ID, users.FIRSTNAME, users.SURNAME FROM routes 
	JOIN users ON routes.OWNER=users.ID";
	$result = $db->getResult($sql); //aby hodil error ked je chyba
	
	$sql = "SELECT DISTINCT users.ID, users.FIRSTNAME, users.SURNAME FROM users 
	JOIN routes ON users.ID=routes.OWNER";
	$resultUser = $db->getResult($sql);
	$sql = "SELECT active.ID FROM ((SELECT routes.ID, routes.LENGTH, SUM(trainings.LENGTH) AS RUN FROM `routes` RIGHT JOIN trainings ON routes.ID = trainings.ROUTE_ID WHERE routes.TYPE = 'súkromná' AND routes.OWNER = $userData->ID AND trainings.USER_ID = routes.OWNER GROUP BY trainings.ROUTE_ID) 
        		UNION (SELECT routes.ID, routes.LENGTH, SUM(trainings.LENGTH) AS RUN FROM `routes` RIGHT JOIN trainings ON routes.ID = trainings.ROUTE_ID WHERE routes.TYPE = 'verejná' AND trainings.USER_ID = $userData->ID GROUP BY trainings.ROUTE_ID) 
                UNION (SELECT o.ROUTE_ID, o.LENGTH, p.RUN FROM (SELECT teams.ID as TEAM_ID, teams.ROUTE_ID, routes.LENGTH FROM routes INNER JOIN teams ON teams.ROUTE_ID = routes.ID INNER JOIN users_teams ON teams.ID = users_teams.TEAM_ID WHERE users_teams.USER_ID = $userData->ID) o INNER JOIN (SELECT SUM(trainings.LENGTH) AS RUN, users_teams.TEAM_ID FROM trainings RIGHT JOIN users_teams on trainings.USER_ID = users_teams.USER_ID GROUP BY users_teams.TEAM_ID) p ON o.TEAM_ID = p.TEAM_ID)) AS active WHERE active.RUN < active.LENGTH";
	$allowed = $db->getAssoc($sql,"ID");
	var_dump($allowed);
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
							$routedisabled = false;
							if ($userData->ACTIVE_ROUTE==$user["ROUTE_ID"])
							{$routeactive = true;echo 1;} else if(!in_array($user["ROUTE_ID"],$allowed)){$routedisabled = true; echo 3;} else echo 2;?>">
								<a class="<?php if($routeactive) echo "selected "; elseif ($routedisabled) echo "disabled "?>routeSelector" data-id="<?php echo $user["ROUTE_ID"] ?>"></a>
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
