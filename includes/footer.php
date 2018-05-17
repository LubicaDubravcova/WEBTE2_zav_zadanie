<footer class="footer pt-2">
	<div class="container">
		<?php if ($role != false): ?>
		<div class="row">
			<div class="col-10">
				<div id="newsCarousel" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
					<?php $res2 = $db->getDB()->query("SELECT ID, Nazov, Text FROM news ORDER BY id DESC");
					$first = true;
					for($i = 0; $i<3; $i++):
						if (($obj = $res2->fetch_object()) == false) break; ?>
						<div class="carousel-item <?php if ($first) {echo "active", $first = false;} ?>">
							<a style="text-dark" href="news.php?news=<?php echo $obj->ID;?>"><div class="carousel-content">
								<h4><?php echo $obj->Nazov; ?></h4>
								<p>
								<?php $lines = explode("\n", $obj->Text);
									if (count($lines) > 1) {
										$text = $lines[0]."\n".$lines[1];
									} else $text = $lines[0];
									if (strlen($text) > 200) $text = substr($text,0,200)."...";
									echo $text;
								?>
								</p>
							</div></a>
						</div>
					<?php endfor; ?>
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
