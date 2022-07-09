<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

// SELECT count(passports.id) as count, surname FROM `passports`, users_complete_renovation_objects, users WHERE passports.complete_renovation_object_id = users_complete_renovation_objects.object_id AND users_complete_renovation_objects.user_id = users.id GROUP BY user_id ORDER BY count DESC;

defined('BASEPATH') or exit('No direct script access allowed');


class MYPDF extends TCPDF
{
	public function Header()
	{
		if ($this->page === 1) {
			$style = array(
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(47, 79, 79),
				'bgcolor' => false
			);
			$image_file = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/logo.png';
			$this->Image($image_file, 16, 10, 20, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			$this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'QRCODE,H', 375, 10, 29, 29, $style, 'N');
			// $this->write2DBarcode('Документ сгенеровано ' . date('d-m-Y року о H:i:s'), 'PDF417', 231, 10, 50, 50, $style, 'N');
			$this->SetFont('dejavusans', 'B', 20, '', true);
			// $this->Cell(0, 15, 'ПрАТ "Кіровоградобленерго"', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}
	}
}

class Passports extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'master') {
			show_404();
		}
		$this->load->model('passport_model');
		$this->load->model('operating_list_model');
		$this->load->model('schedule_model');
		// $this->load->model('subdivision_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('specific_renovation_object_model');
		$this->load->model('equipment_model');
		$this->load->model('voltage_class_model');
		$this->load->model('insulation_type_model');
		$this->load->model('place_model');
		$this->load->model('property_model');
		$this->load->model('passport_property_model');
		$this->load->model('log_model');
	}

	public function index()
	{
		$this->load->library('form_validation');
		$data = [];
		$data['title'] = 'Паспорти обладнання';
		$data['content'] = 'passports/index';
		$data['page'] = 'passports/index';
		$data['page_js'] = 'passports';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Паспорти обладнання';
		$data['title_heading_card'] = 'Паспорти обладнання';
		// $data['subdivisions'] = $this->subdivision_model->get_data();
		$data['stantions'] = $this->complete_renovation_object_model->get_data_for_user();
		$data['equipments'] = $this->equipment_model->get_data();
		$data['voltage_class'] = $this->voltage_class_model->get_data();
		$data['insulation_type'] = $this->insulation_type_model->get_data();
		$data['places'] = $this->place_model->get_data();
		$this->load->view('layout', $data);
	}

	public function copy_passport_properties($equipment_id, $specific_renovation_object_id)
	{
		// if ($this->session->user->id != 10 && $this->session->user->id != 1) {
		redirect('/passports');
		// }

		$data = [];
		$data['title'] = 'Копіювання характеристик обладнання';
		$data['content'] = 'passports/copy_passport_properties';
		$data['page'] = 'passports/copy_passport_properties';
		$data['page_js'] = 'passports';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Копіювання характеристик обладнання';
		$data['title_heading_card'] = 'Форма для копіювання характеристик обладнання';
		$donor = $this->specific_renovation_object_model->get_specific_renovation_object_full($specific_renovation_object_id);
		if (isset($donor)) {
			$data['donor'] = $donor;
			$data['patients'] = $this->specific_renovation_object_model->get_specific_renovation_object_for_copy($equipment_id, $donor->type, $specific_renovation_object_id, $donor->complete_renovation_object_id);
			$data['passport_properties'] = $this->passport_property_model->get_data_for_passport($donor->donor_passport_id);
		}
		$this->load->view('layout', $data);
	}

	public function copy_passport_properties_insert()
	{
		if (!$this->input->post('passport_id')) {
			$this->session->set_flashdata('message', 'Потрібно вибрати обладнання.');
			redirect('/passports/copy_passport_properties/' . $this->input->post('donor_equipment_id') . '/' . $this->input->post('donor_specific_renovation_object_id'));
		}

		$passport_properties = $this->passport_property_model->get_data_for_passport($this->input->post('donor_passport_id'));
		echo "<pre>";
		print_r($this->input->post());
		print_r($passport_properties);
		echo "</pre>";
		exit;
		foreach ($this->input->post('passport_id') as $key => $value) {
			$data = $this->set_properties_copy_data($passport_properties, $value);

			$is_delete = $this->passport_property_model->delete_row($value);

			foreach ($data as $key => $value) {

				$result = $this->passport_property_model->add_data_row($value);
				if ($result) {
					$data_after = $value;
					$log_data = $this->set_log_data("Копіювання паспортних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($data_after, JSON_UNESCAPED_UNICODE), 2);
					$this->log_model->insert_data($log_data);
				}
			}
		}
		redirect('/passports');
	}

	// public function get_data()
	// {
	// 	$this->output->set_content_type('application/json');
	// 	if (!$this->input->post()) {
	// 		$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
	// 		return;
	// 	}

	// 	$passports = $this->passport_model->get_data_datatables();

	// 	$passport_properties = $this->passport_property_model->get_all_passport_properties();

	// 	$properties = $this->property_model->get_all_properties();

	// 	$operating_list = $this->operating_list_model->get_all_operating_list();

	// 	foreach ($passports as $key => $value) {
	// 		$value->passport_properties = [];
	// 		foreach ($passport_properties as $k => $v) {
	// 			if ($value->id === $v->passport_id) {
	// 				array_push($value->passport_properties, $v);
	// 			}
	// 		}

	// 		$value->properties = [];
	// 		foreach ($properties as $k => $v) {
	// 			if ($value->equipment_id === $v->equipment_id) {
	// 				array_push($value->properties, $v);
	// 			}
	// 		}

	// 		$value->operating_list = [];
	// 		foreach ($operating_list as $k => $v) {
	// 			if ($value->id === $v->passport_id) {
	// 				array_push($value->operating_list, $v);
	// 			}
	// 		}
	// 	}



	// 	$data['data'] = $passports;
	// 	$data['user_group'] = $this->session->user->group;
	// 	$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	// }

	public function get_data_server_side()
	{
		$this->output->set_content_type('application/json');
		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		foreach ($this->input->post('columns') as $key => $value) {
			$filter[$value['data']] = $value['search']['value'];
			if ($key == $this->input->post('order')[0]['column']) {
				$order_dir = $this->input->post('order')[0]['dir'];
				$order_field = $this->input->post('columns')[$key]['data'];
			}
		}

		$passports = $this->passport_model->get_data_datatables_server_side($this->input->post(), $filter, $order_dir, $order_field);

		// $passport_properties = $this->passport_property_model->get_all_passport_properties();

		// $properties = $this->property_model->get_all_properties();

		// $operating_list = $this->operating_list_model->get_all_operating_list();

		foreach ($passports as $passport) {
			$passport->passport_properties = [];
			// foreach ($passport_properties as $k => $v) {
			// 	if ($value->id === $v->passport_id) {
			// 		array_push($value->passport_properties, $v);
			// 	}
			// }
			$passport->passport_properties = $this->passport_property_model->get_data_for_passport($passport->id);

			$passport->properties = [];
			// foreach ($properties as $k => $v) {
			// 	if ($value->equipment_id === $v->equipment_id) {
			// 		array_push($value->properties, $v);
			// 	}
			// }
			$passport->propeties = $this->property_model->get_data_for_equipment($passport->equipment_id);

			$passport->operating_list = [];
			// foreach ($operating_list as $k => $v) {
			// 	if ($value->id === $v->passport_id) {
			// 		array_push($value->operating_list, $v);
			// 	}
			// }

			$passport->operating_list = $this->operating_list_model->get_data_for_passport($passport->id);

			$passport->DT_RowId = $passport->id;

			$passport->DT_RowData['user_group'] = $this->session->user->group;
			$passport->DT_RowData['subdivision_id'] = $passport->subdivision_id;
			$passport->DT_RowData['complete_renovation_object_id'] = $passport->complete_renovation_object_id;
			$passport->DT_RowData['specific_renovation_object_id'] = $passport->specific_renovation_object_id;
			$passport->DT_RowData['place_id'] = $passport->place_id;
			$passport->DT_RowData['equipment_id'] = $passport->equipment_id;

			$passport->DT_RowAttr['data-user_group'] = $this->session->user->group;
			$passport->DT_RowAttr['subdivision_id'] = $passport->subdivision_id;
			$passport->DT_RowAttr['complete_renovation_object_id'] = $passport->complete_renovation_object_id;
			$passport->DT_RowAttr['specific_renovation_object_id'] = $passport->specific_renovation_object_id;
			$passport->DT_RowAttr['place_id'] = $passport->place_id;
			$passport->DT_RowAttr['equipment_id'] = $passport->equipment_id;

			// $passport->DT_RowClass = '';
		}

		$data['draw'] = $this->input->post('draw');
		$data['recordsTotal'] = $this->passport_model->get_count_all();
		$data['recordsFiltered'] = $this->passport_model->get_records_filtered($this->input->post(), $filter);
		$data['data'] = $passports;

		$data['user_group'] = $this->session->user->group;

		$data['post'] = $this->input->post();

		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function add_passport()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('complete_renovation_object_id', 'Підстанція', 'required');
		$this->form_validation->set_rules('equipment_id', 'Вид обладнання', 'required');
		$this->form_validation->set_rules('insulation_type_id', 'Вид ізоляції', 'required');
		$this->form_validation->set_rules('place_id', 'Місце встановлення', 'required');
		$this->form_validation->set_rules('voltage_class_id', 'Клас напруги', 'required');
		$this->form_validation->set_rules('disp', 'Диспечерське найменування', 'required|trim');
		$this->form_validation->set_rules('type', 'Тип обладнання', 'required|trim');
		$this->form_validation->set_rules('production_date', 'Дата виготовлення', 'required|trim');
		$this->form_validation->set_rules('number', 'Номер', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {

			// Проверяем есть ли такая запись в таблице specific_renovation_objects
			$disp = $this->specific_renovation_object_model->is_specific_renovation_object($this->input->post('complete_renovation_object_id'), $this->input->post('disp'), $this->input->post('equipment_id'));

			if (!$disp) {
				$specific_renovation_object_data = $this->set_specific_renovation_object_add_data($this->input->post());
				$specific_renovation_object_id = $this->specific_renovation_object_model->add_data($specific_renovation_object_data);
				for ($i = 1; $i <= 3; $i++) {
					$schedule_data = $this->set_schedule_add_data($this->input->post(), $specific_renovation_object_id, $i);
					$this->schedule_model->add_data($schedule_data);
				}
				$passport_data = $this->set_passport_add_data($this->input->post(), $specific_renovation_object_id);
				$passport_id = $this->passport_model->add_data($passport_data);
			} else {
				$specific_renovation_object_id = $disp->id;
				$passport_data = $this->set_passport_add_data($this->input->post(), $specific_renovation_object_id);
				$passport_id = $this->passport_model->add_data($passport_data);
			}

			if ($passport_id > 0) {
				$log_data = $this->set_log_data("Створення паспортних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($passport_data, JSON_UNESCAPED_UNICODE), 2);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'specific_renovation_object_id' => $specific_renovation_object_id, 'passport_id' => $passport_id], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function edit_passport()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		// $this->form_validation->set_rules('complete_renovation_object_id', 'Підстанція', 'required');
		// $this->form_validation->set_rules('equipment_id', 'Вид обладнання', 'required');
		// $this->form_validation->set_rules('insulation_type_id', 'Вид ізоляції', 'required');
		// $this->form_validation->set_rules('place_id', 'Місце встановлення', 'required');
		// $this->form_validation->set_rules('voltage_class_id', 'Клас напруги', 'required');
		// $this->form_validation->set_rules('disp', 'Диспечерське найменування', 'required');
		$this->form_validation->set_rules('type', 'Тип обладнання', 'required|trim');
		// $this->form_validation->set_rules('production_date', 'Дата виготовлення', 'required');
		$this->form_validation->set_rules('number', 'Номер', 'trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$passport_data_before = $this->passport_model->get_passport($this->input->post('id'));
			$passport_data_after = $this->set_passport_edit_data($this->input->post());
			$result = $this->passport_model->edit_data($passport_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна паспортних даних.", 'update', json_encode($passport_data_before, JSON_UNESCAPED_UNICODE), json_encode($passport_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
				return;
			} else {
				$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Помилка на сервері!'], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function get_data_passport()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$passport = $this->passport_model->get_passport($this->input->post('id'));

		if (!$passport) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Не вдалося отримати дані з реєстру!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$disp = $this->specific_renovation_object_model->get_specific_renovation_object($passport->specific_renovation_object_id);

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'passport' => $passport, 'disp' => $disp], JSON_UNESCAPED_UNICODE));
	}

	public function delete_passport($passport_id, $specific_renovation_object_id)
	{
		// $this->passport_model->delete_passport_full($passport_id, $specific_renovation_object_id);
		redirect('/passports');
	}

	public function get_properties()
	{
		$this->output->set_content_type('application/json');
		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$is_passport_properties = $this->passport_property_model->get_data_for_passport($this->input->post('passport_id'));

		if ($is_passport_properties) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Вже є дані для цього паспорту. Можливо Ви хочете змінити дані.'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$is_properties = $this->property_model->get_data_equipment($this->input->post('equipment_id'));

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Характеристики завантажено!', 'passport_properties' => $is_passport_properties, 'properties' => $is_properties], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function add_properties()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$data = $this->set_properties_add_data($this->input->post());

		$result = $this->passport_property_model->add_data_batch($data);

		if ($result) {
			$log_data = $this->set_log_data("Створення технічних характеристик.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($data, JSON_UNESCAPED_UNICODE), 2);
			$this->log_model->insert_data($log_data);
		}

		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'result' => $result], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function edit_property()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('value', 'Значення', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$property_data_defore = $this->passport_property_model->get_data_row($this->input->post('id'));
			$property_data_after = $this->set_properties_edit_data($this->input->post());

			$result = $this->passport_property_model->edit_data_row($property_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна технічних характеристик.", 'update', json_encode($property_data_defore, JSON_UNESCAPED_UNICODE), json_encode($property_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function add_operating_list()
	{
		// print_r($_POST);
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('service_date', 'Дата обслуговування обладнання', 'required|trim');
		$this->form_validation->set_rules('service_data', 'Дані з експлуатації обладнання', 'required|trim');
		$this->form_validation->set_rules('executor', 'Виконавець робіт', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$operating_list_data = $this->set_oparating_list_add_data($this->input->post());

			$result = $this->operating_list_model->add_data($operating_list_data);

			if ($result) {
				$log_data = $this->set_log_data("Створення експлуатаційних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($operating_list_data, JSON_UNESCAPED_UNICODE), 2);
				$this->log_model->insert_data($log_data);
			}

			if ($this->input->post('places')) {
				foreach ($this->input->post('places') as $key => $place) {
					$operating_list_copy_data = $this->set_oparating_list_copy_data($this->input->post(), $place, $this->input->post('passports')[$place]);

					$result = $this->operating_list_model->add_data($operating_list_copy_data);

					if ($result) {
						$log_data = $this->set_log_data("Створення експлуатаційних даних.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($operating_list_copy_data, JSON_UNESCAPED_UNICODE), 2);
						$this->log_model->insert_data($log_data);
					}
				}
			}

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'result' => $result], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function edit_operating_list()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('service_date', 'Дата обслуговування', 'required|trim');
		$this->form_validation->set_rules('service_data', 'Дані з експлуатації', 'required|trim');
		$this->form_validation->set_rules('executor', 'Виконавець', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$operating_list_data_before = $this->operating_list_model->get_data_row($this->input->post('id'));
			$operating_list_data_after = $this->set_oparating_list_edit_data($this->input->post());

			$result = $this->operating_list_model->edit_data_row($operating_list_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна експлуатаційних даних.", 'update', json_encode($operating_list_data_before, JSON_UNESCAPED_UNICODE), json_encode($operating_list_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
	}

	public function delete_operating_list()
	{
		$this->output->set_content_type('application/json');

		// Якщо це не Ajax-запрос
		if ($this->input->is_ajax_request() === FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не Ajax-запрос!']));
			return;
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'master') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Ви не меєте прав видаляти ці дані!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (!$this->input->post('id') || $this->input->post('id') === 'undefined') {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$operating_list_data_before = $this->operating_list_model->get_data_row($this->input->post('id'));

		$result = $this->operating_list_model->delete_data_row($this->input->post('id'));

		if ($result) {
			$log_data = $this->set_log_data("Видалення експлуатаційних даних.", 'delete', json_encode($operating_list_data_before, JSON_UNESCAPED_UNICODE), json_encode(NULL, JSON_UNESCAPED_UNICODE), 4);
			$this->log_model->insert_data($log_data);

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані видалено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function gen_passport_pdf($id, $is_hide = NULL)
	{
		$data = [];
		$passport = $this->passport_model->get_row($id);
		$properties = $this->passport_property_model->get_data_for_passport($id);
		$passport->properties = $properties;
		$data['passport'] = $passport;
		$data['operating_list'] = $this->operating_list_model->get_data_for_passport($id);
		$data['is_hide'] = $is_hide;

		// echo "<pre>";
		// print_r($passport);
		// echo "</pre>";

		// create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A3', true, 'UTF-8', false);

		// set document information
		$pdf->SetAuthor('Yurii Sychov');
		$pdf->SetTitle('Passport');

		// set default header data
		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 11, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		$html = $this->load->view('passports/passport_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 20, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.

		$pdf->Output("Passport.pdf", 'I');
	}

	public function gen_passport_object_pdf($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$data = [];

		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);

		$passports = $this->passport_model->get_rows($id);

		$equipments_group = [];
		foreach ($passports as $row) {
			$row->color = '';
			// switch ($row->insulation_type_id) {
			// 	case 4:
			// 		$row->color = 'red';
			// 		break;
			// case 2:
			// 	$row->color = 'green';
			// 	break;
			// case 6:
			// 	$row->color = 'green';
			// 	break;
			// }

			$equipments_group[$row->equipment_voltage][] = [
				'disp' => $row->disp,
				'place' => $row->place,
				'equipment' => $row->equipment . ' ' . $row->voltage / 1000 . ' кВ',
				'type' => $row->type,
				'number' => $row->number,
				'year' => date('Y', strtotime($row->production_date)),
				'insulation_type' => mb_strtolower($row->insulation_type),

				'id_w' => '5%',
				'disp_w' => '15%',
				'type_w' => '20%',
				'number_w' => '10%',
				'year_w' => '10%',

				'color' => $row->color,

				'disp_bc' => $row->disp === '' ? '#00ff00' : '#ffffff',
				'type_bc' => $row->type === '' ? '#00ff00' : '#ffffff',
				'number_bc' => $row->number === '' ? '#00ff00' : '#ffffff',
				'year_bc' => ($row->production_date === '' || $row->production_date === '0000-00-00') ? '#00ff00' : '#ffffff',
			];
		}

		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $equipments_group;


		// exit;
		// echo "<pre>";
		// print_r($passports);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;
		// $properties = $this->passport_property_model->get_properties($id);
		// $passports->properties = $properties;
		// $data['passport'] = $passports;
		// $data['operating_list'] = $this->operating_list_model->get_data_for_passport($id);

		// echo "<pre>";
		// print_r($passport);
		// echo "</pre>";

		// create new PDF document
		$pdf = new MYPDF('L', PDF_UNIT, 'A3', true, 'UTF-8', false);

		// set document information
		$pdf->SetAuthor('Yurii Sychov');
		$pdf->SetTitle('Passport Object');

		// set default header data
		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		// $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// set header and footer fonts
		// $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		// $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		$html = $this->load->view('passports/passport_object_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 10, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('passport_object.pdf', 'I');
	}

	public function gen_operating_list_object_pdf($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$data = [];
		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);
		if (!$complete_renovation_object) {
			show_404();
		}
		$operating_list = $this->operating_list_model->get_data_for_object($id);
		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $operating_list;

		// echo "<pre>";
		// print_r($data);
		// print_r($operating_list);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;

		// create new PDF document
		$pdf = new MYPDF('L', PDF_UNIT, 'A3', true, 'UTF-8', false);

		// set document information
		$pdf->SetAuthor('Yurii Sychov');
		$pdf->SetTitle('Operating list Object');

		// set default header data
		// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		// $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

		// set header and footer fonts
		// $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		// $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 12, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

		// Set some content to print
		$html = $this->load->view('passports/operating_list_object_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 15, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('operating_list_object.pdf', 'I');
	}

	public function get_places()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$specific_renovation_object_id = $this->input->post('specific_renovation_object_id');
		$place_id = $this->input->post('place_id');

		$places = $this->passport_model->get_places_for_specific_renovation_object($specific_renovation_object_id, $place_id);

		if ($places) {
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Є ще місця встановлення обладнання для цього диспетчерського найменування.', 'places' => $places], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function get_value($value = NULL)
	{
		if (!$value) {
			show_404();
		}
		$data['values'] = $this->operating_list_model->get_value($value);
		$data['field'] = $value;
		$this->load->view('value', $data);
	}

	// public function get_specific_renovation_objects()
	// {
	// 	$this->output->set_content_type('application/json');
	// 	if ($this->input->post('disp')) {
	// 		$results = $this->specific_renovation_object_model->get_search($this->input->post('disp'), $this->input->post('equipment'));
	// 	}
	// 	if (isset($results)) {
	// 		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані отримано!', 'results' => $results], JSON_UNESCAPED_UNICODE));
	// 		return;
	// 	} else {
	// 		$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Сталася помилка!'], JSON_UNESCAPED_UNICODE));
	// 		return;
	// 	}
	// }

	private function set_properties_add_data($post)
	{
		$data = [];

		foreach ($post as $key => $subarr) {
			foreach ($subarr as $subkey => $subvalue) {
				$data[$subkey][$key] = $subvalue;
				$data[$subkey]['created_by'] = $this->session->user->id;
				$data[$subkey]['updated_by'] = $this->session->user->id;
				$data[$subkey]['created_at'] = date('Y-m-d H:i:s');
				$data[$subkey]['updated_at'] = date('Y-m-d H:i:s');
			}
		}

		return $data;
	}

	private function set_properties_copy_data($donor, $passport_id)
	{
		$array_new = [];

		foreach ($donor as $k => $v) {
			$data['passport_id'] = $passport_id;
			$data['property_id'] = $v->property_id;
			$data['value'] = $v->value;
			$data['is_copy'] = 1;
			$data['created_by'] = $this->session->user->id;
			$data['updated_by'] = $this->session->user->id;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			array_push($array_new, $data);
		}

		// foreach ($array_new as $key => $subarr) {
		// 	foreach ($subarr as $subkey => $subvalue) {
		// 		$data[$subkey][$key] = $subvalue;
		// 		// $data[$subkey]['created_by'] = $this->session->user->id;
		// 		// $data[$subkey]['updated_by'] = $this->session->user->id;
		// 		// $data[$subkey]['created_at'] = date('Y-m-d H:i:s');
		// 		// $data[$subkey]['updated_at'] = date('Y-m-d H:i:s');
		// 	}
		// }

		return $array_new;
	}

	private function set_properties_edit_data($post)
	{
		$data = [];

		$data['value'] = $post['value'];
		$data['updated_by'] = $this->session->user->id;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_specific_renovation_object_add_data($post)
	{
		$data = [];
		$data['subdivision_id'] = 1;
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['name'] = $post['disp'];
		$data['year_commissioning'] = NULL;
		$data['equipment_id'] = $post['equipment_id'];
		$data['voltage_class_id'] = $post['voltage_class_id'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_passport_add_data($post, $specific_renovation_object_id)
	{
		$data = [];
		$data['subdivision_id'] = 1;
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $specific_renovation_object_id;
		$data['place_id'] = $post['place_id'];
		$data['insulation_type_id'] = $post['insulation_type_id'];
		$data['type'] = $post['type'];
		$data['production_date'] = date('Y-m-d', strtotime($post['production_date']));
		$data['number'] = $post['number'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_schedule_add_data($post, $specific_renovation_object_id, $i)
	{
		$data = [];
		$data['specific_renovation_object_id'] = $specific_renovation_object_id;
		$data['type_service_id'] = $i;
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_passport_edit_data($post)
	{
		$data = [];
		// $data['subdivision_id'] = 1;
		// $data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		// $data['specific_renovation_object_id'] = $specific_renovation_object_id;
		// $data['place_id'] = $post['place_id'];
		$data['insulation_type_id'] = $post['insulation_type_id'];
		$data['type'] = $post['type'];
		$data['production_date'] = date('Y-m-d', strtotime($post['production_date']));
		$data['number'] = $post['number'];
		$data['refinement_method'] = $post['refinement_method'];
		// $data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		// $data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_oparating_list_add_data($post)
	{

		$data = [];

		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $post['specific_renovation_object_id'];
		$data['place_id'] = $post['place_id'];
		$data['passport_id'] = $post['passport_id'];
		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_oparating_list_copy_data($post, $place_id, $passport_id)
	{

		$data = [];

		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $post['specific_renovation_object_id'];
		$data['place_id'] = $place_id;
		$data['passport_id'] = $passport_id;
		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_oparating_list_edit_data($post)
	{
		$data = [];

		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['updated_by'] = $this->session->user->id;
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_log_data($action, $short_action, $data_before, $data_after, $importance)
	{
		$this->load->library('user_agent');
		$data = [];

		$data['user_id'] = $this->session->user->id;
		$data['link'] = uri_string();
		$data['action'] = $action;
		$data['short_action'] = $short_action;
		$data['data_before'] = $data_before;
		$data['data_after'] = $data_after;
		$data['browser'] = $this->agent->browser();
		$data['ip'] = $this->input->ip_address();
		$data['platform'] = $this->agent->platform();
		$data['importance'] = $importance;
		$data['created_at'] = date('Y-m-d H:i:s');

		return $data;
	}








	// Trash ----------------------------------------------------------------------------------------------------
	public function gen_passport_object($id)
	{
		$data = [];

		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);

		$data['title'] = 'Паспорт ' . $complete_renovation_object->name;
		$data['content'] = 'passports/passport_object';
		$data['page'] = 'passports/passport_object';

		$passports = $this->passport_model->get_rows($id);

		$equipments_group = [];
		foreach ($passports as $row) {
			$row->color = '';
			switch ($row->insulation_type_id) {
				case 4:
					$row->color = 'red';
					break;
					// case 2:
					// 	$row->color = 'green';
					// 	break;
					// case 6:
					// 	$row->color = 'green';
					// 	break;
			}

			$equipments_group[$row->equipment_voltage][] = [
				'disp' => $row->disp,
				'place' => $row->place,
				'equipment' => $row->equipment . ' ' . $row->voltage / 1000 . ' кВ',
				'type' => $row->type,
				'number' => $row->number,
				'year' => date('Y', strtotime($row->production_date)),
				'insulation_type' => mb_strtolower($row->insulation_type),

				'id_w' => '5%',
				'disp_w' => '35%',
				'type_w' => '30%',
				'number_w' => '20%',
				'year_w' => '10%',

				'color' => $row->color,
			];
		}

		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $equipments_group;

		// echo "<pre>";
		// print_r($passports);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;

		$this->load->view('layout', $data);
	}



	// Trash ----------------------------------------------------------------------------------------------------
	public function gen_operating_list_object($id)
	{
		$data = [];

		$complete_renovation_object = $this->complete_renovation_object_model->get_row($id);

		$data['title'] = 'Експлуатаційна відомість ' . $complete_renovation_object->name;
		$data['content'] = 'passports/operating_list_object';
		$data['page'] = 'passports/operating_list_object';

		$operating_list = $this->operating_list_model->get_data_for_object($id);

		$data['complete_renovation_object'] = $complete_renovation_object;
		$data['results'] = $operating_list;

		// echo "<pre>";
		// print_r($operating_list);
		// print_r($equipments_group);
		// echo "</pre>";
		// exit;

		$this->load->view('layout', $data);
	}
}
