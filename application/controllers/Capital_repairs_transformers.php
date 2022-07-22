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

class Capital_repairs_transformers extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (!$this->session->user) {
			redirect('authentication/signin');
		}

		if ($this->session->user->group !== 'admin' && $this->session->user->group !== 'sp' && $this->session->user->group !== 'sdzp' && $this->session->user->group !== 'head') {
			show_404();
		}
		$this->load->library('pagination');
		$this->load->library('user_agent');

		$this->load->model('subdivision_model');
		$this->load->model('complete_renovation_object_model');
		$this->load->model('passport_model');
		$this->load->model('document_model');
		$this->load->model('photo_album_model');
		$this->load->model('photo_model');
	}

	public function index($page = NULL)
	{
		$this->load->helper('form');
		$data = [];
		$data['title'] = 'Капітальні ремонти силових трансформаторів';
		$data['content'] = 'capital_repairs_transformers/index';
		$data['page'] = 'capital_repairs_transformers/index';
		$data['page_js'] = 'capital_repairs_transformers';
		$data['datatables'] = FALSE;
		$data['title_heading'] = 'Капітальні ремонти силових трансформаторів';
		$data['title_heading_card'] = 'Силові трансформатори 35-150 кв';
		$this->load->helper('config_pagination');
		$config = get_config_pagination();
		$this->pagination->initialize($config);

		$per_page = $config['per_page'];
		$offset = $this->input->get('page') ? ($this->input->get('page') - 1) * $per_page : 0;
		$total_rows = $config['total_rows'];

		$passports = $this->passport_model->get_capital_repairs_of_transformers($per_page, $offset);

		foreach ($passports as $passport) {

			$passport->documents = $this->document_model->get_documents_for_passport($passport->id);
			$photos = $this->photo_model->get_photos_for_passport($passport->id);

			$passport->photo_albums = [];
			$passport->photos = [];

			foreach ($photos as $item) {
				if ($passport->id == $item->passport_id) {
					$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['id'] = $item->photo_album_id;
					$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['photo_album_date'] = $item->photo_album_date;
					$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['photo_album_name'] = $item->photo_album_name;
					$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['created_by'] = $item->created_by;
					$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['created_at'] = $item->created_at;
				}

				$group_photos['photo_album_id_' . $item->photo_album_id][] = [
					'id ' => $item->id,
					'photo_date' => $item->photo_date,
					'photo' => $item->photo,
				];
				$passport->photos = $group_photos;
			}
		}

		$data['per_page'] = !$this->input->get('page') ? 1 : (($this->input->get('page') - 1) * $per_page + 1);
		$data['offset'] = $this->input->get('page') ? $offset : $per_page;
		$data['total_rows'] = $total_rows;

		$data['passports'] = $passports;
		// echo "<pre>";
		// print_r($passports[0]);
		// echo "</pre>";
		$this->load->view('layout', $data);
	}

	public function sdzp($subdivision = NULL, $stantion = NULL)
	{
		$data = [];
		$data['title'] = 'Капітальні ремонти силових трансформаторів';
		$data['content'] = 'capital_repairs_transformers/sdzp';
		$data['page'] = 'capital_repairs_transformers/sdzp';
		$data['page_js'] = 'capital_repairs_transformers';
		$data['datatables'] = TRUE;
		$data['title_heading'] = 'Капітальні ремонти силових трансформаторів';
		$data['title_heading_card_sp'] = 'СП підстанції 110-150 (35) кВ';
		$data['title_heading_card_srm'] = 'СРМ (РЕМи)';

		if (!$subdivision && !$stantion) {
			$data['content'] = 'capital_repairs_transformers/sdzp';

			$data['stantions_sp'] = $this->complete_renovation_object_model->get_data_sp();
			$data['subdivision_srm'] = $this->subdivision_model->get_data_srm();

			$this->load->view('layout', $data);
		}

		if ($subdivision && !$stantion) {
			if (!is_numeric($subdivision)) {
				show_404();
			}

			$data['content'] = 'capital_repairs_transformers/stantions';
			$data['title_heading_card_srm'] = $this->subdivision_model->get_row($subdivision)->name . ' підстанції 35 кВ';

			$data['stantions_sp'] = $this->complete_renovation_object_model->get_data_sp();
			$data['stantions_srm'] = $this->complete_renovation_object_model->get_data_for_subdivision($subdivision);
			$this->load->view('layout', $data);
		}

		if ($subdivision && $stantion) {
			if (!is_numeric($subdivision) || !is_numeric($stantion)) {
				show_404();
			}

			$data['content'] = 'capital_repairs_transformers/transformaters';

			$data['trasformaters'] = $this->passport_model->get_transformers($subdivision, $stantion);

			if (!$data['trasformaters']) {
				show_404();
			}

			$this->load->view('layout', $data);
		}

		// $passports = $this->passport_model->get_capital_repairs_of_transformers();

		// foreach ($passports as $passport) {

		// 	$passport->documents = $this->document_model->get_documents_for_passport($passport->id);
		// 	$photos = $this->photo_model->get_photos_for_passport($passport->id);

		// 	$passport->photo_albums = [];
		// 	$passport->photos = [];

		// 	foreach ($photos as $item) {
		// 		if ($passport->id == $item->passport_id) {
		// 			$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['id'] = $item->photo_album_id;
		// 			$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['photo_album_date'] = $item->photo_album_date;
		// 			$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['photo_album_name'] = $item->photo_album_name;
		// 			$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['created_by'] = $item->created_by;
		// 			$passport->photo_albums['photo_album_id_' . $item->photo_album_id]['created_at'] = $item->created_at;
		// 		}

		// 		$group_photos['photo_album_id_' . $item->photo_album_id][] = [
		// 			'id ' => $item->id,
		// 			'photo_date' => $item->photo_date,
		// 			'photo' => $item->photo,
		// 		];
		// 		$passport->photos = $group_photos;
		// 	}
		// }

		// $data['passports'] = $passports;
		// echo "<pre>";
		// print_r($passports[0]);
		// echo "</pre>";
	}

	public function add_document()
	{
		$this->output->set_content_type('application/json');
		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('document_date', 'Дата створення документу', 'required');
		$this->form_validation->set_rules('document_description', 'Короткий опис документу', 'required|min_length[3]|max_length[255]');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$upload = $this->upload_file();
		if (isset($upload['error'])) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $upload['error'], 'error' => TRUE], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (isset($upload['upload_data'])) {
			$data = $this->set_data_document($this->input->post(), $upload['upload_data']['file_name']);

			$result = $this->document_model->insert_data($data);

			if (!$result) {
				unlink('./assets/documents/' . $upload['upload_data']['file_name']);
				$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Не вдалося зробити запис в БД. Файл видалено.'], JSON_UNESCAPED_UNICODE));
				return;
			}

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Файл успішно завантажено.'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	public function add_photos()
	{
		$this->output->set_content_type('application/json');
		if (!$this->input->post()) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Це не POST запрос!'], JSON_UNESCAPED_UNICODE));
			return;
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('photo_album_date', 'Дата створення альбому', 'required');
		$this->form_validation->set_rules('photo_album_name', 'Назва альбому', 'required|min_length[3]|max_length[255]');

		if ($this->form_validation->run() == FALSE) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => 'Щось пішло не так!', 'errors' => $this->form_validation->error_array()], JSON_UNESCAPED_UNICODE));
			return;
		}

		$upload = $this->upload_files();

		if (isset($upload['error'])) {
			$this->output->set_output(json_encode(['status' => 'ERROR', 'message' => $upload['error'][0], 'error' => TRUE], JSON_UNESCAPED_UNICODE));
			return;
		}

		if (isset($upload['success'])) {
			$data = $this->set_data_photo_album($this->input->post());
			$photo_album_id = $this->photo_album_model->insert_data($data);
			if ($photo_album_id) {
				foreach ($upload['success'] as $file) {
					$data = $this->set_data_photo($this->input->post(), $file, $photo_album_id);
					$result = $this->photo_model->insert_data($data);

					if (!$result) {
						unlink('./assets/photos/' . $file);
						$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Не вдалося зробити запис в БД. Файл видалено.'], JSON_UNESCAPED_UNICODE));
						return;
					}
				}
			}

			$this->resize_photo($upload['success']);

			// $this->watermark_photo($upload['success']);

			$this->output->set_output(json_encode(['status' => 'SUCCESS', 'message' => 'Файли успішно завантажено.'], JSON_UNESCAPED_UNICODE));
			return;
		}
	}

	private function resize_photo($photos)
	{
		$this->load->library('image_lib');

		if (!file_exists('./assets/photos/thumb')) {
			mkdir('./assets/photos/thumb', 0755);
		}

		foreach ($photos as $photo) {
			$config['image_library'] = 'gd2';
			$config['source_image'] = './assets/photos/' . $photo;
			$config['new_image'] = './assets/photos/thumb/';
			// $config['create_thumb'] = TRUE;
			$config['maintain_ratio'] = TRUE;
			$config['width'] = 100;

			$this->image_lib->initialize($config);

			if (!$this->image_lib->resize()) {
				echo $this->image_lib->display_errors();
			}
		}
	}

	private function watermark_photo($photos)
	{
		$this->load->library('image_lib');

		foreach ($photos as $photo) {
			$config['source_image'] = './assets/photos/' . $photo;
			$config['wm_text'] = 'Copyright 2022 - Yurii Sychov';
			$config['wm_type'] = 'text';
			$config['wm_font_path'] = './system/fonts/texb.ttf';
			$config['wm_font_size'] = '24';
			$config['wm_font_color'] = 'ffffff';
			$config['wm_vrt_alignment'] = 'middle';
			$config['wm_hor_alignment'] = 'center';

			$this->image_lib->initialize($config);

			if (!$this->image_lib->watermark()) {
				echo $this->image_lib->display_errors();
			}
		}
		exit;
	}

	public function delete_document($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$document = $this->document_model->get_row($id);

		if (!$document) {
			show_404();
		}

		if ($this->session->user->id != $document->created_by && $this->session->user->group !== 'admin') {
			show_404();
		}

		if (unlink('./assets/documents/' . $document->document_scan)) {
			$this->document_model->delete($id);
		}

		redirect($this->agent->referrer());
	}

	public function delete_photo_album($id = NULL)
	{
		if (!is_numeric($id)) {
			show_404();
		}

		$photo_album = $this->photo_album_model->get_row($id);

		if (!$photo_album) {
			show_404();
		}

		if ($this->session->user->id != $photo_album->created_by && $this->session->user->group !== 'admin') {
			show_404();
		}

		$photos = $this->photo_model->get_rows($photo_album->id);

		foreach ($photos as $photo) {
			if (file_exists('./assets/photos/' . $photo->photo)) {
				unlink('./assets/photos/' . $photo->photo);
			}
			if (file_exists('./assets/photos/thumb/' . $photo->photo)) {
				unlink('./assets/photos/thumb/' . $photo->photo);
			}
		}

		$this->photo_model->delete_photos($photo_album->id);
		$this->photo_album_model->delete($id);

		redirect($this->agent->referrer());
	}

	private function upload_file()
	{
		$config['upload_path'] = './assets/documents';
		$config['allowed_types'] = 'pdf';
		$config['encrypt_name'] = TRUE;

		if (!file_exists('./assets/documents')) {
			mkdir('./assets/documents', 0755);
		}

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('document_scan')) {
			$error = array('error' => $this->upload->display_errors('', ''));
			return $error;
		} else {
			$data = array('upload_data' => $this->upload->data());
			return $data;
		}
	}

	private function upload_files()
	{
		$config['upload_path'] = './assets/photos';
		$config['allowed_types'] = 'jpeg';
		$config['encrypt_name'] = TRUE;
		$config['file_ext_tolower'] = TRUE;

		if (!file_exists('./assets/photos')) {
			mkdir('./assets/photos', 0755);
		}
		$this->load->library('upload', $config);


		$count = count($_FILES['photo']['name']);

		$files = $_FILES;

		for ($i = 0; $i < 50; $i++) {
			$this->upload->initialize($config);

			if (!empty($files['photo']['name'][$i])) {
				$_FILES['photo']['name'] = $files['photo']['name'][$i];
				$_FILES['photo']['type'] = $files['photo']['type'][$i];
				$_FILES['photo']['tmp_name'] = $files['photo']['tmp_name'][$i];
				$_FILES['photo']['error'] = $files['photo']['error'][$i];
				$_FILES['photo']['size'] = $files['photo']['size'][$i];

				if (!$this->upload->do_upload('photo')) {
					$error = array('error' => $this->upload->display_errors('', ''));
					$errors['error'][] = $error['error'];
				} else {
					$data = array('upload_data' => $this->upload->data());
					$uploaded_files['success'][] = $data['upload_data']['file_name'];
				}
			}
		}

		if (isset($errors)) {
			foreach ($uploaded_files['success'] as $file) {
				unlink('./assets/photos/' . $file);
			}
			return $errors;
		}
		return ($uploaded_files);
	}

	private function set_data_document($post, $file)
	{
		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $post['specific_renovation_object_id'];
		$data['place_id'] = $post['place_id'];
		$data['passport_id'] = $post['passport_id'];
		$data['document_date'] = date('Y-m-d', strtotime($post['document_date']));
		$data['document_description'] = $post['document_description'];
		$data['document_scan'] = $file;
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_data_photo_album($post)
	{
		$data['photo_album_date'] = date('Y-m-d', strtotime($post['photo_album_date']));
		$data['photo_album_name'] = $post['photo_album_name'];
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	private function set_data_photo($post, $file, $photo_album_id)
	{
		$data['subdivision_id'] = $post['subdivision_id'];
		$data['complete_renovation_object_id'] = $post['complete_renovation_object_id'];
		$data['specific_renovation_object_id'] = $post['specific_renovation_object_id'];
		$data['place_id'] = $post['place_id'];
		$data['passport_id'] = $post['passport_id'];
		$data['photo_album_id'] = $photo_album_id;
		$data['photo_date'] = date('Y-m-d', strtotime($post['photo_album_date']));
		$data['photo'] = $file;
		$data['created_by'] = $this->session->user->id;
		$data['updated_by'] = $this->session->user->id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	public function get_value($value = NULL)
	{
		if (!$value) {
			show_404();
		}
		$data['values'] = $this->document_model->get_value($value);
		$data['field'] = $value;
		$this->load->view('value', $data);
	}
}
