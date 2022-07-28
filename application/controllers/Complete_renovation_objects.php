<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

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

class Complete_renovation_objects extends CI_Controller
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
		// $this->load->model('passport_model');
		$this->load->model('operating_list_object_model');
		// $this->load->model('schedule_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('type_service_model');
		// $this->load->model('specific_renovation_object_model');
		// $this->load->model('equipment_model');
		// $this->load->model('voltage_class_model');
		// $this->load->model('insulation_type_model');
		// $this->load->model('place_model');
		// $this->load->model('property_model');
		// $this->load->model('passport_property_model');
		$this->load->model('log_model');
	}

	public function index()
	{
		$this->load->library('form_validation');
		$data = [];
		$data['title'] = 'Енергетичні об`єкти';
		$data['content'] = 'complete_renovation_objects/index';
		$data['page'] = 'complete_renovation_objects/index';
		$data['page_js'] = 'complete_renovation_objects';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Енергетичні об`єкти (Експлуатаційна відомість)';
		$data['title_heading_card'] = 'Власне енергетичні об`єкти';
		$stantions = $this->complete_renovation_object_model->get_data_with_subdivision_for_user();
		// $data['stantions'] = $this->complete_renovation_object_model->get_data_with_subdivision_for_user();
		$data['type_services'] = $this->type_service_model->get_data();

		$arr_temp = [];
		foreach ($stantions as $stantion) {
			$stantion->count_rows = $this->operating_list_object_model->get_count_rows($stantion->id);
			$stantion->create_last_date = $this->operating_list_object_model->get_max_create_date_row($stantion->id);
		}
		$data['stantions'] = $stantions;
		// $data['equipments'] = $this->equipment_model->get_data();
		// $data['voltage_class'] = $this->voltage_class_model->get_data();
		// $data['insulation_type'] = $this->insulation_type_model->get_data();
		// $data['places'] = $this->place_model->get_data();
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		$this->load->view('layout', $data);
	}

	public function add_operating_list_object()
	{
		$this->output->set_content_type('application/json');

		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Відсутні дані POST!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('service_date', 'Дата обслуговування об`єкту', 'required|trim');
		$this->form_validation->set_rules('service_data', 'Дані з експлуатації по об`єкту', 'required|trim');
		$this->form_validation->set_rules('executor', 'Виконавець робіт', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		} else {
			$operating_list_object_data = $this->set_oparating_list_object_add_data($this->input->post());

			$result = $this->operating_list_object_model->add_data($operating_list_object_data);

			if ($result) {
				$log_data = $this->set_log_data("Створення експлуатаційних даних по об`єкту.", 'create', json_encode(NULL, JSON_UNESCAPED_UNICODE), json_encode($operating_list_object_data, JSON_UNESCAPED_UNICODE), 2);
				$this->log_model->insert_data($log_data);
			}

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані додано!', 'result' => $result], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function edit_operating_list_object()
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
			$operating_list_data_after = $this->set_oparating_list_object_edit_data($this->input->post());

			$result = $this->operating_list_model->edit_data_row($operating_list_data_after, $this->input->post('id'));

			if ($result) {
				$log_data = $this->set_log_data("Зміна експлуатаційних даних.", 'update', json_encode($operating_list_data_before, JSON_UNESCAPED_UNICODE), json_encode($operating_list_data_after, JSON_UNESCAPED_UNICODE), 3);
				$this->log_model->insert_data($log_data);

				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Дані змінено!', 'result' => $result], JSON_UNESCAPED_UNICODE));
				return;
			}
		}
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
		$type_services = $this->type_service_model->get_data();
		$operating_list = $this->operating_list_object_model->get_data_for_object($id);
		foreach ($operating_list as $item) {
			$item->type_service_short_name = NULL;
			$item->type_service_name = NULL;
			foreach ($type_services as $type_service) {
				if ($item->type_service_id == $type_service->id) {
					$item->type_service_short_name = $type_service->short_name;
					$item->type_service_name = $type_service->name;
				}
			}
		}
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
		$html = $this->load->view('complete_renovation_objects/operating_list_object_pdf', $data, TRUE);

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', 15, $html, 0, 1, 0, true, '', true);

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('operating_list_object.pdf', 'I');
	}


	public function get_value($value = NULL)
	{
		if (!$value) {
			show_404();
		}
		$data['values'] = $this->operating_list_object_model->get_value($value);
		$data['field'] = $value;
		$this->load->view('value', $data);
	}

	private function set_oparating_list_object_add_data($post)
	{

		$data = [];

		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['type_service_id'] = $post['type_service_id'] ? $post['type_service_id'] : 0;
		$data['service_date'] = date('Y-m-d', strtotime($post['service_date']));
		$data['service_data'] = $post['service_data'];
		$data['executor'] = $post['executor'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');

		return $data;
	}

	private function set_oparating_list_object_edit_data($post)
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
}
