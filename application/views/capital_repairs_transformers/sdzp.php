<div class="row">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
				<div class="card-header mb-2 text-center">
					<h5><?php echo $title_heading_card_sp; ?></h5>
				</div>
				<div class="col-lg-12">
					<div class="row" id="containerScrollbarStantions">
						<?php foreach ($stantions_sp as $stantion) : ?>
							<div class="col-lg-6 mb-4">
								<div class="card shadow-lg bg-light rounded">
									<div class="card-body">
										<p class="card-title text-center"><a href="/capital_repairs_transformers/sdzp/<?php echo $stantion->subdivision_id . '/' . $stantion->id; ?>" class="btn shadow-none"><?php echo $stantion->name; ?></a></p>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="card">
			<div class="card-body">
				<div class="card-header mb-2 text-center">
					<h5><?php echo $title_heading_card_srm; ?></h5>
				</div>
				<div class="col-lg-12">
					<div class="row" id="containerScrollbarSubdivitions">
						<?php foreach ($subdivision_srm as $subdivision) : ?>
							<div class="col-lg-6 mb-4">
								<div class="card shadow-lg bg-light rounded">
									<div class="card-body">
										<p class="card-title text-center"><a href="/capital_repairs_transformers/sdzp/<?php echo $subdivision->id; ?>" class="btn shadow-none"><?php echo $subdivision->name; ?></a></p>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
