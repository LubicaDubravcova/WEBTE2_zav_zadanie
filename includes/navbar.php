<?php 
$role = false;
session_start();

function banish() { //presmerovanie ak je v inej stranke nez ma povolene
	header('Location: index.php'); 
	die();
}

//---------- Nastavenia povolenych stranok -----------//
$currentSite = basename($_SERVER['PHP_SELF']);
$guestAllowed = array("index.php","register.php");//povolene stranky pre hosta
$userAllowed = array("index.php","route.php","news.php","add-route.php", "add-training.php");//povolene stranky pre prihlaseneho pouzivatela

//--------------- Ziskanie dat z DB ---------------//
if (isset($_SESSION["login"])) {
	include_once("workers/dbConn.php");
	if (!isset($db)) $db = new DBConn();
	$userData = $db->getUserData(); //objekt s datami pouzivatela
	$role = $userData->ROLE;
	
	// --------- Overenie ci je v spravnej stranke --------//
	if ($role == 'user' && !in_array($currentSite, $userAllowed)) banish();
} elseif (!in_array($currentSite, $guestAllowed)) banish();
?>
<header>
	<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
		<a class="navbar-brand" href="#"><b>Route To Fitness</b></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
		<div class="collapse navbar-collapse" id="navbarNavDropdown">
			<ul class="navbar-nav"> <!-- lave menu -->
				<li class="nav-item"><a class="nav-link" href="index.php">Domov</a>
				<?php if ($role == "user"): //Stránky ktoré sa zobrazia po prihlaseni sem ?>
                <li class="nav-item"><a class="nav-link" href="routes.php">Zoznam trás</a>
				<?php elseif ($role == "admin"): //Stránky ktoré sa zobrazia administrátorovi, ak sa nejaka zobrazuje obom tak ju dajte aj sem ?>
                <li class="nav-item"><a class="nav-link" href="users.php">Užívatelia</a>
                <li class="nav-item"><a class="nav-link" href="routes.php">Zoznam trás</a>
				<?php endif; ?>
			</ul>
			<ul class="navbar-nav ml-auto"> <!-- prave menu -->
				<?php if ($role == "user"): //User menu po prihlaseni ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarLoginMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $userData->FIRSTNAME." ".$userData->SURNAME;?></a>
					<div class="dropdown-menu p-1" aria-labelledby="navbarLoginMenuLink">
						<a class="dropdown-item" href="add-route.php">Vytvoriť trasu</a>
						<a class="dropdown-item" href="add-training.php">Pridať tréning</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="workers/logout.php">Odhlásenie</a>
					</div>
				</li>
				<?php elseif ($role == "admin"): //admin menu ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarLoginMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $userData->FIRSTNAME." ".$userData->SURNAME." (administrátor)";?></a>
					<div class="dropdown-menu p-1" aria-labelledby="navbarLoginMenuLink">
						<a class="dropdown-item" href="add-route.php">Vytvoriť trasu</a>
						<a class="dropdown-item" href="add-training.php">Pridať tréning</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="add-news.php">Pridaj aktualitu</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="workers/logout.php">Odhlásenie</a>
					</div>
				</li>
				<?php else: //prihlasovacie menu ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarLoginMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Prihlásenie</a>
					<div class="dropdown-menu p-1" aria-labelledby="navbarLoginMenuLink">
						<form class="form-inline" method="post" action="#" id="loginForm">
							<input type="email" name="email" class="form-control m-1" placeholder="E-mail" id="loginEmail" aria-label="E-mail" required>
							<input type="password" name="password" class="form-control m-1" minlength="8" aria-label="Password" id="loginPassword" required placeholder="Heslo">
							<div class="dropdown-divider"></div>
							<div class="w-100 row m-0">
								<div class="col-6 p-1">
									<button class="btn btn-outline-secondary w-100" type="submit">Prihlásenie</button>
								</div>
								<div class="col-6 p-1">
									<a class="btn btn-outline-secondary w-100" href="register.php">Registrácia</a>
								</div>
							</div>
						</form>
					</div>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</nav>
	<script> //Script na oznacenie aktivneho linku v navbare
		if (!Array.prototype.last){ //metoda na ziskanie posledneho clena pola
			Array.prototype.last = function(){
				return this[this.length - 1];
			};
		};
		$(".nav-item .nav-link").each(function(){
			if (this.href.split('/').last() == window.location.pathname.split('/').last())
				this.parentElement.classList.add("active");
		});
		$('#loginEmail').change(function(){
			this.setCustomValidity('');
		});
		$('#loginPassword').change(function(){
			$('#loginEmail')[0].setCustomValidity('');
		});
		$('#loginForm').submit(function(e){
			e.preventDefault();
			$.post("workers/login.php",$(this).serialize(),function(data){
				if (JSON.parse(data) == true) {
					window.location.replace("index.php");
				} else {
					var email = $('#loginEmail')[0];
					email.setCustomValidity('Nesprávny e-mail alebo heslo!');
					email.reportValidity();
				}
			});
		});
	</script>
</header>
