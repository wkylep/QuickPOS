<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
	
	public function __construct($rules = array())
	{
		parent::__construct($rules);
	}
	
	public function money($str)
	{
		$this->set_message('money', 'The %s field must contain a decimal number.');
		return (bool) preg_match('/^[0-9]+(?:\.[0-9]{1,2})?$/', $str);
		//return (bool) preg_match('/\b\d{1,3}(?:,?\d{3})*(?:\.\d{2})?\b/', $str);
	}
}