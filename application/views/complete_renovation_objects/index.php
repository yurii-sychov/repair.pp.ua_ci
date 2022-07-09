<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>
		<?php if ($this->session->user->group === 'admin') : ?>
			<div class="row my-2">
				<div class="col-lg-6">
					<a class="btn btn-primary my-1 disabled" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addOperatingListObjectsModal">Додати енергетичний об`єкт</a>
				</div>
			</div>
		<?php endif; ?>
		<div class="table-responsive">
			<table class="table table-bordered table-striped" id="datatables">
				<thead>
					<tr class="text-center">
						<th style="width:5%;">ID</th>
						<th style="width:45%;">Підрозділ</th>
						<th style="width:40%;">Об`єкт</th>
						<th style="width:10%;">Дія</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($stantions as $item) : ?>
						<tr data-subdivision_id="<?php echo $item->subdivision_id; ?>" data-complete_renovation_object_id="<?php echo $item->id; ?>">
							<td class="text-center"><?php echo $item->id; ?></td>
							<td><?php echo $item->subdivision; ?></td>
							<td class="stantion"><?php echo $item->name; ?></td>
							<td class="text-center">
								<a href="javascript:void(0);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Додати експлуатаційні дані по об`єкту" onclick="openAddOperatingListObjectModal(event)"><i class="bi bi-journal-plus text-success"></i></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal Form Add Operating List -->
<div class="modal fade" id="addOperatingListObjectModal" tabindex="-1" aria-labelledby="addOperatingListObjectModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Додавання експлуатаційних даних</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('complete_renovation_objects/form_operating_list_object');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary action" onclick="addOperatingListObject(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>
