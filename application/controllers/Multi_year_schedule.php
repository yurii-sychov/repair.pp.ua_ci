<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Font, Border, Alignment};

use function PHPSTORM_META\type;

class Multi_year_schedule extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'engineer') {
			show_404();
		}
		$this->load->model('schedule_model');
		$this->load->model('passport_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('equipment_model');
		$this->load->model('type_service_model');
		$this->load->model('voltage_class_model');
	}

	public function index()
	{
		$data = [];
		$data['title'] = 'Багаторічний графік';
		$data['content'] = 'multi_year_schedule/index';
		$data['page'] = 'multi_year_schedule/index';
		$data['page_js'] = 'multi_year_schedule';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Багаторічний графік';
		$data['title_heading_card'] = 'Редагування багаторічного графіку';
		$data['stantions'] = $this->complete_renovation_object_model->get_data_for_user();
		$data['equipments'] = $this->equipment_model->get_data();
		$data['type_services'] = $this->type_service_model->get_data();
		$data['voltage_class'] = $this->voltage_class_model->get_data();
		$this->load->view('layout', $data);
	}

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

		$specific_renovation_objects = $this->schedule_model->get_data_datatables_server_side($this->input->post(), $filter, $order_dir, $order_field);

		foreach ($specific_renovation_objects as $key => $value) {
			$passports = $this->passport_model->get_passports($value->specific_renovation_object_id);
			$places = [];
			$value->places = $places;
			foreach ($passports as $k => $v) {
				if ($value->specific_renovation_object_id == $v->specific_renovation_object_id) {
					if ($v->place_id == 1) {
						$place_color = 'warning';
					} elseif ($v->place_id == 2) {
						$place_color = 'success';
					} elseif ($v->place_id == 3) {
						$place_color = 'danger';
					} else {
						$place_color = 'primary';
					}
					array_push($places, [
						'place_name' => $v->name,
						'place_color' => $place_color,
						'type' => $v->type,
						'number' => $v->number,
						'production_date' => $v->production_date,
					]);
					$value->places = $places;
				}
			}
		}

		$data['draw'] = $this->input->post('draw');
		$data['recordsTotal'] = $this->schedule_model->get_count_all();
		$data['recordsFiltered'] = $this->schedule_model->get_records_filtered($this->input->post(), $filter);
		$data['data'] = $specific_renovation_objects;

		$data['user_group'] = $this->session->user->group;

		$data['post'] = $this->input->post();

		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function get_data()
	{
		$this->output->set_content_type('application/json');

		// if (!$this->input->post()) {
		// 	$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
		// 	return;
		// }

		$specific_renovation_objects = $this->schedule_model->get_data_datatables();
		$passports = $this->passport_model->get_data();

		foreach ($specific_renovation_objects as $key => $value) {
			// $passports = $this->passport_model->get_passports($value->specific_renovation_object_id);
			// $passport_filter = array_filter($passports, function ($arr) {
			// 	return $arr->specific_renovation_object_id == $arr->specific_renovation_object_id;
			// });
			$places = [];
			$value->places = $places;
			// echo "<pre>";
			// print_r($passport_filter);
			// echo "</pre>";
			foreach ($passports as $k => $v) {
				if ($value->specific_renovation_object_id == $v->specific_renovation_object_id) {
					if ($v->place_id == 1) {
						$place_color = 'warning';
					} elseif ($v->place_id == 2) {
						$place_color = 'success';
					} elseif ($v->place_id == 3) {
						$place_color = 'danger';
					} else {
						$place_color = 'primary';
					}
					array_push($places, [
						'place_name' => $v->place,
						'place_color' => $place_color,
						'type' => $v->type,
						'number' => $v->number,
						'production_date' => $v->production_date,
					]);
					$value->places = $places;
				}
			}
		}

		// $data['passports'] = $pp;
		$data['data'] = $specific_renovation_objects;

		$this->output->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
	}

	public function get_schedule_kr()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Графік капітального ремонту.xlsx"');
		header('Cache-Control: max-age=0');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->mergeCells('A1:K1');
		$sheet->setCellValue("A1", "Багаторічний графік капітального ремонту обладнання СП");
		$sheet->getRowDimension(1)->setRowHeight(30);
		$sheet->getStyle('A1')->applyFromArray([
			'font' => [
				'name' => 'TimesNewRoman',
				'bold' => true,
				'italic' => false,
				'underline' => Font::UNDERLINE_DOUBLE,
				'strikethrough' => false,
				// 'color' => [
				// 	'rgb' => '000000'
				// ],
				'size' => 18
			],
			// 'borders' => [
			// 	'allBorders' => [
			// 		'borderStyle' => Border::BORDER_THIN,
			// 		'color' => [
			// 			'rgb' => '808080'
			// 		]
			// 	],
			// ],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
				'wrapText' => true,
			]
		]);

		$header = [
			'A' => 'Підстанція',
			'B' => 'Обладнання',
			'C' => 'Дисп.',
			'D' => 'Тип',
			'E' => 'Період',
			'F' => 'Рік',
			'G' => date('Y') + 1,
			'H' => date('Y') + 2,
			'I' => date('Y') + 3,
			'J' => date('Y') + 4,
			'K' => date('Y') + 5,
		];
		foreach ($header as $k => $v) {
			$sheet->getColumnDimension($k)->setAutoSize(true);
			$sheet->setCellValue($k . "2", $v);
		}

		$data = [
			[
				'A' => 'ПС 1',
				'B' => 'Вимикач',
				'C' => 'Т-11',
				'D' => 'ВМПЭ-10',
				'E' => '6',
				'F' => '2017',
				'G' => '',
				'H' => 'KP',
				'I' => '',
				'J' => '',
				'K' => '',
			],
			[
				'A' => 'ПС 1',
				'B' => 'Вимикач',
				'C' => 'Т-12',
				'D' => 'ВМПЭ-10',
				'E' => '6',
				'F' => '2018',
				'G' => '',
				'H' => '',
				'I' => 'KP',
				'J' => '',
				'K' => '',
			],
		];
		foreach ($data as $key => $value) {

			foreach ($value as $k => $v) {
				$sheet->setCellValue($k . ($key + 3), $v);
			}
		}
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function get_schedule_pr()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Графік поточного ремонту.xlsx"');
		header('Cache-Control: max-age=0');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue("A1", "Hello World !");
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function get_schedule_to()
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Графік технічного обслуговування.xlsx"');
		header('Cache-Control: max-age=0');
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue("A1", "Hello World !");
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	public function change_cipher_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('value', 'Шифр ремонту', 'numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!'], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$result = $this->schedule_model->change_value('cipher', $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function change_periodicity_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('value', 'Періодичність', 'numeric');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!'], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$result = $this->schedule_model->change_value('periodicity', $this->input->post('value'), $this->input->post('id'));
			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function change_year_service_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}
		$result = $this->schedule_model->change_value('year_last_service', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}

	public function change_status_ajax()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}
		$this->schedule_model->change_value('status', $this->input->post('value'), $this->input->post('id'));
		$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!'], JSON_UNESCAPED_UNICODE));
		return;
	}
}
