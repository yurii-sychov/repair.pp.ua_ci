<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Schedule_Model extends CI_Model
{

	public function get_records_filtered($post, $filter)
	{
		$this->db->select('schedules.id');
		$this->db->from('schedules, specific_renovation_objects, complete_renovation_objects, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id=schedules.specific_renovation_object_id');
		$this->db->where('complete_renovation_objects.id=specific_renovation_objects.complete_renovation_object_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {
			$this->db->where('specific_renovation_objects.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['type_service_id']) {
			$this->db->where('schedules.type_service_id', rtrim(ltrim($filter['type_service_id'], "^"), "$"));
		}
		if ($filter['voltage_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_id'], "^"), "$"));
		}
		$this->db->get();
		return $this->db->affected_rows();
	}

	public function get_data_datatables_server_side($post, $filter, $order_dir, $order_field)
	{
		$this->db->select('schedules.*, schedules.year_last_service as year_service, specific_renovation_objects.complete_renovation_object_id, specific_renovation_objects.equipment_id, specific_renovation_objects.name as disp, specific_renovation_objects.voltage_class_id as voltage_id, type_services.name as type_service, type_services.short_name as short_type_service, complete_renovation_objects.name as stantion, equipments.name as equipment, concat(equipments.name, " ", (ROUND(voltage_class.voltage/1000, 1)), " кВ") as equipment_with_voltage, concat( ROUND((voltage_class.voltage/1000), 1), " кВ") as voltage');
		$this->db->from('schedules, complete_renovation_objects, specific_renovation_objects, type_services, equipments, voltage_class, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id=schedules.specific_renovation_object_id');
		$this->db->where('type_services.id=schedules.type_service_id');
		$this->db->where('complete_renovation_objects.id=specific_renovation_objects.complete_renovation_object_id');
		$this->db->where('equipments.id=specific_renovation_objects.equipment_id');
		$this->db->where('voltage_class.id=specific_renovation_objects.voltage_class_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		if ($filter['complete_renovation_object_id']) {
			$this->db->where('specific_renovation_objects.complete_renovation_object_id', rtrim(ltrim($filter['complete_renovation_object_id'], "^"), "$"));
		}
		if ($filter['equipment_id']) {
			$this->db->where('specific_renovation_objects.equipment_id', rtrim(ltrim($filter['equipment_id'], "^"), "$"));
		}
		if ($filter['type_service_id']) {
			$this->db->where('schedules.type_service_id', rtrim(ltrim($filter['type_service_id'], "^"), "$"));
		}
		if ($filter['voltage_id']) {
			$this->db->where('specific_renovation_objects.voltage_class_id', rtrim(ltrim($filter['voltage_id'], "^"), "$"));
		}

		$this->db->order_by($order_field, $order_dir);
		$this->db->limit($post['length'], $post['start']);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_count_all()
	{
		$this->db->select('COUNT(schedules.id) as count');
		$this->db->from('schedules, specific_renovation_objects, complete_renovation_objects, users_complete_renovation_objects');
		$this->db->where('specific_renovation_objects.id=schedules.specific_renovation_object_id');
		$this->db->where('complete_renovation_objects.id=specific_renovation_objects.complete_renovation_object_id');
		$this->db->where('complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$query = $this->db->get();

		return (int) $query->row('count');
	}

	// public function get_data_datatables()
	// {
	// 	$this->db->select('schedules.*, schedules.year_last_service as year_service, specific_renovation_objects.complete_renovation_object_id, specific_renovation_objects.equipment_id, specific_renovation_objects.name as disp, specific_renovation_objects.voltage_class_id as voltage_id, type_services.name as type_service, complete_renovation_objects.name as stantion, equipments.name as equipment, concat(equipments.name, " ", (ROUND(voltage_class.voltage/1000, 1)), " кВ") as equipment_with_voltage, concat( ROUND((voltage_class.voltage/1000), 1), " кВ") as voltage');
	// 	$this->db->from('schedules, complete_renovation_objects, specific_renovation_objects, type_services, equipments, voltage_class');
	// 	$this->db->where('specific_renovation_objects.id=schedules.specific_renovation_object_id');
	// 	$this->db->where('type_services.id=schedules.type_service_id');
	// 	$this->db->where('complete_renovation_objects.id=specific_renovation_objects.complete_renovation_object_id');
	// 	$this->db->where('equipments.id=specific_renovation_objects.equipment_id');
	// 	$this->db->where('voltage_class.id=specific_renovation_objects.voltage_class_id');
	// 	// $this->db->limit(5);
	// 	$query = $this->db->get();

	// 	return $query->result();
	// }

	public function change_value($field, $value, $id)
	{
		$this->db->set($field, $value === '' ? NULL : $value);
		$this->db->where('id', $id);
		$query = $this->db->update('schedules');
		return $query;
	}

	public function get_schedules($specific_renovation_object_id)
	{
		$this->db->select('schedules.*, type_services.name, type_services.short_name');
		$this->db->from('schedules');
		$this->db->join('type_services', 'type_services.id = schedules.type_service_id');
		$this->db->where('specific_renovation_object_id', $specific_renovation_object_id);
		$this->db->order_by('type_service_id', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_count()
	{
		$query = $this->db->count_all('schedules');
		return $query;
	}

	public function add_data($data)
	{
		$this->db->insert('schedules', $data);
		return $this->db->insert_id();
	}
}
