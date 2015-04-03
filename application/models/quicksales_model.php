<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Quicksales_model extends MY_Model {
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function get_sales(DateTime $start, DateTime $end)
	{
		$s = $start->format("Y-m-d");
		$e = $end->format("Y-m-d");
		
		// Sales Totals & Counts
		$sql = "SELECT COUNT(id) AS count,SUM(tax) AS tax,SUM(total) AS total FROM receipt WHERE datetime >= ? AND datetime < ? AND void = FALSE";
		$sales = $this->db->query($sql, array($s, $e))->row();
		
		if ( ! $sales)
		{
			return FALSE;
		}
		
		$sql = "SELECT COUNT(id) AS voids FROM receipt WHERE datetime >= ? AND datetime < ? AND void = TRUE";
		$sales->void = $this->db->query($sql, array($s, $e))->row()->voids;
		
		// Service Grouped Detail
		$sql = "SELECT description,service,COUNT(receipt_item.id) AS count,SUM(receipt_item.total) AS total FROM receipt,receipt_item WHERE receipt.datetime >= ? AND receipt.datetime < ? AND  receipt.void = FALSE AND receipt_item.receipt = receipt.id AND receipt_item.service != 0 GROUP BY receipt_item.service";
		$sales->services = $this->db->query($sql, array($s, $e))->result();
		$sales->service_total = 0;
		
		foreach ($sales->services as $i)
		{
			$sales->service_total += $i->total;
		}
		
		// Coupon Grouped Detail
		$sql = "SELECT description,coupon,COUNT(receipt_item.id) AS count,SUM(receipt_item.total) AS total FROM receipt,receipt_item WHERE receipt.datetime >= ? AND receipt.datetime < ? AND  receipt.void = FALSE AND receipt_item.receipt = receipt.id AND receipt_item.coupon != 0 GROUP BY receipt_item.coupon";
		$sales->coupons = $this->db->query($sql, array($s, $e))->result();
		$sales->coupon_total = 0;
		$sales->coupon_count = 0;
		
		foreach ($sales->coupons as $i)
		{
			$sales->coupon_total += $i->total;
			$sales->coupon_count += $i->count;
		}
		
		// Summary Data
		$sales->net_average = $sales->count ? round(($sales->total - $sales->tax) / $sales->count, 2) : 0;
		$sales->coupon_average = $sales->coupon_count ? round($sales->coupon_total / $sales->coupon_count, 2) : 0;
		
		return $sales;
	}
	
	public function get_deposit_data(DateTime $start, DateTime $end)
	{
		$s = $start->format("Y-m-d");
		$e = $end->format("Y-m-d");
		
		$sql = "SELECT payment,SUM(amount) as total FROM receipt,receipt_payment WHERE receipt.datetime >= ? AND receipt.datetime < ? AND receipt.void = FALSE AND receipt_payment.receipt = receipt.id GROUP BY payment";
		$sales = $this->db->query($sql, array($s, $e))->result();
		
		$sql = "SELECT payment,SUM(amount) as total FROM deposit WHERE datetime >= ? AND datetime < ? GROUP BY payment";
		$deposits = $this->db->query($sql, array($s, $e))->result();
		
		$payments = $this->db->get('payment')->result();
		
		foreach ($payments as &$p)
		{
			$p->total = 0;
			
			foreach ($sales as $s)
			{
				if ($s->payment == $p->id)
				{
					$p->total = $s->total;
					break;
				}
			}
			
			foreach ($deposits as $d)
			{
				if ($d->payment == $p->id)
				{
					$p->deposit = $d->total;
					break;
				}
			}
		}
		
		return $payments;
	}
	
	public function save_deposit($deposit)
	{
		foreach ($deposit as $d)
		{
			if ($d->amount != 0)
			{
				$this->db->insert('deposit', $d);
			}
		}
	}
	
	public function check_deposit_today()
	{
		$s = new DateTime('today');
		$e = new DateTime('tomorrow');
		$sql = "SELECT `datetime` FROM deposit WHERE datetime >= ? AND datetime < ? LIMIT 1";
		$r = $this->db->query($sql, array($s->format("Y-m-d"), $e->format("Y-m-d")))->row();
		
		if ($r != FALSE)
		{
			$r = new DateTime($r->datetime);
		}
		
		return $r;
	}
}