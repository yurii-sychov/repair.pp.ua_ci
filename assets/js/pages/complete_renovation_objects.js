$(document).ready(function () {
	$(".datemask").mask("99.99.9999");
});

// function actionCollapse(event) {
// 	$(event.currentTarget)
// 		.find("i")
// 		.toggleClass("bi-eye-slash text-primary bi-eye text-info");
// 	const tr_current = $(event.currentTarget).closest("tr");
// 	const tr_not_current_and_next = $("#collapseParent tbody tr.parent").not(
// 		tr_current
// 	);
// 	tr_current.toggleClass("bg-custom");
// 	tr_not_current_and_next.toggle();
// }

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
	formData = new FormData(form.get(0));
	$.ajax({
		method: "POST",
		url: "/complete_renovation_objects/add_operating_list_object",
		data: formData,
		processData: false,
		contentType: false,
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

function editOperatingListObject(event) {
	$(event.currentTarget)
		.find("i")
		.toggleClass("bi-check2 text-danger bi-pencil text-success");
	if (
		$(event.currentTarget).closest("tr").find("input, select").attr("disabled")
	) {
		$(event.currentTarget)
			.closest("tr")
			.find("input, select")
			.removeAttr("disabled");
	} else {
		$(event.currentTarget)
			.closest("tr")
			.find("input, select")
			.attr("disabled", "deabled");
	}
}

function reUploadFile(event) {
	const id = $(event.currentTarget).closest("tr").data("id");
	const input = $(event.currentTarget).next();
	input.off();
	input.click();
	input.change((e) => {
		const file = e.target.files[0];
		const formData = new FormData();
		formData.append("id", id);
		formData.append("act_scan", file);
		$.ajax({
			method: "POST",
			url: "/complete_renovation_objects/edit_operating_list_object_act_scan",
			data: formData,
			processData: false,
			contentType: false,
		}).done(function (data) {
			if (data.status === "SUCCESS") {
				toastr.success(data.message, "Успіх");
				setTimeout(() => {
					location.reload();
				}, 1000);
			} else {
				toastr.error(data.message, "Помилка");
			}
		});
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
// console.log($('[data-bs-toggle="tooltip"]')).tooltip({
// 	trigger: "hover",
// });
