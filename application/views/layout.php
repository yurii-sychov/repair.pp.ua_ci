<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

?>

<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf_test_name" content="<?php echo $this->security->get_csrf_hash(); ?>">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<!-- <link href="/assets/css/lib/bootstrap/cosmo.min.css" rel="stylesheet"> -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

	<?php if (isset($datatables)) : ?>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.css" />
	<?php endif; ?>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" />
	<link rel="stylesheet" type="text/css" href="/assets/css/custom.css" />

	<?php if ($page === 'capital_repairs_transformers/index' || $page === 'capital_repairs_transformers/sdzp') : ?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" integrity="sha512-ZKX+BvQihRJPA8CROKBhDNvoc2aDMOdAlcm7TUQY+35XYtrd3yh95QOOhsPDQY9QnKE0Wqag9y38OIgEvb88cA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<?php endif; ?>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.5/css/perfect-scrollbar.min.css" integrity="sha512-ygIxOy3hmN2fzGeNqys7ymuBgwSCet0LVfqQbWY10AszPMn2rB9JY0eoG0m1pySicu+nvORrBmhHVSt7+GI9VA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<link rel="icon" href="/assets/images/favicon.png" />



	<title><?php echo $title; ?></title>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top d-print-none">
		<div class="container-fluid">
			<a class="navbar-brand" href="javascript:void(0);">REPAIR.PP.UA</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'multi_year_schedule/index') : ?>active<?php endif; ?>" aria-current="page" href="/multi_year_schedule">Графік</a>
						</li>
					<?php endif; ?>

					<?php if (($this->session->user->group === 'admin' || $this->session->user->group === 'master') && ($this->session->user->group !== 'sp' || $this->session->user->group !== 'sdzp' || $this->session->user->group !== 'head')) : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'complete_renovation_objects/index') : ?>active<?php endif; ?>" aria-current="page" href="/complete_renovation_objects">Енергетичні об`єкти</a>
						</li>
					<?php endif; ?>

					<?php if (($this->session->user->group === 'admin' || $this->session->user->group === 'master') && ($this->session->user->group !== 'sp' || $this->session->user->group !== 'sdzp' || $this->session->user->group !== 'head')) : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'passports/index') : ?>active<?php endif; ?>" aria-current="page" href="/passports">Паспорти</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'sp' || $this->session->user->group === 'sdzp' || $this->session->user->group === 'head') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'capital_repairs_transformers/index') : ?>active<?php endif; ?>" aria-current="page" href="/capital_repairs_transformers">КРСТ</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'sp' || $this->session->user->group === 'sdzp' || $this->session->user->group === 'head') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'capital_repairs_transformers/sdzp') : ?>active<?php endif; ?>" aria-current="page" href="/capital_repairs_transformers/sdzp">КРСТ_1</a>
						</li>
					<?php endif; ?>

					<?php if (($this->session->user->group === 'admin' || $this->session->user->group === 'master') && ($this->session->user->group !== 'sp' || $this->session->user->group !== 'sdzp' || $this->session->user->group !== 'head')) : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'protective_arsenal/index') : ?>active<?php endif; ?>" aria-current="page" href="/protective_arsenal">Захисні засоби</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'logs/index') : ?>active<?php endif; ?>" aria-current="page" href="/logs">Логи</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'users/index') : ?>active<?php endif; ?>" aria-current="page" href="/users">Користувачі</a>
						</li>
					<?php endif; ?>
				</ul>
				<!-- <form class="d-flex"> -->
				<!-- <input class="form-control me-2" type="search" placeholder="Пошук" aria-label="Search" disabled> -->
				<!-- <button class="btn btn-outline-success disabled" type="submit">Пошук</button> -->
				<!-- </form> -->
				<ul class="navbar-nav mb-2 mb-lg-0 d-flex">
					<li class="nav-item dropdown">
						<a class="nav-link" href="javascript:void(0);" id="navbarDropdownBell" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-bell" style="font-size: 24px"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="navbarDropdownBell">
							Bells
						</div>
					</li>
				</ul>
				<?php echo $this->session->user->name . ' ' . $this->session->user->surname; ?>
				<ul class="navbar-nav mb-2 mb-lg-0 d-flex">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-person-circle" style="font-size: 24px"></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
							<li>
								<a class="dropdown-item <?php if ($page === 'profile/index') : ?>active<?php endif; ?>" href="/profile">
									Профіль
								</a>
							</li>
							<li>
								<hr class="dropdown-divider">
							</li>
							<li><a class="dropdown-item" href="/authentication/logout">Вийти</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12 my-2">
				<?php if (isset($title_heading)) : ?>
					<h3><?php echo $title_heading; ?></h3>
				<?php endif; ?>
				<?php $this->load->view($content); ?>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

	<?php if (isset($datatables) && $datatables) : ?>
		<script src="https://cdn.datatables.net/v/bs5/dt-1.11.3/datatables.min.js"></script>
	<?php endif; ?>

	<?php if ($page === 'capital_repairs_transformers/index' || $page === 'capital_repairs_transformers/sdzp') : ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" integrity="sha512-k2GFCTbp9rQU412BStrcD/rlwv1PYec9SNrkbQlo6RZCf75l6KcC3UwDY8H5n5hl4v77IDtIPwOk9Dqjs/mMBQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<?php endif; ?>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" integrity="sha512-d4KkQohk+HswGs6A1d6Gak6Bb9rMWtxjOa0IiY49Q3TeFd5xAzjWXDCBW9RS7m86FQ4RzM2BdHmdJnnKRYknxw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.5/perfect-scrollbar.min.js" integrity="sha512-X41/A5OSxoi5uqtS6Krhqz8QyyD8E/ZbN7B4IaBSgqPLRbWVuXJXr9UwOujstj71SoVxh5vxgy7kmtd17xrJRw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<?php if (isset($page_js) && $page_js) : ?>
		<script src="/assets/js/pages/<?php echo $page_js; ?>.js?v=<?php echo date("Y-m-d"); ?>"></script>
	<?php endif; ?>
</body>

</html>
