<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct()
       {
            parent::__construct();
            // Your own constructor code
			$this->load->model('UsersModel');
			
			$Token = $this->input->post('Token');
			$Email = $this->input->post('Email');
			
			if(!$this->UsersModel->checkToken($Email,$Token)){
				die(json_encode(array('error'=>'Bad token')));
			}
       }


}