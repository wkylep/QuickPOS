<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Extending the default Input class to support the is_post method.
 */
class MY_Input extends CI_Input {

	function __construct()
	{
		parent::__construct();
	}
	
	/* @return bool Returns true if request method is POST. */
	function is_post()
	{
		$method = $this->server('REQUEST_METHOD');
		
		return (strtolower($method) == 'post');
	}
}