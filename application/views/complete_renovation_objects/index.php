<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>

		<?php if ($count_subdivisions > 1) : ?>
			<form action="/complete_renovation_objects/index" method="GET">
				<div class="row justify-content-start mb-2">
					<div class="col-lg-3 mb-1">
						<select name="subdivision_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
							<option value="/complete_renovation_objects/index">Оберіть підрозділ</option>
							<?php foreach ($subdivisions as $item) : ?>
								<option value="/complete_renovation_objects/index/?subdivision_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('subdivision_id') ? 'selected' : 'NULL'; ?>><?php echo $item->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<!-- <div class="col-lg-4 mb-1 d-grid gap-2 d-lg-block">
					<button type="submit" class="btn btn-primary">Шукати</button>
					<?php if ($this->input->get('stantion')) : ?>
						<a href="/capital_repairs_transformers/index" class="btn btn-success">Скинути</a>
					<?php endif; ?>
				</div> -->
				</div>
			</form>
		<?php endif; ?>

		<?php if ($this->session->user->group === 'admin') : ?>
			<div class="row my-2">
				<div class="col-lg-6">
					<a class="btn btn-primary my-1 disabled" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addOperatingListObjectsModal">Додати енергетичний об`єкт</a>
				</div>
			</div>
		<?php endif; ?>
		<div class="table-responsive">
			<table class="table align-middle" id="collapseParent">
				<thead>
					<tr class="text-center">
						<th style="width:5%;">ID</th>
						<th style="width:30%;">Підрозділ</th>
						<th style="width:30%;">Об`єкт</th>
						<th style="width:10%;">Кількість записів</th>
						<th style="width:15%;">Остання дата</th>
						<th style="width:10%;">Дія</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = $per_page; ?>
					<?php foreach ($stantions as $item) : ?>
						<tr class="parent" data-subdivision_id="<?php echo $item->subdivision_id; ?>" data-complete_renovation_object_id="<?php echo $item->id; ?>">
							<td class="text-center"><?php echo $item->id; ?></td>
							<td><?php echo $item->subdivision; ?></td>
							<td class="stantion"><?php echo $item->name; ?></td>
							<td class="text-center"><?php echo $item->count_rows ? $item->count_rows : '-'; ?></td>
							<td class="text-center"><?php echo $item->create_last_date ? date('d.m.Y H:i', strtotime($item->create_last_date)) : '-'; ?></td>
							<td class="text-center">
								<a href="javascript:void(0);" class="mx-1" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Додати експлуатаційні дані по об`єкту" onclick="openAddOperatingListObjectModal(event)"><i class="bi bi-journal-plus text-success"></i></a>
								<a href="/complete_renovation_objects/gen_operating_list_object_pdf/<?php echo $item->id; ?>" class="mx-1" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Згенерувати експлуатаційні дані по об`єкту" target="_blank"><i class="bi bi-file-pdf-fill text-danger"></i></a>
								<a class="mx-1" data-bs-toggle="collapse" href="#collapse_<?php echo $i; ?>" role="button" aria-expanded="false" aria-controls="collapse_<?php echo $i; ?>" onCLick="typeof(actionCollapse) === 'function' ? actionCollapse(event) : '';"><i class="bi bi-eye text-info" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Більше інформації"></i></a>
							</td>
						</tr>

						<tr class="collaps collapse-horizontal" id="collapse_<?php echo $i; ?>" data-bs-parent="#collapseParent">
							<td colspan="6">
								<?php if (count($item->operating_data)) : ?>
									<table class="table table-bordered table-info align-middle">
										<thead>
											<tr class="text-center">
												<th style="width:5%;">№ п/п</th>
												<th style="width:10%;">Дата</th>
												<th style="width:10%;">№ акта R3</th>
												<th style="width:15%;">Вид обслуговування</th>
												<th style="width:40%;">Перелік робіт</th>
												<th style="width:10%;">Виконавець</th>
												<th style="width:10%;">Дія</th>
											</tr>
										</thead>
										<tbody>
											<?php $y = 1; ?>
											<?php foreach ($item->operating_data as $data) : ?>
												<tr class="form" data-id="<?php echo $data->id; ?>">
													<td class="text-center"><?php echo $y; ?></td>
													<td class="text-center" onclick="editOperatingListObject(event);">
														<input type="text" class="form-control" value="<?php echo date('d.m.Y', strtotime($data->service_date)); ?>" disabled />
													</td>
													<td class="text-center" onclick="editOperatingListObject(event);">
														<input type="text" class="form-control" value="<?php echo $data->act_number; ?>" disabled />
													</td>
													<td class="text-center" onclick="editOperatingListObject(event);">
														<input type="text" class="form-control" value="<?php echo $data->type_service_short_name; ?>" disabled />
													</td>
													<td onclick="editOperatingListObject(event);">
														<input type="text" class="form-control" value="<?php echo $data->service_data; ?>" disabled />
													</td>
													<td onclick="editOperatingListObject(event);">
														<input type="text" class="form-control" value="<?php echo $data->executor; ?>" disabled />
													</td>
													<td class="text-center">
														<a href="javascript:void(0);" class="mx-1" onclick="editOperatingListObject(event);" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Змінити дані"><i class="bi bi-pencil text-success"></i></a>
														<?php if ($data->act_scan) : ?>
															<a href="<?php echo 'uploads/acts/' . $data->act_scan; ?>" class="mx-1" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Подивитись скан акту" target="_blank"><i class="bi bi-image-fill text-danger"></i></a>
														<?php else : ?>
															<a href="javascript:void(0);" class="mx-1"><i class="bi bi-image-fill text-secondary"></i></a>
														<?php endif; ?>
														<a href="javascript:void(0);" class="mx-1" onclick="typeof(reUploadFile) === 'function' ? reUploadFile(event) : '';" data-bs-toggle="tooltip" data-bs-trigger="hover" title="<?php echo $data->act_scan ? 'Замінити файл' : 'Завантажити файл'; ?>"><i class="bi bi-box-arrow-in-down text-warning"></i></a>
														<input type="file" name="act_scan" class="d-none" />
													</td>
												</tr>
												<?php $y++; ?>
											<?php endforeach; ?>
										</tbody>
									</table>

								<?php else : ?>
									<table class="table table-danger align-middle">
										<tr class="text-center">
											<td>Дані відсутні</td>
										</tr>
									</table>
								<?php endif; ?>
							</td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">Показано з <?php echo $per_page; ?> по <?php echo $i - 1; ?> запис з <?php echo isset($total_filter_rows) ? $total_filter_rows : $total_rows; ?> записів</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php echo $this->pagination->create_links(); ?>
	</div>
</div>

<!-- Modal Form Add Operating List -->
<div class="modal fade" id="addOperatingListObjectModal" tabindex="-1" aria-labelledby="addOperatingListObjectModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addOperatingListObjectModalLabel">Додавання експлуатаційних даних</h5>
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
