<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EmailModel extends CI_Model {
	var $Mandrill = null;
	var $MandrillKey = "OwsblBeLxNwKnOQ61QDr8g";
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		
		
		require_once(APPPATH.'libraries/mandrill-api-php/src/Mandrill.php');
		
		
		try {
			$this->mandrill = new Mandrill($this->MandrillKey);
		} catch(Mandrill_Error $e) {
			
			// A mandrill error occurred: Mandrill_Invalid_Key - Invalid API key
			throw $e;
		}
    }
	
	function ping(){
	
		try {
			$result = $this->Mandrill->users->ping();
			return $result;
			/*
			PONG!*/
		} catch(Mandrill_Error $e) {
			// Mandrill errors are thrown as exceptions
			return $e;
		}
	
	}
   
	
	
	
	
	
	
	
	
} 