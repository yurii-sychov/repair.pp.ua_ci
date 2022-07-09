<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Type_service_Model extends CI_Model
{

	public function get_data()
	{
		$this->db->select('*');
		$this->db->from('type_services');
		$query = $this->db->get();
		return $query->result();
	}
}
