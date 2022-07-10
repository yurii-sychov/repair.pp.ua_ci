<form id="formEditPassport">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" id="idEdit" name="id" />
	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idStantionEdit" class="form-label">Підстанція</label>
			<select class="form-select" id="idStantionEdit" name="complete_renovation_object_id" disabled>
				<option value="" selected>Оберіть підстанцію</option>
				<?php foreach ($stantions as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idEquipmentEdit" class="form-label">Вид обладнання</label>
			<select class="form-select" id="idEquipmentEdit" name="equipment_id" disabled>
				<option value="" selected>Оберіть вид обладнання</option>
				<?php foreach ($equipments as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idInsulationTypeEdit" class="form-label">Вид ізоляції</label>
			<select class="form-select" id="idInsulationTypeEdit" name="insulation_type_id">
				<option value="" selected>Оберіть вид ізоляції</option>
				<?php foreach ($insulation_type as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->insulation_type; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idPlaceEdit" class="form-label">Місце встановлення</label>
			<select class="form-select" id="idPlaceEdit" name="place_id" disabled>
				<option value="" selected>Оберіть місце встановлення</option>
				<?php foreach ($places as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idVoltageClassEdit" class="form-label">Клас напруги</label>
			<select class="form-select" id="idVoltageClassEdit" name="voltage_class_id" disabled>
				<option value="" selected>Оберіть клас напруги</option>
				<?php foreach ($voltage_class as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo ($item->voltage / 1000); ?> кВ</option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idDispEdit" class="form-label">Диспечерське найменування</label>
			<input type="text" class="form-control" id="idDispEdit" placeholder="Введіть диспечерське найменування" name="disp" disabled>
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idTypeEdit" class="form-label">Тип обладнання</label>
			<input type="text" class="form-control" id="idTypeEdit" placeholder="Введіть тип обладнання" name="type">
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idProductionDateEdit" class="form-label">Дата виготовлення</label>
			<input type="text" class="form-control datemask datepicker" id="idProductionDateEdit" placeholder="Введіть дату виготовлення" name="production_date">
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idNumberEdit" class="form-label">Номер</label>
			<input type="text" class="form-control" id="idNumberEdit" placeholder="Введіть номер" name="number">
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idRefinementMethodEdit" class="form-label">Спосіб уточнення</label>
			<select class="form-select" id="idRefinementMethodEdit" name="refinement_method">
				<option value="" selected>Оберіть спосіб уточнення</option>
				<option value="Дані уточнено з таблички обладнання">Дані уточнено з таблички обладнання</option>
				<option value="Дані уточнено з паперових паспортів">Дані уточнено з паперових паспортів</option>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>
</form>
