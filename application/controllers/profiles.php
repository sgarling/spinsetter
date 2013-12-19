<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profiles extends MY_Controller {


	//This two are almost the same except that one is prepeared only for retriving data whereas
	//the other one renders a view
	public function get($type,$Id){
		
		echo $type.":".$Id;
	}
	public function getSongs(){
	
	}
}
