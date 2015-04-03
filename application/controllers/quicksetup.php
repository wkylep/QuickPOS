<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quicksetup extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('quicksetup_model');
	}
	
	public function index()
	{
		$this->load->view('quicksetup');
	}
	
	public function config()
	{
		$this->config->load('quickpos');
		$this->_json($this->config->item('quickpos'));
	}
	
	public function get_services()
	{
		$groups = $this->quicksetup_model->get_services();
		$coupons = $this->quicksetup_model->get_coupons();
		
		$this->_json(array('groups' => $groups, 'coupons' => $coupons));
	}
	
	public function get_group($id)
	{
		$group = $this->quicksetup_model->get_group($id);
		
		$this->_json(array('group' => $group));
	}
	
	public function save_group()
	{
		$group = new stdClass();
		$group->id = $this->input->post('id');
		$group->name = $this->input->post('name');
		$this->quicksetup_model->update_group($group);
		$this->_json('');
	}
	
	public function get_service($group, $id)
	{
		$groups = $this->quicksetup_model->get_groups();
		$service = $id ? $this->quicksetup_model->get_service($id) : FALSE;
		
		if ($service)
		{
			$group = $service->group;
		}
		
		$this->_json(array('service' => $service, 'groups' => $groups, 'group' => $group));
	}
	
	public function save_service()
	{
		$service = new stdClass();
		$service->id = $this->input->post('id');
		$service->group = $this->input->post('group');
		$service->name = $this->input->post('name');
		$service->retail = $this->input->post('retail');
		$service->edit = $this->input->post('edit');
		$this->quicksetup_model->update_service($service);
		$this->_json('');
	}
	
	public function get_coupon($id)
	{
		$coupon = $this->quicksetup_model->get_coupon($id);
		
		$this->_json(array('coupon' => $coupon));
	}
	
	public function save_coupon()
	{
		$coupon = new stdClass();
		$coupon->id = $this->input->post('id');
		$coupon->name = $this->input->post('name');
		$coupon->amount = $this->input->post('amount');
		$coupon->taxable = TRUE;
		$this->quicksetup_model->update_coupon($coupon);
		$this->_json('');
	}
	
	public function get_store_info()
	{
		$this->_json('');
	}
}