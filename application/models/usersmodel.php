<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define("HOWTOSTATUS",'howto');

define("DELETED",'deleted');
define("INVITED",'invited');
define("FIRSTLOGIN",HOWTOSTATUS);
define("REGISTERED",'registered');



class UsersModel extends CI_Model {
    
	var $IdUser = '';
	var $Username = '';
	var $Location ='';
    var $SpinsetterHQId = 54;
	var $defaultBlogs = array(102,3,100,71);
	var $StartNowSong = 25091;
	
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    private function getUsersTable(){
		return 'users';
	}
	private function getReblogTable(){
		return 'songsusers';
	}
	private function getFollowingsTable(){
		return 'followings';
	}
	public function getHowtoStatus(){
		return HOWTOSTATUS;
	}
	public function getStartStatus(){
		return STARTSTATUS;
	}
	public function getInvitedStatus(){
		return INVITED;
	}
	
	function searchByUsername($Username,$limit=4,$offset=0){
		$this->db->select('Username,Email,Firstname,Lastname,IdUser,ProfilePicture');
		$this->db->where('Username LIKE "%'.$Username.'%" AND Token IS NOT NULL AND IdUser!="'.$this->SpinsetterHQId.'"');
		$query = $this->db->get($this->getUsersTable(),$limit,$offset);
		
                $result = $query->result();
		
		return $result;
	}
	function search($q,$limit=4,$offset=0){
		$this->db->select('Username,Email,Firstname,Lastname,IdUser,ProfilePicture');
		$this->db->where('(Username LIKE "%'.$q.'%" OR Email LIKE "%'.$q.'%" OR Firstname LIKE "%'.$q.'%" OR Lastname LIKE "%'.$q.'%" OR CONCAT(Firstname," ",Lastname) LIKE "%'.$q.'%")AND Token IS NOT NULL AND IdUser!="'.$this->SpinsetterHQId.'"');
		$query = $this->db->get($this->getUsersTable(),$limit,$offset);
		
                $result = $query->result();
		
		return $result;
	}
	
	function setUserStatus($IdUser,$status){
		$this->db->where('IdUser', $IdUser);
		$this->db->update('users',array("Status"=>$status));
		return (object)array('IdUser'=>$IdUser,'Status'=>$status);
		
	}
        function getUserStatus($IdUser){
		$this->db->where('IdUser', $IdUser);
                $this->db->select('Status');
		$query = $this->db->get('users');
                
                $result = $query->result();
                
		return $result[0]->Status;
		
	}
	
