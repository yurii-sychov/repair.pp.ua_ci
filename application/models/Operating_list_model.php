<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Operating_list_Model extends CI_Model
{
	public function get_data_for_passport($passport_id)
	{
		$this->db->select('id, DATE_FORMAT(service_date, "%d.%m.%Y") as service_date_format, service_date, service_data, executor, passport_id');
		$this->db->from('operating_list');
		$this->db->where('passport_id', $passport_id);
		$this->db->order_by('service_date', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_object($complete_renovation_object_id)
	{
		$this->db->select('specific_renovation_objects.name as disp, places.name as place, passports.type, DATE_FORMAT(service_date, "%d.%m.%Y") as service_date_format, service_data, executor');
		$this->db->from('operating_list, specific_renovation_objects, places, passports');
		$this->db->where('operating_list.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('passports.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('operating_list.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('operating_list.place_id = places.id');
		$this->db->where('operating_list.passport_id = passports.id');
		$this->db->order_by('service_date', 'ASC');
		$this->db->order_by('disp', 'ASC');
		$this->db->order_by('place', 'ASC');
		$this->db->order_by('service_data', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_row($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$this->db->from('operating_list');
		$query = $this->db->get();

		return $query->row();
	}

	public function add_data($data)
	{
		$this->db->insert('operating_list', $data);
		return $this->db->insert_id();
	}

	public function edit_data_row($data, $id)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('operating_list', $data);
		return $query;
	}

	public function get_all_operating_list()
	{
		$this->db->select('id, DATE_FORMAT(service_date, "%d.%m.%Y") as service_date_format, service_date, service_data, executor, passport_id');
		$this->db->from('operating_list');
		$this->db->order_by('service_date', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}

	public function delete_data_row($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->delete('operating_list');

		return $query;
	}

	public function get_value($field)
	{
		$this->db->select($field);
		$this->db->distinct();
		// $this->db->where('created_by', $this->session->user->id);
		$this->db->order_by($field);
		$query = $this->db->get('operating_list');
		return $query->result();
	}
}
