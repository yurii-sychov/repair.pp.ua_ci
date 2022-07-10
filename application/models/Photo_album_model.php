<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Photo_album_Model extends CI_Model
{
	public function insert_data($data)
	{
		$this->db->insert('photo_albums', $data);
		return $this->db->insert_id();
	}

	public function get_row($id)
	{
		$this->db->select('*');
		$this->db->where('id', $id);
		$query = $this->db->get('photo_albums');
		return $query->row();
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('photo_albums');
	}
}