	function gotIt($IdUser){
		
		$status=REGISTERED;
		
		$this->setUserStatus($IdUser,$status);
		
		return (object)array('IdUser'=>$IdUser,'status'=>$status);
	}
    function getById($IdUser)
    {
		
		$query = $this->db->get_where($this->getUsersTable(), array('IdUser'=>$IdUser));
		
                $result = $query->result();
		$result = $result[0];
		return $result;
    }
    function getFriendById($IdUser){
		$this->db->select("IdUser,Username,Email,Firstname,Lastname,ProfilePicture,Status");
		$query = $this->db->get_where($this->getUsersTable(), array('IdUser'=>$IdUser));
		
                $result = $query->result();
		
		if(isset($result[0])){
			$result = $result[0];
			
			return $result;
		}
		
		return false;
    }
	function getByUsername($Username)
    {
		
		$query = $this->db->get_where($this->getUsersTable(), array('Username'=>$Username));
		
        $result = $query->result();
		$result = $result[0];
		return $result;
		
    }
	function getByEmail($Email)
    {
		$this->db->select("IdUser,Username,Email,Firstname,Lastname,ProfilePicture,Status,Token");
		
		$query = $this->db->get_where($this->getUsersTable(), array('Email'=>$Email));
		
		$result = $query->result();
		$result = $result[0];
		return $result;
		
    }
	function getFollowings($IdUser){

		$this->db->select("followings.*,(IF(blogs.NameBlog IS NULL,users.Firstname,blogs.NameBlog )) as Name");
		$this->db->join('blogs',"followings.IdFollowed=blogs.IdBlog AND followings.TypeFollowed='blogs'",'left');
		$this->db->join('users',"followings.IdFollowed=users.IdUser AND followings.TypeFollowed='users'",'left');
		$query = $this->db->get_where($this->getFollowingsTable(), array('followings.IdFollower'=>$IdUser));
		
        return $query->result();
	}
	function getFollowers($IdUser){

		$this->db->select("followings.*,(IF(blogs.NameBlog IS NULL,users.Firstname,blogs.NameBlog )) as Name");
		$this->db->join('blogs',"followings.IdFollower=blogs.IdBlog AND followings.TypeFollowed='blogs'",'left');
		$this->db->join('users',"followings.IdFollower=users.IdUser AND followings.TypeFollowed='users'",'left');
		$query = $this->db->get_where($this->getFollowingsTable(), array('followings.IdFollowed'=>$IdUser));
		
		return $query->result();
	}
	function respinSong($IdSong,$IdUserFrom,$IdUserTo){
		
		$query = $this->db->get_where('respins', array('IdSong'=>$IdSong,'IdUserFrom'=>$IdUserFrom,'IdUserTo'=>$IdUserTo));
		
        $result = $query->result();
		$c = $query->num_rows();
		if($c==0){
			$this->db->insert('respins', (object)array('IdSong'=>$IdSong,'IdUserFrom'=>$IdUserFrom,'IdUserTo'=>$IdUserTo,'Date'=>date('Y-m-d H:i:s')));
			return (object)array('IdSong'=>$IdSong,'IdUserFrom'=>$IdUserFrom,'IdUserTo'=>$IdUserTo,'Date'=>date('Y-m-d H:i:s'));
		}else{
			return false;
		}
	
	}
        private function respinSongAll($IdSong,$IdUserFrom){
		
                
                /*
                 SELECT   '1234' AS IdSong ,
                '54' AS IdUserFrom, , users.IdUser AS IdUserTo, NOW() AS Date, '1' AS Status
                FROM users
                WHERE users.IdUser !=  '54'
                */
                //We should avoid raw querying, instead we should try to use active record class for all queries...
		$query = $this->db->query("INSERT respins (IdSong,IdUserFrom,IdUserTo,Date,Status)
                            SELECT   '".$IdSong."' AS IdSong ,
                            '".$IdUserFrom."' AS IdUserFrom, u.IdUser AS IdUserTo, NOW() AS Date, '1' AS Status
                            FROM users u
                            WHERE u.IdUser !=  '".$IdUserFrom."'
                           ");
                           
		return true;
			
	}
	function unRespinSong($IdSong,$IdUser){
		
		
		$this->db->where(array('IdSong'=> $IdSong,'IdUserTo'=>$IdUser));
		$this->db->update('respins',array("Status"=>"2"));
		return (object)array('IdSong'=>$IdSong,'Status'=>"2");
        
		
	}
	function recentActivityFriends($IdUser,$limit=4,$offset=0){
		
		//Cant we do just 1 query?...improve this...
		
		$this->db->select('respins.*');
		$this->db->order_by('respins.Date','DESC');
		$where = "(respins.IdUserTo = ".$IdUser." OR respins.IdUserFROM = ".$IdUser.") AND respins.Status > 0 AND respins.IdUserFrom!='".$this->SpinsetterHQId."'";

		$this->db->where($where);
		
		
                if(!$limit){
                    $query = $this->db->get('respins');
                }else{
                    $query = $this->db->get('respins',$limit,$offset);
                }
                
                $result = $query->result();
		
		$friends = array();
		$friends_id = array();
		
		
		foreach($result as $r){
			
			if($r->IdUserTo == $IdUser){
				$id=$r->IdUserFrom;
				
			}else{
				$id = $r->IdUserTo;
			}
			if(!isset($friends_id[$id])){
				$friends_id[$id]=true;
				
				$friend = $this->getFriendById($id);
				if(!$friend){
					continue;
				}
				if($friend->Status == INVITED || $friend->Status == DELETED){
					continue;
				}
				$friends[]=$friend;
			}
		}
		
		return $friends;
		
	}
	function reblogSong($IdSong,$IdUser){
                
                if($IdUser == $this->SpinsetterHQId){
                    //Respin to all users:
                    //$this->respinSong($IdSong,$IdUserFrom,$IdUserTo)
                    $this->respinSongAll($IdSong,$IdUser);
                }
		
		$query = $this->db->get_where($this->getReblogTable(), array('IdSong'=>$IdSong,'IdUser'=>$IdUser));
		
                $result = $query->result();
		$c = $query->num_rows();
		if($c==0){
			$this->db->insert($this->getReblogTable(), (object)array('IdSong'=>$IdSong,'IdUser'=>$IdUser,'PubDate'=>date('Y-m-d H:i:s')));
			return (object)array('IdSong'=>$IdSong,'IdUser'=>$IdUser,'PubDate'=>date('Y-m-d H:i:s'));
		}else{
			return false;
		}
        
		
	}
	function unReblogSong($IdSong,$IdUser){
		
        return $this->db->delete($this->getReblogTable(), array('IdSong'=>$IdSong,'IdUser'=>$IdUser));
		
	}
	function followProfile($IdUser,$IdFollowed,$TypeFollowed){
	
		if(!$this->checkExistUser($IdUser)){
			return false;
		}
		$query = $this->db->get_where($this->getFollowingsTable(), array('IdFollower'=>$IdUser,'IdFollowed'=>$IdFollowed,'TypeFollowed'=>$TypeFollowed));
		
        $result = $query->result();
		$c = $query->num_rows();
		if($c==0){
			if($TypeFollowed =='users' && !$this->checkExistUser($IdFollowed)){
				return false;
			}
			$this->db->insert($this->getFollowingsTable(), (object)array('IdFollower'=>$IdUser,'IdFollowed'=>$IdFollowed,'TypeFollowed'=>$TypeFollowed));
			
			if($TypeFollowed=='blogs'){
				$this->db->select('NameBlog AS Name');
				$query = $this->db->get_where('blogs', array('IdBlog'=>$IdFollowed));
				$result = $query->result();
				$name = $result[0]->Name;
			}elseif($TypeFollowed=='users'){
				$this->db->select('Username AS Name');
				$query = $this->db->get_where('users', array('IdUser'=>$IdFollowed));
				$result = $query->result();
				$name = $result[0]->Name;
			}else{
				return false;
			}
			
			
			return (object)array('IdFollower'=>$IdUser,'IdFollowed'=>$IdFollowed,'TypeFollowed'=>$TypeFollowed,'Name'=>$name);
		}else{
			return false;
		}
	}
	function unfollowProfile($IdUser,$IdFollowed,$TypeFollowed){
		$this->db->delete($this->getFollowingsTable(),array('IdFollower'=>$IdUser,'IdFollowed'=>$IdFollowed,'TypeFollowed'=>$TypeFollowed));
		return (object)array('IdFollower'=>$IdUser,'IdFollowed'=>$IdFollowed,'TypeFollowed'=>$TypeFollowed);
	}
	
	public function login($Email,$Password){
		
		$query = $this->db->get_where($this->getUsersTable(), array('Email'=>$Email,'Password'=>sha1($Password)));
		if($query->num_rows()>0){
			
			$result = $query->result();
			$result = $result[0];
			
			$token = $this->generateToken();
			
			$this->db->where('IdUser', $result->IdUser);
			$this->db->update($this->getUsersTable(), (object)array('Token'=>$token,'TimeToken'=>date('Y-m-d H:i:s')));
			
			return $token;
		}
		return false;
	}
	public function resetPassword($Email,$Password){
		$this->db->where('Email', $Email);
		$query = $this->db->update($this->getUsersTable(), (object)array(
		'Password'=>sha1($Password)
		
		));
		
		return true;
	}
	public function checkToken($Email,$Token){
		$query = $this->db->get_where($this->getUsersTable(), array('Email'=>$Email,'Token'=>$Token));
		if($query->num_rows()>0){
			return true;
		}else{
			return false;
		}
		
	}
	public function getTokenByEmail($Email){
		$query = $this->db->get_where($this->getUsersTable(), array('Email'=>$Email));
		if($query->num_rows()>0){
			$result = $query->result();
			$result = $result[0];
			return $result->Token;
		}else{
			return false;
		}
	}
	private function generateToken(){
		return md5(microtime().rand(100, 999));
	}
	public function emailInvited($Email){
		$this->db->select('IdUser');
		$query = $this->db->get_where($this->getUsersTable(), array('Username'=>$Email,'Email'=>$Email));
		if($query->num_rows()>0){
			$result = $query->result();
			$result = $result[0];
			return $result->IdUser;
		}else{
			return false;
		}
	}
	public function checkExistEmail($Email){
		$query = $this->db->get_where($this->getUsersTable(), array('Email'=>$Email));
		if($query->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	public function checkExistUsername($Username){
		$query = $this->db->get_where($this->getUsersTable(), array('Username'=>$Username));
		if($query->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	public function checkExistUser($IdUser){
		$query = $this->db->get_where($this->getUsersTable(), array('IdUser'=>$IdUser));
		if($query->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
	public function register($user){
		
		$this->db->where('IdUser', $user->IdUser);
		$query = $this->db->update($this->getUsersTable(), (object)array(
		'Username'=>$user->Username,
		'Password'=>sha1($user->Password),
		'Email'=>$user->Email,
		'Firstname'=>$user->Firstname,
		'Lastname'=>$user->Lastname,
		'Status'=>FIRSTLOGIN
		));
		
		
		
		//54 is the SpinsetterHQ user. Change this hardcoded value...
		$this->followProfile($user->IdUser,$this->SpinsetterHQId,'users');
		//
		//Folow 3 profiles more dictated by samora...
		foreach($this->defaultBlogs as $b){
			$this->followProfile($user->IdUser,$b,'blogs');
		}
		//Respin the default Start Now song
		$this->respinSong($this->StartNowSong,$this->SpinsetterHQId,$user->IdUser);
		
		return $query;
		
	}
	public function inviteEmail($Email){
		if($this->checkExistEmail($Email)){
			return false;
		}else{
			$this->db->insert($this->getUsersTable(), (object)array('Username'=>$Email,'Email'=>$Email,'STATUS'=>INVITED));
			
                        return $this->db->insert_id();
		}
		
		
	}
	
	public function updateProfilePicture($IdUser,$ProfilePicture){
		
		
		$this->db->where('IdUser', $IdUser);
		$this->db->update($this->getUsersTable(),array("ProfilePicture"=>$ProfilePicture)); 
		
		return true;
	
	}
	
	
	
	
	
	
	
	
	
} 