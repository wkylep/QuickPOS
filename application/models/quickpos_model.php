<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quickpos_model extends MY_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_services()
	{
		$this->db->order_by('group', 'asc');
		$this->db->order_by('name', 'asc');
		return $this->db->get('service')->result();
	}
	
	public function get_coupons()
	{
		$this->db->order_by('name', 'asc');
		return $this->db->get('coupon')->result();
	}
	
	public function get_payments()
	{
		return $this->db->get('payment')->result();
	}
	
	public function get_service_by_id($id)
	{
		return $this->db->get_where('service', array('id' => $id), 1)->row();
	}
	
	public function get_coupon_by_id($id)
	{
		return $this->db->get_where('coupon', array('id' => $id), 1)->row();
	}
	
	public function get_payment_by_id($id)
	{
		return $this->db->get_where('payment', array('id' => $id), 1)->row();
	}
	
	public function complete_invoice($payment, $items)
	{
		$line = 0;
		$receipt = new stdClass();
		$receipt->datetime = date('Y-m-d H:i:s');
		$receipt->tax = 0;
		$receipt->total = 0;
		
		foreach ($items as $i)
		{
			$receipt->total += $i->retail;
		}
		
		$receipt->tax = round($receipt->total * 0.07, 2);
		$receipt->total += $receipt->tax;
		
		$this->db->insert('receipt', $receipt);
		$receipt->id = $this->db->insert_id();
		
		foreach ($items as $i)
		{
			$recitem = new stdClass();
			$recitem->receipt = $receipt->id;
			$recitem->line = $line;
			$line++;
			if ($i->type == "service")
			{
				$recitem->service = $i->id;
			}
			elseif ($i->type =="coupon")
			{
				$recitem->coupon = $i->id;
			}
			$recitem->description = $i->name;
			$recitem->total = $i->retail;
			$this->db->insert('receipt_item', $recitem);
		}
		
		$pmt = new stdClass();
		$pmt->receipt = $receipt->id;
		$pmt->payment = $payment;
		$pmt->amount = $receipt->total;
		$this->db->insert('receipt_payment', $pmt);
		
		// Printable Receipt Data
		$pmt_desc = $this->get_payment_by_id($payment);
		$pmt->name = $pmt_desc->name;
		$receipt->subtotal = $receipt->total - $receipt->tax;
		$receipt->payments = array($pmt);
		$receipt->items = $items;
		return $receipt;
	}
	
	public function get_receipts(DateTime $start, DateTime $end, $payment = FALSE)
	{
		$receipts = $this->db->get_where('receipt', array('datetime >=' => $start->format('Y-m-d'), 'datetime <=' => $end->format('Y-m-d')))->result();
		
		if ($payment)
		{
			foreach ($receipts as &$r)
			{
				$r->payment = $this->db->get_where('receipt_payment', array('receipt' => $r->id))->result();
				$r->void = $r->void ? TRUE : FALSE;
			}
		}
		
		return $receipts;
	}
	
	public function void_receipt($id)
	{
		$this->db->update('receipt', array('void' => '1'), array('id' => $id), 1);
	}
}