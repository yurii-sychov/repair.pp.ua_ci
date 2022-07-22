<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Passport_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('passports.*, places.name as place');
		$this->db->from('passports');
		$this->db->join('places', 'places.id = passports.place_id');
		$query = $this->db->get();
		return $query->result();
	}

	public function add_data($data)
	{
		$this->db->insert('passports', $data);
		return $this->db->insert_id();
	}

	public function edit_data($data, $id)
	{
		$this->db->where('id', $id);
		$query = $this->db->update('passports', $data);
		return $query;
	}

	public function delete_passport_full($passport_id, $specific_renovation_object_id)
	{
		$this->db->where('passport_id', $passport_id);
		$this->db->delete('operating_list');

		$this->db->where('passport_id', $passport_id);
		$this->db->delete('passport_properties');

		$this->db->where('specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->delete('schedules');

		$this->db->where('id', $specific_renovation_object_id);
		$this->db->delete('specific_renovation_objects');

		$this->db->where('id', $passport_id);
		$query = $this->db->delete('passports');


		return $query;
	}

	public function get_passports($specific_renovation_object_id)
	{
		$this->db->select('passports.*, places.name');
		$this->db->from('passports');
		$this->db->join('places', 'places.id = passports.place_id');
		$this->db->where('specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->order_by('places.name', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_passport($id)
	{
		$this->db->select('passports.*, DATE_FORMAT(passports.production_date, "%d.%m.%Y") as production_date');
		$this->db->from('passports');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_data_datatables()
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments, users_complete_renovation_objects');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id=places.id');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_records_filtered($post, $filter)
	{
		$this->db->select('passports.id');
		$this->db->from('passports, specific_renovation_objects, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id=passports.specific_renovation_object_id');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {

			$this->db->where('passports.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['voltage_class_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_class_id'], "^"), "$"));
		}
		if ($post['search']['value']) {
			$this->db->like('specific_renovation_objects.name', $post['search']['value']);
		}
		$this->db->get();

		return $this->db->affected_rows();
	}

	public function get_data_datatables_server_side($post, $filter, $order_dir, $order_field)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, specific_renovation_objects.voltage_class_id as voltage_class_id, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments, users_complete_renovation_objects');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id=places.id');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {

			$this->db->where('passports.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['voltage_class_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_class_id'], "^"), "$"));
		}
		if ($post['search']['value']) {
			$this->db->like('specific_renovation_objects.name', $post['search']['value']);
		}
		$this->db->order_by($order_field, $order_dir);

		$this->db->limit($post['length'], $post['start']);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_count_all()
	{
		$this->db->select('COUNT(passports.id) as count');
		$this->db->from('passports, users_complete_renovation_objects');
		$this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$query = $this->db->get();

		return (int) $query->row('count');
	}

	public function get_row($id)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, places.name as place');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('passports.id', $id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_rows($complete_renovation_object_id)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, equipments.id as equipment_id, specific_renovation_objects.name as disp, places.name as place, voltage_class.voltage as voltage, CONCAT(equipments.name, " ", (voltage_class.voltage/1000), " кВ") as equipment_voltage, insulation_type.insulation_type as insulation_type');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, places, equipments, voltage_class, insulation_type');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('passports.insulation_type_id = insulation_type.id');
		$this->db->where('passports.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->order_by('equipment', 'ASC');
		$this->db->order_by('disp', 'ASC');
		$this->db->order_by('place', 'ASC');
		// $this->db->limit(20);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_places_for_specific_renovation_object($specific_renovation_object_id, $place_id)
	{
		$this->db->select('passports.id, passports.place_id, places.name as place');
		$this->db->from('passports, places');
		$this->db->where('passports.place_id = places.id');
		$this->db->where('specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->where_not_in('place_id', $place_id);
		$this->db->order_by('place_id', 'ACS');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_capital_repairs_of_transformers($per_page, $offset)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, specific_renovation_objects.name as disp');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, equipments');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		// $this->db->where('passports.complete_renovation_object_id = users_complete_renovation_objects.object_id');
		// $this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->where('equipments.id = 19');
		$this->db->where('specific_renovation_objects.voltage_class_id >= 3');
		$this->db->where('specific_renovation_objects.voltage_class_id <= 5');
		if ($this->input->get('stantion')) {
			$this->db->like('complete_renovation_objects.name', $this->input->get('stantion'));
		}
		if ($this->input->get('disp')) {
			$this->db->like('specific_renovation_objects.name', $this->input->get('disp'));
		}
		$this->db->order_by('stantion', 'ASC');
		$this->db->order_by('disp', 'ASC');
		$this->db->limit($per_page, $offset);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_total_capital_repairs_of_transformers()
	{
		$this->db->select('passports.id');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, equipments');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('equipments.id = 19');
		$this->db->where('specific_renovation_objects.voltage_class_id >= 3');
		$this->db->where('specific_renovation_objects.voltage_class_id <= 5');
		if ($this->input->get('stantion')) {
			$this->db->like('complete_renovation_objects.name', $this->input->get('stantion'));
		}
		if ($this->input->get('disp')) {
			$this->db->like('specific_renovation_objects.name', $this->input->get('disp'));
		}
		$this->db->get();

		return $this->db->affected_rows();
	}

	public function get_transformers($subdivision_id, $complete_renovation_object_id)
	{
		$this->db->select('passports.*, complete_renovation_objects.name as stantion, equipments.name as equipment, specific_renovation_objects.name as disp');
		$this->db->from('passports, complete_renovation_objects, specific_renovation_objects, equipments');
		$this->db->where('passports.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('passports.specific_renovation_object_id = specific_renovation_objects.id');
		$this->db->where('passports.subdivision_id', $subdivision_id);
		$this->db->where('passports.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('equipments.id = 19');
		$this->db->order_by('disp', 'ASC');
		$query = $this->db->get();

		return $query->result();
	}
}
