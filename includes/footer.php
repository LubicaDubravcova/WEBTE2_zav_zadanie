<footer class="footer pt-2">
	<div class="container">
		<?php if ($role != false): ?>
		<div class="row">
			<div class="col-10">
				<div id="newsCarousel" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<div class="carousel-content">
								<h4>News Example 1</h4>
								<p>Takto budu vyzerat news</p>
							</div>
						</div>
						<div class="carousel-item">
							<div class="carousel-content">
								<h4>News Example 2</h4>
								<p>Bude tam carousel</p>
							</div>
						</div>
						<div class="carousel-item">
							<div class="carousel-content">
								<h4>News Example 3</h4>
								<p>Cez ajax sa bude nacitavat posledych 5, resp 3 alebo kolko chceme</p>
							</div>
						</div>
					</div>
					<a class="carousel-control-prev" href="#newsCarousel" role="button" data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="sr-only">Predchádzajúci</span>
					</a>
					<a class="carousel-control-next" href="#newsCarousel" role="button" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Ďalší</span>
					</a>
				</div>
			</div>
			<div class="col text-dark">
				<label class="chckbx d-flex flex-row w-100 h-100 align-items-center">
					<div class="m-2">Subscribe</div>
					<input id="subCheck" type="checkbox" <?php if($userData->SUBSCRIBED) echo "checked";?>>
					<span class="checkmark"></span>
				</label>
			</div>
		</div>
		<?php endif; ?>
		<div id="copyright" class="text-dark w-100 text-center">
			Copyright - Group 3 - 2018
		</div>
	</div>
	<script>
		$( document ).ready(function(){
			$("body").css("margin-bottom", $(".footer").height() + 20 + "px");
		});
		<?php if ($role != false): ?>
		$("#subCheck").change(function(){
			$.post("workers/subscribe.php", {id : <?php echo $userData->ID;?>, sub : this.checked});
		});
		<?php endif; ?>
	</script>
</footer>