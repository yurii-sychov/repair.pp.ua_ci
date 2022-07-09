function format(d) {
	// `d` is the original data object for the row
	let html = "";
	let tr_passport_properties = "";
	d.passport_properties.forEach(function (v, k) {
		let icon_trash =
			d.DT_RowData.user_group === "admin"
				? `<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Видалити"><i class="bi bi-trash text-danger" style="font-size: 24px"></i></a>`
				: "";
		tr_passport_properties += `
		<tr class="align-middle" data-id="${v.id}">
			<th>${v.property}</th>
			<td><input class="form-control" value="${v.value}" name="value" disabled /></td>
			<td class="text-center">
				<a href="javascript:void(0);" onclick="editProperty(event);" data-bs-toggle="tooltip" data-bs-placement="top" title="Активувати форму"><i class="bi bi-toggle-off" style="font-size: 24px"></i></a>
				${(icon_trash = "")}
			</td>
		</tr>
		`;
	});

	let tr_operating_list = "";
	d.operating_list.forEach(function (v, k) {
		let icon_trash =
			d.DT_RowData.user_group === "admin"
				? `<a href="javascript:void(0);" onclick="deleteOperatingList(event);" data-bs-toggle="tooltip" data-bs-placement="top" title="Видалити"><i class="bi bi-trash text-danger" style="font-size: 24px"></i></a>`
				: `<a href="javascript:void(0);" onclick="deleteOperatingList(event);" data-bs-toggle="tooltip" data-bs-placement="top" title="Видалити"><i class="bi bi-trash text-danger" style="font-size: 24px"></i></a>`;
		tr_operating_list += `
		<tr class="align-middle" data-id="${v.id}" data-user_group="${d.DT_RowData.user_group}">
			<td><input class="form-control datepicker" value="${v.service_date_format}" name="service_date" disabled /></td>
			<td><input class="form-control" placeholder="Введіть дані з експлуатації" value="${v.service_data}" name="service_data" disabled /></td>
			<td><input class="form-control" placeholder="Введіть виконавця" value="${v.executor}" name="executor" disabled /></td>
			<td class="text-center">
				<a href="javascript:void(0);" onclick="editOperatingList(event);" data-bs-toggle="tooltip" data-bs-placement="top" title="Активувати форму"><i class="bi bi-toggle-off" style="font-size: 24px"></i></a>
				${icon_trash}
			</td>
		</tr>
		`;
	});
	html = `
		<div class="card">
			<div class="card-header"><h5>Більше інформації</h5></div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<h4 class="text-center">Технічні характеристики</h4>
						<table class="table table-bordered table-striped more-info">
							<thead>
								<tr class="text-center">
									<th style="width:65%">Характеристика</th>
									<th style="width:20%">Значення</th>
									<th style="width:15%">Дія</th>
								</tr>
							</thead>
							<tbody>
								${tr_passport_properties}
								<tr class="text-center align-middle d-none">
									<td>
										<select class="form-select" disabled>
											<option value="">Оберіть характеристику</option>
										</select>
									</td>
									<td>
										<input class="form-control" disabled />
									</td>
									<td>
										<a href="javascript:void(0);" onclick="alert('Функція в розробці')">
											<i class="bi bi-plus-square text-success" style="font-size: 24px"></i>
										</a>
									</td>
								</tr>
							</tbody>
						</table>
						<a href="/passports/gen_passport_pdf/${d.id}" class="btn btn-danger my-1 d-none" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Друкувати</a>
						<a class="btn btn-info my-1 d-none" href="/passports/copy_passport_properties/${d.DT_RowData.equipment_id}/${d.DT_RowData.specific_renovation_object_id}"><i class="bi bi-clipboard-plus"></i> Копіювати характеристики</a>
					</div>
					<div class="col-md-8">
						<h4 class="text-center">Експлуатаційні дані</h4>
						<table class="table table-bordered table-striped more-info">
							<thead>
								<tr class="text-center">
									<th style="width:15%">Дата</th>
									<th style="width:55%">Експлуатаційні дані</th>
									<th style="width:15%">Викованець</th>
									<th style="width:15%">Дія</th>
								</tr>
							</thead>
							<tbody>
								${tr_operating_list}				
							</tbody>
						</table>
					</div>
					<hr class="mt-5" />
				</div>
			</div>
		</div>
	`;
	return html;
}

