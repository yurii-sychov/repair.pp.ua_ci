<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>
		<div class="row my-2">
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterStantion">
					<option value="" selected>Всі підстанції</option>
					<?php foreach ($stantions as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterEquipment">
					<option value="" selected>Все обладнання</option>
					<?php foreach ($equipments as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterTypeService">
					<option value="" selected>Всі види ремонтів</option>
					<?php foreach ($type_services as $item) :  ?>
						<option value="<?php echo $item->id;  ?>"><?php echo $item->name; ?></option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterVoltageClass">
					<option value="" selected>Всі класи напруги</option>
					<?php foreach ($voltage_class as $item) :  ?>
						<option value="<?php echo htmlspecialchars($item->id);  ?>"><?php echo $item->voltage / 1000; ?> кВ</option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-lg-4">
				<div class="d-grid gap-2 d-sm-block">
					<!-- <button class="btn btn-primary my-1" title="Скинути всі фільтри" id="ResetFilters"><i class="bi bi-x-square"></i></button> -->
					<button class="btn btn-success my-1" title="Очистити LocalStorage" id="clearLocalStorage"><i class="bi bi-x-square"></i></button>
					<a class="btn btn-danger my-1" href="/multi_year_schedule/get_schedule_kr" role="button" title="Tooltip on top">
						<i class="bi bi-file-earmark-pdf"></i> КР
					</a>
					<a class="btn btn-danger my-1" href="/multi_year_schedule/get_schedule_pr" role="button" title="Tooltip on top">
						<i class="bi bi-file-earmark-pdf"></i> ПР
					</a>

					<a class="btn btn-danger my-1" href="/multi_year_schedule/get_schedule_to" role="button" title="Tooltip on top">
						<i class="bi bi-file-earmark-pdf"></i> ТО
					</a>
				</div>
			</div>
		</div>

		<div class="loading text-center">
			<div class="spinner-border text-secondary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>

		<table class="table table-hover table-bordered" id="datatables"></table>

	</div>
</div>
