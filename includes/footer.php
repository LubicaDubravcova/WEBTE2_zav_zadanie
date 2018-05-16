<footer class="footer pt-2">
	<div class="container">
		<?php if ($role != false): ?>
		<div class="row">
			<div class="col">
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
		</div>
		<?php endif; ?>
		<div id="copyright" class="text-dark w-100 text-center">
			Copyright - Group 3 - 2018
		</div>
	</div>
	<script>
		$( document ).ready(function(){
			$("body").css("margin-bottom", $(".footer").height() + 20 + "px")
		});
	</script>
</footer>