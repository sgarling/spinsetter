<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BlogsModel extends CI_Model {
    
	var $IdBlog = '';
	var $NameBlog   = '';
    var $UrlBlog = '';
    var $FeedBlog    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    private function getFollowingsTable(){
		return 'followings';
	}
    function getAll()
    {
        $this->db->order_by("Genre,NameBlog", "asc");
		
		//This is baddly hardcoded to consolidate Pitchfork,Disco Naivete and This Song is Sick... :S  :S 
		$where = 'Genre IS NOT NULL AND IdBlog NOT IN("2","5","6")';
		$this->db->where($where);
		
		$query = $this->db->get_where('blogs',array('blogs.Status'=>'1'));
		
        return $query->result();
    }
	function getById($IdBlog){
		
		$query = $this->db->get_where('blogs', array('IdBlog' => $IdBlog));
		$result = $query->result();
		$result = $result[0];
		return $result;
	}
	function getFollowers($IdBlog){

		$this->db->select("followings.*,(IF(blogs.NameBlog IS NULL,users.Username,blogs.NameBlog )) as Name");
		$this->db->join('blogs',"followings.IdFollower=blogs.IdBlog AND followings.TypeFollowed='blogs'",'left');
		$this->db->join('users',"followings.IdFollower=users.IdUser AND followings.TypeFollowed='users'",'left');
		$query = $this->db->get_where($this->getFollowingsTable(), array('followings.IdFollowed'=>$IdBlog,'blogs.Status'=>'1'));
		
		return $query->result();
	}
    function insert($url,$name,$feed)
    {
        $this->UrlBlog   = $url;
        $this->NameBlog = $name;
        $this->FeedBlog    = $feed;

        $this->db->insert('blogs', $this);
		
    }


} 