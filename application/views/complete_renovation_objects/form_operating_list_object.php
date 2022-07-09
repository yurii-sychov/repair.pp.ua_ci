<form id="formAddOperatingListObject">
	<input type="hidden" name="subdivision_id" value="" id="idSubdivisionIdAdd" />
	<input type="hidden" name="complete_renovation_object_id" value="" id="idCompleteRenovationObjectIdAdd" />
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idServiceDateAdd" class="form-label">Дата обслуговування об`єкту</label>
			<input type="text" class="form-control datemask datepicker" id="idServiceDateAdd" placeholder="Введіть дату обслуговування об`єкту" name="service_date" autocomplete="on">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idServiceDataAdd" class="form-label">
				<?php echo anchor_popup('/complete_renovation_objects/get_value/service_data', 'Дані з експлуатації по об`єкту', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" class="form-control" id="idServiceDataAdd" placeholder="Введіть дані з експлуатації по об`єкту" name="service_data" autocomplete="on">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idExecutorAdd" class="form-label">
				<?php echo anchor_popup('/complete_renovation_objects/get_value/executor', 'Виконавець робіт', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" class="form-control" id="idExecutorAdd" placeholder="Введіть виконавця робіт" name="executor" autocomplete="on">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3 places">
		</div>
	</div>
</form>