$(document).ready(function () {
	$(".datemask").mask("99.99.9999");
	const serverSide = true;
	const table = $("#datatables")
		.on("processing.dt", function (e, settings, processing) {
			$(".loading").css("display", processing ? "block" : "none");
		})
		.DataTable({
			// DataTables - Features
			// processing: true,
			autoWidth: false,
			stateSave: true,
			deferRender: true,
			pagingType: "full_numbers",
			serverSide: serverSide,

			// DataTables - Data
			ajax: {
				url: serverSide
					? "/passports/get_data_server_side"
					: "/passports/get_data",
				type: "POST",
				// data: serverSide ? null : { post: 1 },
			},

			// DataTables - Callbacks
			createdRow: function (row, data, dataIndex, cells) {
				$(row).attr("data-id", data.id);
				$(row).attr("data-subdivision_id", data.subdivision_id);
				$(row).attr(
					"data-complete_renovation_object_id",
					data.complete_renovation_object_id
				);
				$(row).attr(
					"data-specific_renovation_object_id",
					data.specific_renovation_object_id
				);
				$(row).attr("data-place_id", data.place_id);
				$(row).attr("data-equipment_id", data.equipment_id);

				$(row).css("cursor", "pointer");
				$(row).addClass("align-middle");
			},
			drawCallback: function (settings) {
				if (
					typeof settings.json !== "undefined" &&
					settings.json.user_group !== "admin"
				) {
					$("td.actions").find("i.bi-trash").closest("a").remove();
				}
				// $(settings.aanFeatures.l).find("select").removeClass("form-select-sm");
				// $(settings.aanFeatures.f).find("input").removeClass("form-control-sm");

				$.extend($.fn.dataTableExt.oStdClasses, {
					sFilterInput: "form-control yourClass",
					sLengthSelect: "form-control yourClass",
				});
			},
			headerCallback(thead, data, start, end, display) {
				$(thead).addClass("text-center align-middle");
			},
			stateLoadParam: function (settings, data) {},
			initComplete: function (settings, json) {
				for (i = 0; i < settings.aoColumns.length; i++) {
					if (settings.aoColumns[i].data === "complete_renovation_object_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterStantion").val(value.substring(1, value.length - 1));
					}
					if (settings.aoColumns[i].data === "equipment_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterEquipment").val(value.substring(1, value.length - 1));
					}
					if (settings.aoColumns[i].data === "voltage_class_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterVoltageClass").val(value.substring(1, value.length - 1));
					}
				}
			},

			// DataTables - Options
			dom: serverSide
				? "<'row'<'col-sm-12 col-md-4 my-1'l><'col-sm-12 col-md-4 my-1'B><'col-sm-12 col-md-4 my-1'f>>" +
				  "<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				  "<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1'p>>"
				: "<'row'<'col-sm-12 col-md-4 my-1'l><'col-sm-12 col-md-4 my-1'B><'col-sm-12 col-md-4 my-1'f>>" +
				  "<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				  "<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1'p>>",
			lengthMenu: [
				[5, 10, 25, 50, 100],
				[
					"Показати 5 записів",
					"Показати 10 записів",
					"Показати 25 записів",
					"Показати 50 записів",
					"Показати 100 записів",
					// "Показати всі записи",
				],
			],

			// DataTables - Columns
			columns: [
				{
					data: "id",
					title: "ID",
					name: "ID",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "id text-center",
				},
				{
					data: "stantion",
					title: "Підстанція",
					name: "Підстанція",
					orderable: true,
					searchable: true,
					visible: true,
					width: "15%",
					className: "stantion",
				},
				{
					data: "equipment",
					title: "Обладнання",
					name: "Обладнання",
					orderable: true,
					searchable: true,
					visible: true,
					width: "17%",
					className: "equipment",
				},
				{
					data: "disp",
					title: "Дисп.",
					name: "Дисп.",
					orderable: true,
					searchable: true,
					visible: true,
					width: "8%",
					className: "disp",
				},
				{
					data: "place",
					title: "Місце",
					name: "Місце",
					orderable: true,
					searchable: true,
					visible: true,
					width: "9%",
					className: "place",
				},
				{
					data: "type",
					title: "Тип",
					name: "Тип",
					orderable: true,
					searchable: true,
					visible: true,
					width: "14%",
					className: "type",
				},
				{
					data: "number",
					title: "Номер",
					name: "Номер",
					orderable: true,
					searchable: true,
					visible: true,
					width: "10%",
					className: "number text-center",
				},
				{
					data: "production_date",
					title: '<i class="bi bi-calendar"></i>',
					name: "Дата випуску",
					orderable: true,
					searchable: true,
					visible: true,
					width: "7%",
					className: "production_date text-center",
					render: function (data, type) {
						var options = { year: "numeric", month: "numeric", day: "numeric" };
						return data !== "0000-00-00"
							? new Date(data).toLocaleString("ru", options)
							: "NO DATA";
					},
				},
				{
					data: null,
					title: "Дія",
					name: "actions",
					orderable: false,
					searchable: false,
					visible: true,
					width: "12%",
					className: "text-center actions",
					render: function (data, type) {
						return `
						<a href="javascript:void(0);" onclick="fillAddPropertiesModal(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Додати властивості"><i class="bi bi-plus-square text-primary"></i></a>
						<a href="javascript:void(0);" onclick="getDataPassport(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Редагувати паспорт"><i class="bi bi-pencil-square text-success"></i></a>
						<a href="javascript:void(0);" onclick="movePassport(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Перемістити паспорт"><i class="bi bi-arrow-left-right text-warning"></i></a>
						<a href="javascript:void(0);" onclick="printPassport(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Сгенерувати паспорт"><i class="bi bi-file-earmark-pdf text-danger"></i></a>
						<a href="javascript:void(0);" class="mx-1 dt-control" data-bs-toggle="tooltip" data-bs-placement="top" title="Більше інформації"><i class="bi bi-eye text-info"></i></a>
						<a href="javascript:void(0);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Додати експлуатаційні дані" onclick="openAddOperatingListModal(event)"><i class="bi bi-journal-plus text-success"></i></a>
						<a href="javascript:void(0);" onclick="deletePassport(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Видалити"><i class="bi bi-trash text-danger"></i></a>
					`;
					},
				},
				// {
				// 	data: "null",
				// 	title: "Більше",
				// 	name: "Більше",
				// 	orderable: false,
				// 	searchable: false,
				// 	visible: true,
				// 	width: "5%",
				// 	className: "text-center dt-control",
				// 	defaultContent: "",
				// },
				{
					data: "complete_renovation_object_id",
					title: "complete_renovation_object_id",
					name: "complete_renovation_object_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "complete_renovation_object_id",
				},
				{
					data: "specific_renovation_object_id",
					title: "specific_renovation_object_id",
					name: "specific_renovation_object_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "specific_renovation_object_id",
				},
				{
					data: "place_id",
					title: "place_id",
					name: "place_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "place_id",
				},
				{
					data: "equipment_id",
					title: "equipment_id",
					name: "equipment_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "equipment_id",
				},
				{
					data: "voltage_class_id",
					title: "voltage_class_id",
					name: "voltage_class_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "voltage_class_id",
				},
			],

			// DataTables - Internationalisation
			language: {
				infoFiltered: "(відфільтровано з _MAX_ рядків)",
				paginate: {
					first: "«",
					previous: "‹",
					next: "›",
					last: "»",
				},
				info: "Показано з _START_ до _END_ запису з _TOTAL_",
				search: "_INPUT_",
				searchPlaceholder: "Пошук...",
				lengthMenu: "_MENU_ ",
				zeroRecords: "Немає записів для відображення",
				emptyTable: "Дані відсутні в таблиці",
				processing: "Чекайте!",
			},
		});

	// Add event listener for opening and closing details
	$("#datatables tbody").on("click", "td a.dt-control", function () {
		// var exampleEl = $('.more-info [data-bs-toggle="tooltip"]');

		// var tooltip = new bootstrap.Tooltip(exampleEl);
		var tr = $(this).closest("tr");
		var row = table.row(tr);

		$(this).find("i").toggleClass("bi-eye-slash text-primary bi-eye text-info");
		tr.toggleClass("bg-custom");

		if (row.child.isShown()) {
			// This row is already open - close it
			row.child.hide();
			table.ajax.reload(null, false);
		} else {
			// Open this row
			let html = format(row.data());
			row.child(html).show();
			$(".datepicker").datepicker({
				format: "dd.mm.yyyy",
				autoclose: true,
			});
			// var exampleEl = $(html).find('.more-info [data-bs-toggle="tooltip"]');
			// console.log(exampleEl);
			// var tooltip = new bootstrap.Tooltip(exampleEl, {});
			// console.log(tooltip);
			// tr.addClass("shown");
		}
	});

	$("#FilterStantion").on("change", function () {
		table
			.columns(".complete_renovation_object_id")
			.search(this.value ? "^" + this.value + "$" : "", true, true)
			.draw();
	});

	$("#FilterEquipment").on("change", function () {
		table
			.columns(".equipment_id")
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();
	});

	$("#FilterVoltageClass").on("change", function () {
		table
			.columns(".voltage_class_id")
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();
	});

	$("#clearLocalStorage").on("click", function () {
		table.state.clear();
		window.location.reload();
	});
});

