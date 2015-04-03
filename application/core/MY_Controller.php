<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function _json($data, $error = 0, $message = '')
	{
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(
			array(
				'error_code' => $error,
				'message' => $message,
				'content' => $data
			)
		));
	}
}
