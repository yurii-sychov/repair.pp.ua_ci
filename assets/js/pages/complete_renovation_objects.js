$(document).ready(function () {
	$(".datemask").mask("99.99.9999");
});

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

function openAddOperatingListObjectModal(event) {
	const subdivision_id = $(event.currentTarget)
		.closest("tr")
		.data("subdivision_id");
	const complete_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("complete_renovation_object_id");
	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const title = `Додавання експлуатаційних даних по об\`єкту<br /><span class="text-success">${stantion}<span>`;

	$("#idSubdivisionIdAdd").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAdd").val(complete_renovation_object_id);

	const modal = $("#addOperatingListObjectModal");
	modal.find(".modal-title").html(title);
	modal.modal("show");
}

function addOperatingListObject(event) {
	const form = $("#formAddOperatingListObject");
	const places = $('[name="places[]"]');

	$.ajax({
		method: "POST",
		url: "/complete_renovation_objects/add_operating_list_object",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formAddOperatingListObject")
				.find("input")
				.removeClass("is-invalid")
				.addClass("is-valid");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#addOperatingListObjectModal").modal("hide");
				event.target.disabled = false;
				$("#formAddOperatingListObject")
					.find(".row input, .row select")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				location.reload();
			}, 1000);
		} else {
			$("#formAddOperatingListObject input, #formAddOperatingListObject select")
				.removeClass("is-invalid")
				.addClass("is-valid");
			for (let key in data.errors) {
				console.log(data.errors[key]);
				if (key === form[0].elements[key].name) {
					$('[name="' + key + '"]')
						.addClass("is-invalid")
						.next()
						.text(data.errors[key]);
				}
			}
			// console.log(data.errors);
			// console.log(form[0].elements.service_date.name);
			toastr.error(data.message, "Помилка");
		}
	});
}

$("#addOperatingListObjectModal").on("hidden.bs.modal", function (event) {
	$(event.target).find(".row select, .row input").val("");
	$(event.target)
		.find(".row select, .row input")
		.removeClass("is-invalid is-valid")
		.next()
		.text("");
});

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});

let exampleEl = $('[data-bs-toggle="tooltip"]');
if (exampleEl) {
	for (let i = 0; i < exampleEl.length; i++) {
		let tooltip = new bootstrap.Tooltip(exampleEl[i]);
	}
}