function addPassport(event) {
	const form = $("#formAddPassport");
	$.ajax({
		method: "POST",
		url: "/passports/add_passport",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formAddPassport")
				.find(".row select, .row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#addPassportModal").modal("hide");
				event.target.disabled = false;
				$("#formAddPassport")
					.find(".row select, .row input")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
				table.order([0, "desc"]).draw();
			}, 1000);
		} else {
			if (data.errors.complete_renovation_object_id) {
				$("#idStantionAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.complete_renovation_object_id);
			} else {
				$("#idStantionAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.equipment_id) {
				$("#idEquipmentAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.equipment_id);
			} else {
				$("#idEquipmentAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.insulation_type_id) {
				$("#idInsulationTypeAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.insulation_type_id);
			} else {
				$("#idInsulationTypeAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.place_id) {
				$("#idPlaceAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.place_id);
			} else {
				$("#idPlaceAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.voltage_class_id) {
				$("#idVoltageClassAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.voltage_class_id);
			} else {
				$("#idVoltageClassAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.disp) {
				$("#idDispAdd").addClass("is-invalid").next().text(data.errors.disp);
			} else {
				$("#idDispAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.type) {
				$("#idTypeAdd").addClass("is-invalid").next().text(data.errors.type);
			} else {
				$("#idTypeAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.production_date) {
				$("#idProductionDateAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.production_date);
			} else {
				$("#idProductionDateAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.number) {
				$("#idNumberAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.number);
			} else {
				$("#idNumberAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
	});
}

function editPassport(event) {
	const form = $("#formEditPassport");
	$.ajax({
		method: "POST",
		url: "/passports/edit_passport",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formEditPassport")
				.find(".row select, .row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#editPassportModal").modal("hide");
				event.target.disabled = false;
				$("#formEditPassport")
					.find(".row select, .row input")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
			}, 1000);
		} else {
			// if (data.errors.complete_renovation_object_id) {
			// 	$("#idStantionEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.complete_renovation_object_id);
			// } else {
			// 	$("#idStantionEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			// if (data.errors.equipment_id) {
			// 	$("#idEquipmentEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.equipment_id);
			// } else {
			// 	$("#idEquipmentEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			if (data.errors.insulation_type_id) {
				$("#idInsulationTypeEdit")
					.addClass("is-invalid")
					.next()
					.text(data.errors.insulation_type_id);
			} else {
				$("#idInsulationTypeEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			// if (data.errors.place_id) {
			// 	$("#idPlaceEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.place_id);
			// } else {
			// 	$("#idPlaceEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			// if (data.errors.voltage_class_id) {
			// 	$("#idVoltageClassEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.voltage_class_id);
			// } else {
			// 	$("#idVoltageClassEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			// if (data.errors.disp) {
			// 	$("#idDispEdit").addClass("is-invalid").next().text(data.errors.disp);
			// } else {
			// 	$("#idDispEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			if (data.errors.type) {
				$("#idTypeEdit").addClass("is-invalid").next().text(data.errors.type);
			} else {
				$("#idTypeEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.production_date) {
				$("#idProductionDateEdit")
					.addClass("is-invalid")
					.next()
					.text(data.errors.production_date);
			} else {
				$("#idProductionDateEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.number) {
				$("#idNumberEdit")
					.addClass("is-invalid")
					.next()
					.text(data.errors.number);
			} else {
				$("#idNumberEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
	});
}

$("#addPassportModal, #editPassportModal, #addOperatingListModal").on(
	"hidden.bs.modal",
	function (event) {
		$(event.target).find(".row select, .row input").val("");
		$(event.target)
			.find(".row select, .row input")
			.removeClass("is-invalid is-valid")
			.next()
			.text("");
	}
);

function getDataPassport(event) {
	let id = $(event.currentTarget).closest("tr").data("id");
	$.ajax({
		method: "POST",
		url: "/passports/get_data_passport/",
		data: { id },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "OK");
			fillFormPassport(data.passport, data.disp);
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
	let modal = $("#editPassportModal");
	modal.modal("show");
}

function fillFormPassport(passport, disp) {
	$("#idEdit").val(passport.id);
	$("#idStantionEdit").val(passport.complete_renovation_object_id);
	$("#idEquipmentEdit").val(disp.equipment_id);
	$("#idInsulationTypeEdit").val(passport.insulation_type_id);
	$("#idPlaceEdit").val(passport.place_id);
	$("#idVoltageClassEdit").val(disp.voltage_class_id);
	$("#idDispEdit").val(disp.name);
	$("#idTypeEdit").val(passport.type);
	$("#idProductionDateEdit").val(passport.production_date).datepicker("update");
	$("#idNumberEdit").val(passport.number);
	$("#idRefinementMethodEdit").val(passport.refinement_method);
}

function movePassport(event) {
	alert("movePassport");
}

function printPassport(event) {
	const id = $(event.currentTarget).closest("tr").data("id");
	window.open("/passports/gen_passport_pdf/" + id, "_blank");
}

function addProperties(event) {
	const form = $("#formAddProperties");
	$.ajax({
		method: "POST",
		url: "/passports/add_properties",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			setTimeout(() => {
				$("#addPropertiesModal").modal("hide");
				event.target.disabled = false;
				$("#formAddProperties").find("tbody").html("");
				toastr.success(data.message, "Успіх");
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
			}, 1000);
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editProperty(event) {
	$(event.currentTarget).find("i").toggleClass("bi-toggle-on");
	$(event.currentTarget)
		.closest("tr")
		.find("input")
		.attr("disabled", function (index, attr) {
			if (typeof attr === "undefined" && index == 0) {
				let id = $(event.currentTarget).closest("tr").data("id");
				let value = $(event.currentTarget)
					.closest("tr")
					.find('[name="value"]')
					.val();
				$.ajax({
					method: "POST",
					url: "/passports/edit_property",
					data: { id, value },
				}).done(function (data) {
					if (data.status === "SUCCESS") {
						toastr.success(data.message, "Успіх");
					} else {
						toastr.error(data.message, "Помилка");
					}
				});
			}
			return attr == "disabled" ? null : "disabled";
		});
}

$("#addPropertiesModal").on("hidden.bs.modal", function (event) {
	$("#formAddProperties").find("tbody").html("");
});

function fillAddPropertiesModal(event) {
	const equipment_id = $(event.currentTarget)
		.closest("tr")
		.data("equipment_id");
	const passport_id = $(event.currentTarget).closest("tr").data("id");

	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const place = $(event.currentTarget).closest("tr").find(".place").text();
	const title = `Додавання характеристик обладнання<br /><span class="text-primary">${stantion} (${disp} ${place})<span>`;

	$.ajax({
		method: "POST",
		url: "/passports/get_properties",
		data: { passport_id, equipment_id },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			let html = "";
			data.properties.forEach(function (val, key, arr) {
				html += `
					<tr class="align-middle">
						<th>
							${val.name}
							<input type="hidden" name="passport_id[]" value="${passport_id}" />
							<input type="hidden" name="property_id[]" value="${val.id}" />
						</th>
						<td><input class="form-control" type="text" name="value[]" /></td>
					</tr>
				`;
			});
			$("#formAddProperties").find("tbody").append(html);
			const modal = $("#addPropertiesModal");
			modal.find(".modal-title").html(title);
			modal.modal("show");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function openAddOperatingListModal(event) {
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
	const passport_id = $(event.currentTarget).closest("tr").data("id");

	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const place = $(event.currentTarget).closest("tr").find(".place").text();
	const title = `Додавання експлуатаційних даних<br /><span class="text-success">${stantion} (${disp} ${place})<span>`;

	$("#idSubdivisionIdAdd").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAdd").val(complete_renovation_object_id);
	$("#idSpecificRenovationObjectIdAdd").val(specific_renovation_object_id);
	$("#idPlaceIdAdd").val(place_id);
	$("#idPassportIdAdd").val(passport_id);
	// $("#idServiceDateAdd").val("").datepicker("update");

	$(".places").html("");
	$.ajax({
		method: "POST",
		url: "/passports/get_places",
		data: { specific_renovation_object_id, place_id },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			let html = "";
			data.places.forEach(function (v, k) {
				html += `<div class="form-check">
								<input class="form-check-input" data-passport_id="${v.id}" type="checkbox" value="${v.place_id}" id="check_${v.place_id}" name="places[]">
								<label class="form-check-label" for="check_${v.place_id}">
									Копіювати на ${v.place}
								</label>
								<input type="hidden" name="passports[${v.place_id}]" value="${v.id}" />
							</div>`;
			});
			$("#formAddOperatingList .places").append(html);
		} else {
		}
	});

	const modal = $("#addOperatingListModal");
	modal.find(".modal-title").html(title);
	modal.modal("show");
}

function addOperatingList(event) {
	const form = $("#formAddOperatingList");
	const places = $('[name="places[]"]');
	console.log(form);
	console.log(form.serialize());
	form[0].places = places;

	$.ajax({
		method: "POST",
		url: "/passports/add_operating_list",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formAddOperatingList")
				.find("input")
				.removeClass("is-invalid")
				.addClass("is-valid");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#addOperatingListModal").modal("hide");
				event.target.disabled = false;
				$("#formAddOperatingList")
					.find(".row input")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
			}, 1000);
		} else {
			$("#formAddOperatingList input")
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
			console.log(data.errors);
			console.log(form[0].elements.service_date.name);
			toastr.error(data.message, "Помилка");
		}
	});
}

function editOperatingList(event) {
	$(event.currentTarget).find("i").toggleClass("bi-toggle-on");
	$(event.currentTarget)
		.closest("tr")
		.find("input")
		.attr("disabled", function (index, attr) {
			if (typeof attr === "undefined" && index == 0) {
				let id = $(event.currentTarget).closest("tr").data("id");
				let service_date = $(event.currentTarget)
					.closest("tr")
					.find('[name="service_date"]')
					.val();
				let service_data = $(event.currentTarget)
					.closest("tr")
					.find('[name="service_data"]')
					.val();
				let executor = $(event.currentTarget)
					.closest("tr")
					.find('[name="executor"]')
					.val();
				$.ajax({
					method: "POST",
					url: "/passports/edit_operating_list",
					data: { id, service_date, service_data, executor },
				}).done(function (data) {
					if (data.status === "SUCCESS") {
						toastr.success(data.message, "Успіх");
					} else {
						toastr.error(data.message, "Помилка");
					}
				});
			}
			return attr == "disabled" ? null : "disabled";
		});
}

function deletePassport(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href =
			"/passports/delete_passport/" +
			$(event.currentTarget).parents("tr").data("id") +
			"/" +
			$(event.currentTarget)
				.parents("tr")
				.data("specific_renovation_object_id");
	} else {
		return;
	}
}

function deleteOperatingList(event) {
	let result = confirm("Ви впевнені?");

	if (result) {
		if (
			$(event.currentTarget).closest("tr").data("user_group") !== "admin" &&
			$(event.currentTarget).closest("tr").data("user_group") !== "master"
		) {
			toastr.error("Ви не меєте прав видаляти ці дані!", "Помилка");
		} else {
			let id = $(event.currentTarget).closest("tr").data("id");
			let tr = $(event.currentTarget).closest("tr");
			$.ajax({
				method: "POST",
				url: "/passports/delete_operating_list",
				data: { id },
			}).done(function (data) {
				if (data.status === "SUCCESS") {
					$(tr).remove();
					toastr.success(data.message, "Успіх");
				} else {
					toastr.error(data.message, "Помилка");
				}
			});
		}
	}
}

function getDataForCopyProperties() {}

function copyProperties(event) {
	alert("Error");
}

// function getSpecificRenovationObjects(event) {
// 	let disp = event.target.value;
// 	let equipment_id = $("#formCopyProperties").find(".equipment").val();

// 	$.ajax({
// 		method: "POST",
// 		url: "/passports/get_specific_renovation_objects",
// 		data: { disp, equipment_id },
// 	}).done(function (data) {
// 		if (data.status === "SUCCESS" && data.results.length) {
// 			let html = "";
// 			data.results.forEach(function (value) {
// 				let li = `<li>${value.equipment} ${value.name}</li>`;
// 				html += li;
// 			});
// 			$("#formCopyProperties").find(".list").append(html);
// 		} else {
// 			$("#formCopyProperties").find(".list").html("");
// 		}
// 	});
// }

// function onChangeEqupment(event) {
// 	$("#formCopyProperties").find(".disp").val("");
// 	$("#formCopyProperties").find(".list").html("");
// 	if (event.target.value !== "") {
// 		$("#formCopyProperties").find(".disp").removeAttr("disabled");
// 	} else {
// 		$("#formCopyProperties").find(".disp").attr("disabled", "disabled");
// 	}
// }

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});

var exampleEl = $('[data-bs-toggle="tooltip"]');
if (tooltip) {
	var tooltip = new bootstrap.Tooltip(exampleEl);
}
