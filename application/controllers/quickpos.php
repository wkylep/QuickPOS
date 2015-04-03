<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quickpos extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('quickpos_model');
	}
	
	public function index()
	{
		$this->load->view('quickpos');
	}
	
	public function config()
	{
		$this->config->load('quickpos');
		$this->_json($this->config->item('quickpos'));
	}
	
	public function get_services()
	{
		$this->_json($this->quickpos_model->get_services());
	}
	
	public function get_coupons()
	{
		$this->_json($this->quickpos_model->get_coupons());
	}
	
	public function get_payments()
	{
		$this->_json($this->quickpos_model->get_payments());
	}
	
	public function get_service($id)
	{
		$this->_json($this->quickpos_model->get_service_by_id($id));
	}
	
	public function get_coupon($id)
	{
		$this->_json($this->quickpos_model->get_coupon_by_id($id));
	}
	
	public function get_payment($id)
	{
		$this->_json($this->quickpos_model->get_payment_by_id($id));
	}
	
	public function complete_invoice()
	{
		$payment = $this->input->post('payment');
		$items = json_decode($this->input->post('items'));
		$r = $this->quickpos_model->complete_invoice($payment, $items);
		$this->_json($r);
		// print receipt
	}
	
	public function get_receipt_list()
	{
		$start = new DateTime("today");
		$end = new DateTime("tomorrow");
		$receipts = $this->quickpos_model->get_receipts($start, $end, TRUE);
		$this->_json($receipts);
	}
	
	public function void_receipt($id)
	{
		$this->quickpos_model->void_receipt($id);
		$this->_json('');
		// print void
	}
	
	public function open_cashdrawer()
	{
		// open cash drawer
		$this->_json('');
	}
	
	public function print_cashdrawer()
	{
		// get form data and print report
		$this->_json('');
	}
	
	public function payout()
	{
		// get form data and print payout slip
	}
}