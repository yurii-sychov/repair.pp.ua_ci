<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>
		<?php if ($this->session->flashdata('message')) : ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?php echo $this->session->flashdata('message');  ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>
		<div class="row my-2">
			<div class="col-lg-4 text-center">
				<img src="<?php echo $user->gender == 1 ? '/assets/images/avatar_male.webp' :  '/assets/images/avatar_female.webp'; ?>" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
				<h5 class="my-3"><?php echo $user->name . " " . $user->surname; ?></h5>
				<p class="text-muted mb-1"><?php echo $user->position; ?></p>
				<p class="text-muted mb-4">Україна</p>
				<div class="mb-2 d-grid gap-2 d-md-block">
					<a href="/profile/update/<?php echo $user->id; ?>" class="btn btn-primary">Редагувати</a>
					<a href="/profile/send_message" class="btn btn-outline-success ms-1">Повідомлення</a>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-sm-3">
						<p class="mb-0">Прізвище ім'я по батькові </p>
					</div>
					<div class="col-sm-9">
						<p class="text-muted mb-0"><?php echo $user->surname . ' ' . $user->name . ' ' . $user->patronymic; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<p class="mb-0">Підрозділ</p>
					</div>
					<div class="col-sm-9">
						<p class="text-muted mb-0">
							<?php foreach ($subdivisions as $item) : ?>
								<?php if ($item->id == $user->subdivision_id) : ?>
									<?php echo $item->name; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						</p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<p class="mb-0">Стать</p>
					</div>
					<div class="col-sm-9">
						<p class="text-muted mb-0"><?php echo ($user->gender == 1) ? 'Чоловік' : 'Жінка'; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<p class="mb-0">Email</p>
					</div>
					<div class="col-sm-9">
						<p class="text-muted mb-0"><?php echo $user->email; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<p class="mb-0">Телефон</p>
					</div>
					<div class="col-sm-9">
						<p class="text-muted mb-0"><?php echo $user->phone; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<p class="mb-0">Мобільний</p>
					</div>
					<div class="col-sm-9">
						<p class="text-muted mb-0"><?php echo $user->phone_mobile; ?></p>
					</div>
				</div>
				<hr>
				<!-- <div class="row">
					<div class="col-sm-3">
						<p class="mb-0">Адреса</p>
					</div>
					<div class="col-sm-9">
						<p class="text-muted mb-0">Україна</p>
					</div>
				</div> -->
			</div>
		</div>
	</div>
</div>
