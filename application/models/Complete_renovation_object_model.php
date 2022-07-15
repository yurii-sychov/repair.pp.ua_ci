<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Complete_renovation_object_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('complete_renovation_objects');
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_sp()
	{
		$this->db->select('*');
		$this->db->from('complete_renovation_objects');
		$this->db->where('subdivision_id', 1);
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_subdivision($subdivision_id)
	{
		$this->db->select('complete_renovation_objects.*');
		$this->db->from('complete_renovation_objects, specific_renovation_objects, passports');
		$this->db->where('complete_renovation_objects.id = passports.complete_renovation_object_id');
		$this->db->where('specific_renovation_objects.id = passports.specific_renovation_object_id');
		$this->db->where('specific_renovation_objects.equipment_id', 19);
		$this->db->where('complete_renovation_objects.subdivision_id', $subdivision_id);
		$this->db->order_by('complete_renovation_objects.name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_for_user()
	{
		$this->db->select('*');
		$this->db->from('complete_renovation_objects');
		$this->db->join('users_complete_renovation_objects', 'complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->order_by('name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_data_with_subdivision_for_user()
	{
		$this->db->select('complete_renovation_objects.*, subdivisions.name as subdivision');
		$this->db->from('complete_renovation_objects, subdivisions');
		$this->db->join('users_complete_renovation_objects', 'complete_renovation_objects.id = users_complete_renovation_objects.object_id');
		$this->db->where('complete_renovation_objects.subdivision_id = subdivisions.id');
		$this->db->where('users_complete_renovation_objects.user_id', $this->session->user->id);
		$this->db->order_by('complete_renovation_objects.name', 'asc');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_row($id)
	{
		$this->db->from('complete_renovation_objects');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}
}
