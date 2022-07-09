function format(d) {
	// `d` is the original data object for the row
	return "Більше інформації";
}

$(document).ready(function () {
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
					? "/multi_year_schedule/get_data_server_side"
					: "/multi_year_schedule/get_data",
				type: "POST",
				// data: {
				// 	icon_edit: "icon_edit",
				// 	icon_view: "icon_view,",
				// 	icon_delete: "icon_delete",
				// 	select_checkbox: "select_checkbox",
				// },
			},

			// DataTables - Callbacks
			createdRow: function (row, data, dataIndex, cells) {
				$(row).attr("data-id", data.id);
				$(row).css("cursor", "pointer");
				$(row).addClass("align-middle");
				// typeof createdRow === "function" ? createdRow(row, data, dataIndex, cells) : null;
			},
			headerCallback(thead, data, start, end, display) {
				$(thead).addClass("text-center");
			},
			stateLoadParam: function (settings, data) {},
			initComplete: function (settings, json) {
				// this.api()
				// 	.column(".stantion")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterStantion").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				// this.api()
				// 	.column(".equipment")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterEquipment").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				// this.api()
				// 	.column(".type-service")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterTypeService").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				// this.api()
				// 	.column(".voltage")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterVoltageClass").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				for (i = 0; i < settings.aoColumns.length; i++) {
					if (settings.aoColumns[i].data === "complete_renovation_object_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterStantion").val(value.substring(1, value.length - 1));
						// $("#FilterStantion").val(value);
					}
					if (settings.aoColumns[i].data === "equipment_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterEquipment").val(value.substring(1, value.length - 1));
						// $("#FilterEquipment").val(value);
					}
					if (settings.aoColumns[i].data === "type_service_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterTypeService").val(value.substring(1, value.length - 1));
						// $("#FilterTypeService").val(value);
					}
					if (settings.aoColumns[i].data === "voltage_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterVoltageClass").val(value.substring(1, value.length - 1));
						// $("#FilterVoltageClass").val(value);
					}
				}
			},

			// DataTables - Options
			dom:
				"<'row'<'col-sm-12 col-md-2 my-1 text-left'l><'col-sm-12 col-md-8 my-1 text-center'B><'col-sm-12 col-md-2 my-1 text-right'f>>" +
				"<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				"<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1 text-center'p>>",
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
					className: "id text-center align-middle",
				},
				{
					data: "stantion",
					title: "Підстанція",
					name: "Підстанція",
					orderable: true,
					searchable: true,
					visible: true,
					width: "19%",
					className: "stantion align-middle",
				},
				{
					data: "complete_renovation_object_id",
					title: "Підстанція",
					name: "Підстанція",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "complete_renovation_object_id align-middle",
				},
				{
					data: "equipment",
					title: "Обладнання",
					name: "Обладнання",
					orderable: true,
					searchable: true,
					visible: true,
					width: "17%",
					className: "equipment align-middle",
				},
				// {
				// 	data: "equipment_with_voltage",
				// 	title: "Обладнання",
				// 	name: "Обладнання",
				// 	orderable: true,
				// 	searchable: true,
				// 	visible: true,
				// 	width: "20%",
				// 	className: "equipment-with-voltage align-middle",
				// },
				{
					data: "equipment_id",
					title: "Обладнання",
					name: "Обладнання",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "equipment_id align-middle",
				},
				{
					data: "voltage_id",
					title: "voltage",
					name: "voltage",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "voltage_id align-middle",
				},
				{
					data: "disp",
					title: "Дисп.",
					name: "Дисп.",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "disp text-center align-middle",
				},
				{
					data: "places",
					title: "Інфо",
					name: "Інфо",
					orderable: false,
					searchable: false,
					visible: true,
					width: "15%",
					className: "text-center places align-middle",
					render: function (data, type) {
						let html = "";
						data.forEach(function (value, key, array) {
							html += `<span  class="badge bg-${value.place_color}">${value.place_name} ${value.type} (№ ${value.number})</span><br/>`;
						});
						return html;
					},
				},
				// {
				// 	data: "type",
				// 	title: "Тип",
				// 	name: "Тип",
				// 	orderable: true,
				// 	searchable: true,
				// 	visible: true,
				// 	width: "10%",
				// 	className: "type align-middle",
				// },
				{
					data: "short_type_service",
					title: "Вид ремонту",
					name: "Вид ремонту",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "text-center short_type_service align-middle",
				},
				{
					data: "type_service_id",
					title: "Вид ремонту",
					name: "Вид ремонту",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "type_service_id align-middle",
				},
				{
					data: "cipher",
					title: "Шифр ремонту",
					name: "Шифр ремонту",
					orderable: true,
					searchable: true,
					visible: true,
					width: "9%",
					className: "cipher align-middle",
					render: function (data, type) {
						return `<input type="text" class="form-control form-control-sm text-left" value="${
							data ? data : ""
						}" tabindex="1" maxlength="10" onchange="changeCipher(event);" />`;
					},
				},
				{
					data: "periodicity",
					title: "Період",
					name: "Період",
					orderable: false,
					searchable: true,
					visible: true,
					width: "7%",
					className: "periodicity align-middle",
					render: function (data, type) {
						return `<input type="text" class="form-control form-control-sm text-center" value="${
							data ? data : ""
						}" tabindex="2" maxlength="2" onchange="changePeriodicity(event);" />`;
					},
				},
				{
					data: "year_service",
					title: "Рік",
					name: "Ріку",
					orderable: false,
					searchable: true,
					visible: true,
					width: "8%",
					className: "year_service align-middle",
					render: function (data, type) {
						return `
						<input type="text" class="form-control form-control-sm text-center" value="${
							data ? data : ""
						}" tabindex="3" onchange="changeYearService(event);"/>
					`;
					},
				},
				{
					data: "status",
					title: "Статус",
					name: "Статус",
					orderable: false,
					searchable: true,
					visible: true,
					width: "5%",
					className: "status",
					render: function (data, type) {
						if (data == 0) {
							return `<input class="form-check-input" type="checkbox" tabindex="4" onclick="changeStatus(event);" />`;
						} else {
							return `<input class="form-check-input" type="checkbox" tabindex="4" checked onclick="changeStatus(event);" />`;
						}
					},
					className: "text-center align-middle",
				},
				{
					data: "null",
					title: "Більше",
					name: "Більше",
					orderable: false,
					searchable: false,
					visible: true,
					width: "5%",
					className: "text-center dt-control align-middle",
					defaultContent: "",
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
	$("#datatables tbody").on("click", "td.dt-control", function () {
		var tr = $(this).closest("tr");
		var row = table.row(tr);

		if (row.child.isShown()) {
			// This row is already open - close it
			row.child.hide();
			tr.removeClass("shown");
		} else {
			// Open this row
			row.child(format(row.data())).show();
			tr.addClass("shown");
		}
	});

	table.on("init", function () {
		$(".datepicker").datepicker({
			format: "yyyy",
			autoclose: true,
		});
	});

	$("#FilterStantion").on("change", function () {
		table
			.columns(".complete_renovation_object_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, true)
			.draw();
	});

	$("#FilterEquipment").on("change", function () {
		table
			.columns(".equipment_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();
	});

	$("#FilterTypeService").on("change", function () {
		table
			.columns(".type_service_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();
	});

	// table.columns(".type_service_id").search("^1$", true, false).draw();

	$("#FilterVoltageClass").on("change", function () {
		table
			.columns(".voltage_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();
	});

	$("#ResetFilters").on("click", function () {
		$("#FilterStantion").val("");
		table.columns(".complete_renovation_object_id").search("").draw();
		$("#FilterEquipment").val("");
		table.columns(".equipment_id").search("").draw();
		$("#FilterTypeService").val("");
		table.columns(".type_service_id").search("").draw();
		$("#FilterVoltageClass").val("");
		table.columns(".voltage_id").search("").draw();
	});

	$("#clearLocalStorage").on("click", function () {
		table.state.clear();
		window.location.reload();
	});
});

function changeCipher(event) {
	const id = $(event.target).parents("tr").data("id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_cipher_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changePeriodicity(event) {
	const id = $(event.target).parents("tr").data("id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_periodicity_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeYearService(event) {
	const id = $(event.target).parents("tr").data("id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_year_service_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeStatus(event) {
	const id = $(event.target).parents("tr").data("id");
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_status_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function htmlspecialchars(str) {
	if (typeof str == "string") {
		str = str.replace(/&/g, "&amp;");
		str = str.replace(/"/g, "&quot;");
		str = str.replace(/'/g, "&#039;");
		str = str.replace(/</g, "&lt;");
		str = str.replace(/>/g, "&gt;");
	}
	return str;
}
