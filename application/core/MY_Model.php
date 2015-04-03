<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function update($table, $item, $field = 'id')
	{
		if ($item->$field)
		{
			$this->db->update($table, $item, array($field => $item->$field), 1);
		}
		else
		{
			unset($item->$field);
			$this->db->insert($table, $item);
		}
	}
	
	public function get_by_id($table, $id, $field = 'id')
	{
		return $this->db->get_where($table, array($field => $id), 1)->row();
	}
}
