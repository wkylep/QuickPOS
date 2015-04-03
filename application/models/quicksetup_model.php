<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quicksetup_model extends MY_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_groups()
	{
		return $this->db->get('service_group')->result();
	}
	
	public function get_services()
	{
		$groups = $this->get_groups();
		$services = $this->db->get('service')->result();
		
		foreach ($groups as &$g)
		{
			$g->services = array();
			
			foreach ($services as $s)
			{
				if ($s->group == $g->id)
				{
					$g->services[] = $s;
				}
			}
		}
		
		return $groups;
	}
	
	public function get_service($id)
	{
		return $this->get_by_id('service', $id);
	}
	
	public function update_service($service)
	{
		$this->update('service', $service);
	}
	
	public function get_coupons()
	{
		$this->db->order_by('name', 'asc');
		return $this->db->get('coupon')->result();
	}
	
	public function get_group($id)
	{
		return $this->get_by_id('service_group', $id);
	}
	
	public function update_group($group)
	{
		$this->update('service_group', $group);
	}
	
	public function get_coupon($id)
	{
		return $this->get_by_id('coupon', $id);
	}
	
	public function update_coupon($coupon)
	{
		$this->update('coupon', $coupon);
	}
}