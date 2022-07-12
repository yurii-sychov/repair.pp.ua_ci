$(document).ready(function () {
	$(".datemask").mask("99.99.9999");
});

function addDocument(event) {
	const form = $("#formAddDocument");
	const modal = $("#addDocumentModal");
	formData = new FormData(form.get(0));
	$.ajax({
		method: "POST",
		url: "/capital_repairs_transformers/add_document",
		data: formData,
		processData: false,
		contentType: false,
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				modal.modal("hide");
				event.target.disabled = false;
				form.find(".row input").removeClass("is-invalid is-valid");
				form[0].reset();
				location.reload();
			}, 1000);
		}
		if (data.status === "ERROR" && data.errors) {
			if (data.errors.document_date) {
				$("#documentDate")
					.addClass("is-invalid")
					.next()
					.text(data.errors.document_date);
			} else {
				$("#documentDate")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.document_description) {
				$("#documentDescription")
					.addClass("is-invalid")
					.next()
					.text(data.errors.document_description);
			} else {
				$("#documentDescription")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
		if (data.status === "ERROR" && data.error) {
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			$("#documentScan").addClass("is-invalid").next().text(data.message);
			toastr.error(data.message, "Помилка");
		}
	});
}

function openAddDocumentModal(event) {
	const subdivision_id = $(event.currentTarget)
		.closest("tr")
		.data("subdivision_id");
	const complete_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("complete_renovation_object_id");
	const specific_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("specific_renovation_object_id");
	const place_id = $(event.currentTarget).closest("tr").data("place_id");
	const passport_id = $(event.currentTarget).closest("tr").data("passport_id");

	$("#idSubdivisionIdAddDocument").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAddDocument").val(
		complete_renovation_object_id
	);
	$("#idSpecificRenovationObjectIdAddDocument").val(
		specific_renovation_object_id
	);
	$("#idPlaceIdAddDocument").val(place_id);
	$("#idPassportIdAddDocument").val(passport_id);

	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const title = `Завантаження документу<br /><span class="text-success">${stantion} (${disp})<span>`;

	const modal = $("#addDocumentModal");
	modal.find(".modal-title").html(title);
	modal.modal("show");
}

function addPhotos(event) {
	const form = $("#formAddPhotos");
	const modal = $("#addPhotosModal");
	formData = new FormData(form.get(0));
	$.ajax({
		method: "POST",
		url: "/capital_repairs_transformers/add_photos",
		data: formData,
		processData: false,
		contentType: false,
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				modal.modal("hide");
				event.target.disabled = false;
				form.find(".row input").removeClass("is-invalid is-valid");
				form[0].reset();
				location.reload();
			}, 1000);
		}
		if (data.status === "ERROR" && data.errors) {
			if (data.errors.photo_album_date) {
				$("#photoAlbumDate")
					.addClass("is-invalid")
					.next()
					.text(data.errors.photo_album_date);
			} else {
				$("#photoAlbumDate")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.photo_album_name) {
				$("#photoAlbumName")
					.addClass("is-invalid")
					.next()
					.text(data.errors.photo_album_name);
			} else {
				$("#photoAlbumName")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
		if (data.status === "ERROR" && data.error) {
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			$("#photos").addClass("is-invalid").next().text(data.message);
			toastr.error(data.message, "Помилка");
		}
	});
}

function openAddPhotosModal(event) {
	const subdivision_id = $(event.currentTarget)
		.closest("tr")
		.data("subdivision_id");
	const complete_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("complete_renovation_object_id");
	const specific_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("specific_renovation_object_id");
	const place_id = $(event.currentTarget).closest("tr").data("place_id");
	const passport_id = $(event.currentTarget).closest("tr").data("passport_id");

	$("#idSubdivisionIdAddPhoto").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAddPhoto").val(complete_renovation_object_id);
	$("#idSpecificRenovationObjectIdAddPhoto").val(specific_renovation_object_id);
	$("#idPlaceIdAddPhoto").val(place_id);
	$("#idPassportIdAddPhoto").val(passport_id);

	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const title = `Завантаження фото<br /><span class="text-success">${stantion} (${disp})<span>`;

	const modal = $("#addPhotosModal");
	modal.find(".modal-title").html(title);
	modal.modal("show");
}

function openOperatingListModal(event) {
	const modal = $("#operatingListModal");
	modal.modal("show");
}

$("#addDocumentModal, #addPhotosModal").on("hidden.bs.modal", function (event) {
	$(event.target).find(".row select, .row input").val("");
	$(event.target)
		.find(".row select, .row input")
		.removeClass("is-invalid is-valid")
		.next()
		.text("");
	$(event.target).find("form input").val("");
});

function deleteDocument(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href =
			"/capital_repairs_transformers/delete_document/" +
			$(event.currentTarget).parents("tr").data("id");
	} else {
		return;
	}
}

function deletePhotoAlbum(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href =
			"/capital_repairs_transformers/delete_photo_album/" +
			$(event.currentTarget).parents("tr").data("id");
	} else {
		return;
	}
}

function actionCollapse(event) {
	$(event.currentTarget).toggleClass(
		"bi-eye-slash text-primary bi-eye text-info"
	);
	const tr_current = $(event.currentTarget).closest("tr");
	const tr_not_current_and_next = $("#collapseParent tbody tr.parent").not(
		tr_current
	);
	tr_current.toggleClass("bg-custom");
	tr_not_current_and_next.toggle(400);
}

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});

var exampleEl = $('[data-bs-toggle="tooltip"]');
if (exampleEl) {
	for (let i = 0; i < exampleEl.length; i++) {
		var tooltip = new bootstrap.Tooltip(exampleEl[i]);
	}
}

lightbox.option({
	wrapAround: true,
	albumLabel: "Зображення %1 з %2",
});
