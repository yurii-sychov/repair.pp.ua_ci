<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>
		<div class="row my-2">
			<!-- <div class="col-lg-2">
				<select class="form-select my-1" id="FilterSubdivision">
					<option value="" selected>Всі підрозділи</option>
					<?php foreach ($subdivisions as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div> -->
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
				<select class="form-select my-1" id="FilterVoltageClass">
					<option value="" selected>Всі класи напруги</option>
					<?php foreach ($voltage_class as $item) :  ?>
						<option value="<?php echo htmlspecialchars($item->id);  ?>"><?php echo $item->voltage / 1000; ?> кВ</option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-lg-6">
				<div class="d-grid gap-2 d-sm-block">
					<button class="btn btn-success my-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Очистити сховище" id="clearLocalStorage"><i class="bi bi-x-square"></i></button>
					<!-- <a class="btn btn-info my-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#copyPropertiesModal">Копіювати характеристики</a> -->
					<?php if ($this->session->user->id == 1) : ?>
						<a class="btn btn-primary my-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPassportModal">Додати паспорт</a>
					<?php endif; ?>
					<button class="btn btn-danger my-1 dropdown-toggle" type="button" id="dropdownMenuButtonObjects" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="bi bi-file-earmark-pdf"></i> Генерувати паспорт підстанції
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonObjects">
						<?php foreach ($stantions as $item) : ?>
							<li>
								<a class="dropdown-item" href="/passports/gen_passport_object_pdf/<?php echo $item->id; ?>" target="_blank">
									<?php echo $item->name; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<button class="btn btn-warning my-1 dropdown-toggle" type="button" id="dropdownMenuButtonStantions" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="bi bi-file-earmark-pdf"></i> Генерувати експлуатаційну відомість
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonStantions">
						<?php foreach ($stantions as $item) : ?>
							<li>
								<a class="dropdown-item" href="/passports/gen_operating_list_object_pdf/<?php echo $item->id; ?>" target="_blank">
									<?php echo $item->name; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
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

<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button> -->

<!-- Modal Form Add Passport -->
<div class="modal fade" id="addPassportModal" tabindex="-1" aria-labelledby="addPassportModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Додавання паспорту обладнання</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('passports/form_passport_add');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary action" onclick="addPassport(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Form Edit Passport -->
<div class="modal fade" id="editPassportModal" tabindex="-1" aria-labelledby="editPassportModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Редагування паспорту обладнання</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('passports/form_passport_edit'); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary action" onclick="editPassport(event);">Змінити</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Form Add Properties -->
<div class="modal fade" id="addPropertiesModal" tabindex="-1" aria-labelledby="addPropertiesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Додавання характеристик обладнання</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('passports/form_properties'); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary action" onclick="addProperties(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Form Add Operating List -->
<div class="modal fade" id="addOperatingListModal" tabindex="-1" aria-labelledby="addOperatingListModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Додавання експлуатаційних даних</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('passports/form_operating_list');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info disabled">Розблокувати неактивні поля</button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary action" onclick="addOperatingList(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Form Copy Properties -->
<!-- <div class="modal fade" id="copyPropertiesModal" tabindex="-1" aria-labelledby="copyPropertiesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Копіювання характеристики обладнання</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('passports/form_copy_properties');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary action" onclick="copyProperties(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div> -->
