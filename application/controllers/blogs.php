<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blogs extends MY_Controller {

	
	public function get($IdBlog)
	{
		$this->load->model('BlogsModel');
		$blog = $this->BlogsModel->getById($IdBlog);
		$blog->Followers = $this->BlogsModel->getFollowers($IdBlog);
		echo json_encode($blog);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */