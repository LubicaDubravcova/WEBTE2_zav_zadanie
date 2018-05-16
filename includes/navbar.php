<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
	<a class="navbar-brand" href="#"><b>Route To Fitness</b></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
	  	</button>
	<div class="collapse navbar-collapse" id="navbarNavDropdown">
		<ul class="navbar-nav"> <!-- lave menu -->
			<li class="nav-item"><a class="nav-link" href="index.php">Domov</a>
			<?php if ($loggedIn): //Stránky ktoré sa zobrazia po prihlaseni sem ?>
			<li class="nav-item"><a class="nav-link" href="#">Something</a>
			<li class="nav-item"><a class="nav-link" href="#">Lorem Ipsun</a>
			<?php endif; ?>
		</ul>
		<ul class="navbar-nav ml-auto"> <!-- prave menu -->
			<?php if ($loggedIn): //User menu po prihlaseni ?>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarLoginMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Používateľ</a>
				<div class="dropdown-menu p-1" aria-labelledby="navbarLoginMenuLink">
					<a class="dropdown-item" href="#">Action</a>
					<a class="dropdown-item" href="#">Another action</a>
					<a class="dropdown-item" href="#">Something else here</a>
					<div class="dropdown-divider"></div>
					<a class="dropdown-item" href="#">And now for something completely different</a>
				</div>
			</li>
			<?php else: //prihlasovacie menu ?>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="navbarLoginMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Prihlásenie</a>
				<div class="dropdown-menu p-1" aria-labelledby="navbarLoginMenuLink">
					<form class="form-inline" method="post" action="workers/login.php">
						<input type="email" name="email" class="form-control m-1" placeholder="E-mail" id="loginEmail" aria-label="E-mail" required>
						<input type="password" name="password" class="form-control m-1" minlength="8" aria-label="Password" id="loginPassword" required placeholder="Heslo">
						<input type="hidden" name="site" value='<?php echo basename($_SERVER['PHP_SELF']);?>'>
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
	<script> //Script na oznacenie aktivneho linku v navbare
		if (!Array.prototype.last){ //metoda na ziskanie posledneho clena pola
			Array.prototype.last = function(){
				return this[this.length - 1];
			};
		};
		$(".nav-item .nav-link").each(function(){
			console.log(this.href.split('/').last() +"=="+ window.location.pathname.split('/').last());
			if (this.href.split('/').last() == window.location.pathname.split('/').last())
				this.parentElement.classList.add("active");
		});
	</script>
</nav>