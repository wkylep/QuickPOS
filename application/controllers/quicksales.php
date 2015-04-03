<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quicksales extends MY_Controller {
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('quicksales_model');
	}
	
	public function index()
	{
		$this->load->view('quicksales');
	}
	
	public function config()
	{
		$this->config->load('quickpos');
		$this->_json($this->config->item('quickpos'));
	}
	
	public function get_sales_report($start = "today", $end = "today")
	{
		$s = new DateTime($start);
		$e = new DateTime($end);
		$e->add(new DateInterval('P1D'));
		
		$sales = $this->quicksales_model->get_sales($s, $e);
		$this->_json($sales);
	}
	
	public function get_deposit_data($day = "today")
	{
		$s = new DateTime($day);
		$e = clone $s;
		$e->add(new DateInterval('P1D'));
		
		$payments = $this->quicksales_model->get_deposit_data($s, $e);
		$today = $day == "today" ? $this->quicksales_model->check_deposit_today() : FALSE;
		
		if ($today instanceof DateTime)
		{
			$today = $today->format("g:m a");
		}
		
		$this->_json(array('payments' => $payments, 'today' => $today));
	}
	
	public function save_deposit()
	{
		$post = $this->input->post();
		$deposit = array();
		$dt = date('Y-m-d H:i:s');
		
		foreach ($post as $k => $v)
		{
			$i = strpos($k, 'pay_');
			
			if ($i === 0)
			{
				$this->form_validation->set_rules($k, $k, 'money');
				$d = new stdClass();
				$d->payment = substr($k, 4);
				$d->amount = $v;
				$d->datetime = $dt;
				$deposit[] = $d;
			}
		}
		
		if ($this->form_validation->run())
		{
			$this->quicksales_model->save_deposit($deposit);
			$this->_json('');
		}
		else
		{
			$this->_json('', TRUE, validation_errors('&nbsp;', '<br />'));
		}
	}
	
	public function get_deposit_report($date = "today")
	{
		$dt = new DateTime($date);
		$e = clone $dt;
		$e->add(new DateInterval('P1D'));
		
		$payments = $this->quicksales_model->get_deposit_data($dt, $e);
		
		$this->_json(array('payments' => $payments, 'title' => $dt->format("l F j, Y")));
	}
}