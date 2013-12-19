<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {


	  
	public function index($encodedEmail=false,$Email=false){
		
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		$this->load->model('UsersModel');
		
		$blogs = $this->BlogsModel->getAll();
		
		$blogs2=array();
		
		
		if($encodedEmail && $Email){
			$Email=urldecode($Email);
			$createAccount=true;
		}else{
			$createAccount=false;
		}
		
		/*
		 *Popular 1
		 *Indie 2
		 *
		 *Hip Hop 3
		 *
		 *Dance/Electronic 4
		 */
		$blogsNames = array(
			1=>"Popular",
			2=>"Indie",
			3=>"Hip Hop",
			4=>"Dance/Electronic"	
		);
		foreach($blogs as $k=>$blog){
			$blogs2[$blogsNames[$blog->Genre]][]=$blog;
		}
		
		$this->load->view('header');
		$this->load->view('main',array('blogs'=>$blogs2,'createAccount'=>$createAccount,'Email'=>$Email));
		$this->load->view('analytics');
		$this->load->view('footer');
	}
	
	public function profiles(){
		$this->index();
	}
	
	public function reset($Token,$Email){
		$this->load->model('UsersModel');
		
		if(!$this->UsersModel->checkToken($Email,$Token)){
			redirect(base_url());
		}
		
		//echo "Lets reset your password...";
		
		$this->load->view('header');
		$this->load->view('main',array('blogs'=>array(),'createAccount'=>false,'Email'=>$Email));
		$this->load->view('analytics');
		
		$this->load->view('resetPassword',array('Email'=>$Email,'Token'=>$Token));
		
		
		$this->load->view('footer');
		
		
	}
	
	public function card($type){
		if($type=="introHowto"){
			$this->load->view('introHowto');
		}elseif($type=="introStart"){
			$this->load->view('introStart');
		}elseif($type=="user"){
			$this->load->view('userCard');
		}else{
			$this->load->view('profileStream',array('type'=>$type));
		}
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */