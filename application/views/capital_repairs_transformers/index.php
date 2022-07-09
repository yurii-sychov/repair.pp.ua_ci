<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>

		<div class="table-responsive">
			<table class="table" id="collapseParent">
				<thead>
					<tr class="text-center">
						<th style="width: 5%;">№ п/п</th>
						<th style="width: 35%;">Підстанція</th>
						<th style="width: 10%;">Дисп. ім`я</th>
						<th style="width: 20%;">Тип</th>
						<th style="width: 10%;">Зав. №</th>
						<th style="width: 10%;">Рік виготовлення</th>
						<th style="width: 10%;">Дія</th>
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					<?php foreach ($passports as $item) : ?>
						<tr class="text-center parent" data-subdivision_id="<?php echo $item->subdivision_id; ?>" data-complete_renovation_object_id="<?php echo $item->complete_renovation_object_id; ?>" data-specific_renovation_object_id="<?php echo $item->specific_renovation_object_id; ?>" data-place_id="<?php echo $item->place_id; ?>" data-passport_id="<?php echo $item->id; ?>">
							<td><?php echo $i; ?></td>
							<td class="text-start stantion"><?php echo $item->stantion; ?></td>
							<td class="disp"><?php echo $item->disp; ?></td>
							<td class="text-start"><?php echo $item->type; ?></td>
							<td><?php echo $item->number; ?></td>
							<td><?php echo date('Y', strtotime($item->production_date)); ?></td>
							<td>
								<a href="javascript:void(0);" class="mx-1" onCLick="openAddDocumentModal(event)"><i class="bi bi-card-list text-primary" title="Додати документацію" data-bs-toggle="tooltip"></i></a>
								<a href="javascript:void(0);" class="mx-1" onCLick="openAddPhotosModal(event)"><i class="bi bi-card-image text-danger" title="Додати фотографії" data-bs-toggle="tooltip"></i></a>
								<a class="mx-1" data-bs-toggle="collapse" href="#collapse_<?php echo $i; ?>" role="button" aria-expanded="false" aria-controls="collapse_<?php echo $i; ?>"><i class="bi bi-eye text-info" title="Більше інформації" data-bs-toggle="tooltip" onCLick="actionCollapse(event);"></i></a>
							</td>
						</tr>
						<tr class="collapse collapse-horizontal" id="collapse_<?php echo $i; ?>" data-bs-parent="#collapseParent">
							<td colspan="3">
								<table class="table table-striped table-hover table-bordered">
									<thead>
										<tr class="text-center">
											<th style="width: 10%;">№ п/п</th>
											<th style="width: 15%;">Дата</th>
											<th style="width: 65%;" class="text-start">Короткий опис документу</th>
											<th style="width: 10%;">Дія</th>
										</tr>
									</thead>
									<tbody>
										<?php $y = 1; ?>
										<?php foreach ($item->documents as $doc) : ?>
											<tr data-id="<?php echo $doc->id; ?>">
												<td class="text-center"><?php echo $y; ?></td>
												<td class="text-center"><?php echo date('d.m.Y', strtotime($doc->document_date)); ?></td>
												<td><?php echo $doc->document_description; ?></td>
												<td class="text-center">
													<a href="/assets/documents/<?php echo $doc->document_scan; ?>" class="mx-1" target="_blank"><i class="bi bi-file-pdf-fill text-warning" title="Завантажити документ" data-bs-toggle="tooltip"></i></a>
													<a href="javascript:void(0);" class="mx-1" onClick="deleteDocument(event);"><i class="bi bi-trash text-danger" title="Видалити документ" data-bs-toggle="tooltip"></i></a>
												</td>
											</tr>
											<?php $y++; ?>
										<?php endforeach; ?>
									</tbody>
								</table>
							</td>
							<td colspan="4">
								<table class="table table-striped table-hover table-bordered">
									<thead>
										<tr class="text-center">
											<th style="width: 10%;">№ п/п</th>
											<th style="width: 15%;">Дата</th>
											<th style="width: 65%;" class="text-start">Назва фотоальбому</th>
											<th style="width: 10%;">Дія</th>
										</tr>
									</thead>
									<tbody>
										<?php $y = 1; ?>
										<?php foreach ($item->photo_albums as $album_name => $album) : ?>
											<tr>
												<td class="text-center"><?php echo $y; ?></td>
												<td class="text-center"><?php echo date('d.m.Y', strtotime($album['photo_album_date'])); ?></td>
												<td><?php echo $album['photo_album_name']; ?></td>
												<td class="text-center">

													<?php foreach ($item->photos[$album_name] as $k => $photo) : ?>
														<a href="/assets/photos/<?php echo $photo['photo']; ?>" data-lightbox="image_<?php echo $album_name; ?>"">
															<img src=" /assets/photos/thumb/<?php echo $photo['photo']; ?>" alt="Фото" height="15" class="<?php echo $k > 0 ? 'd-none' : NULL; ?>">
														</a>
													<?php endforeach; ?>
												</td>
											</tr>
											<?php $y++; ?>
										<?php endforeach; ?>
									</tbody>
								</table>
							</td>
						</tr>
						<!-- <script>
							var myCollapsible = document.getElementById('collapse_<?php echo $i; ?>');
							myCollapsible.addEventListener('shown.bs.collapse', function() {
								$(this).prev().addClass('bg-secondary');
							});
							myCollapsible.addEventListener('hide.bs.collapse', function() {
								$('tr').removeClass('bg-secondary');
							});
						</script> -->
						<?php $i++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Modal Form Add Docs -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addDocumentModalLabel">Додавання документу</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('capital_repairs_transformers/form_add_document');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary" onClick="addDocument(event)">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Form Add Photos -->
<div class="modal fade" id="addPhotosModal" tabindex="-1" aria-labelledby="addPhotosModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addPhotosModalLabel">Додавання фотографій</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('capital_repairs_transformers/form_add_photos');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary" onClick="addPhotos(event)">Зберегти</button>
			</div>
		</div>
	</div>
</div>
