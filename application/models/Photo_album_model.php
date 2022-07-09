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

	public function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('photo_albums');
	}

	public function get_photo_albums_for_passport($passport_id)
	{
		$this->db->select('photo_albums.*');
		$this->db->where('photos.photo_album_id = photo_albums.id');
		$this->db->where('passport_id', $passport_id);
		$this->db->limit(1);
		$query = $this->db->get('photos, photo_albums');
		return $query->result();
	}
}
