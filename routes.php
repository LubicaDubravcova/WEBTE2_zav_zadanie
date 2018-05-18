<!doctype html>
<html>
<head>
    <title>Záverečné Zadanie</title>
    <?php require("includes/head.php");?>
</head>
<body class="bg-dark text-white">
<?php require("includes/navbar.php"); ?>
<div class="container text-center">
    <div class="row">
        <div class="col">
            <h2 class="m-4 d-inline-block">Zoznam trás</h2>
        </div>
    </div>

	<div class='row'><div class='btn btn-block btn-danger disabled' style="display: none" id="activeWrong">Zvolenú trasu nie je možné nastaviť ako aktívnu.</div></div>

    <div class="row justify-content-center bg-light text-dark rounded p-5">
        <?php if ($role == "admin") :?>
            <div class="container" id="select">

            </div>
        <?php endif; ?>
        <div class="col">
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
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require("includes/footer.php");?>
<script src="scripts/sorttable.js"></script>
<script type="text/javascript">
function reloadContent() {
	$("#load").load("workers/routes.php",function() {
		var th = $(".sorttable_sorted,.sorttable_sorted_reverse")[0];
		if(th != undefined) {
			if ($(th).hasClass("sorttable_sorted")) {
				$(th).removeClass("sorttable_sorted");
				sorttable.innerSortFunction.apply(th, []);
			} else {
				$(th).removeClass("sorttable_sorted_reverse");
				sorttable.innerSortFunction.apply(th, []);
				sorttable.innerSortFunction.apply(th, []);
			}
		}
	});
}
function loadUsers() {
    $("#select").load("workers/selectUser.php",function(){});
}
$(document).on("click", '.routeSelector', function(event) {
    event.stopPropagation();
    $.post("workers/selectRoute.php", {id : <?php echo $userData->ID;?>, route: $(this).data("id")}, function(data){if (data == "1") reloadContent(); else {document.getElementById("activeWrong").style.display = "block"; setTimeout(hideActionWrong, 5000);}});
});
$(document).ready(function(){
	reloadContent();
	loadUsers();
	setInterval(reloadContent,5000);
});

function hideActionWrong() {
	document.getElementById("activeWrong").style.display = "none";
}
</script>
</body>
</html>
