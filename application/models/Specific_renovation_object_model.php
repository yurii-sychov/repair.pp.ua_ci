<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Specific_renovation_object_Model extends CI_Model
{

	public function add_data($data)
	{
		$this->db->insert('specific_renovation_objects', $data);
		return $this->db->insert_id();
	}

	public function is_specific_renovation_object($complete_renovation_object_id, $name, $equipment_id)
	{
		$this->db->select('*');
		$this->db->where('complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where('name', $name);
		$this->db->where('equipment_id', $equipment_id);
		$query = $this->db->get('specific_renovation_objects');
		return $query->row();
	}

	public function get_specific_renovation_object($id)
	{
		$this->db->select('specific_renovation_objects.*');
		$this->db->from('specific_renovation_objects');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_search($name, $equipment_id)
	{
		$this->db->select('specific_renovation_objects.*, complete_renovation_objects.name as complete_renovation_object, equipments.name as equipment, voltage_class.voltage');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, equipments, voltage_class');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id=complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id=equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id=voltage_class.id');
		$this->db->like('specific_renovation_objects.name', $name);
		$this->db->like('specific_renovation_objects.equipment_id', $equipment_id);
		$this->db->order_by('specific_renovation_objects.id', 'ASC');
		// $this->db->limit(10);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_specific_renovation_object_full($id)
	{
		$this->db->select('specific_renovation_objects.*, complete_renovation_objects.name as stantion, equipments.name as equipment, voltage_class.voltage as voltage, passports.type, passports.specific_renovation_object_id, passports.id as donor_passport_id');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, equipments, voltage_class, passports');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id = equipments.id');
		$this->db->where('specific_renovation_objects.voltage_class_id = voltage_class.id');
		$this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_specific_renovation_object_for_copy($equipment_id, $type, $specific_renovation_object_id, $complete_renovation_object_id)
	{
		$this->db->select('passports.id, passports.type, specific_renovation_objects.name as disp, complete_renovation_objects.name as stantion');
		$this->db->from('specific_renovation_objects, complete_renovation_objects, passports');
		$this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.complete_renovation_object_id = complete_renovation_objects.id');
		$this->db->where('specific_renovation_objects.equipment_id', $equipment_id);
		$this->db->where('passports.type', $type);
		$this->db->where('passports.complete_renovation_object_id', $complete_renovation_object_id);
		$this->db->where_not_in('passports.specific_renovation_object_id', $specific_renovation_object_id);
		$query = $this->db->get();
		return $query->result();
	}
}
