<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends MY_Controller {


	//This two are almost the same except that one is prepeared only for retriving data whereas
	//the other one renders a view
	public function get(){
		
		$this->load->model('UsersModel');
		
		$Email = $this->input->post("Email");
		
		$user = $this->UsersModel->getByEmail($Email);
		$user->Followings = $this->UsersModel->getFollowings($user->IdUser);
		$user->Followers = $this->UsersModel->getFollowers($user->IdUser);
		
		$user->Token = $this->input->post('Token');
		
		
		echo json_encode($user);
	}
	
	//Dummy method for testing... delete it, not neccesary at all..:
	public function test($IdSong){
		$this->load->model('SongsModel');
		$song = $this->SongsModel->getById($IdSong);
		$meta = $this->SongsModel->getMetadata($song->UrlSong,$song->TypeSource,false);
		print_r($meta['UrlSource']);
	}
	public function getIntro($IdUser,$Status){
		$this->load->model('UsersModel');
		$howtoStatus = $this->UsersModel->getHowtoStatus();
		
		$cards=array();
		
		if(strpos($Status,$howtoStatus)!==false){
			$cards[] = array(
				'IdCard'=>0,
				'type'=>'introHowto',
				'status'=>$howtoStatus,
				'title'=>$howtoStatus
			);
		}
		
		
		echo json_encode($cards);
		
	}
	public function gotIt(){
		$this->load->model('UsersModel');
		
		$IdUser = $this->input->post("IdUser");
		
		$result = $this->UsersModel->gotIt($IdUser);
		
		echo json_encode($result);
	}
	public function removeIntro(){
		$this->load->model('UsersModel');
		
		$IdUser = $this->input->post("IdUser");
		$Status = $this->input->post("Status");
		
		$result = $this->UsersModel->removeStatus($IdUser,$Status);
		
		echo json_encode($result);
	
	}
	public function getFriendById($IdUser){
		$this->load->model('UsersModel');
		
		$user = $this->UsersModel->getFriendById($IdUser);
		
		$user->Followings = $this->UsersModel->getFollowings($user->IdUser);
		$user->Followers = $this->UsersModel->getFollowers($user->IdUser);
		
		
		
		echo json_encode($user);
		
	}
	public function search($Username,$limit=4){
	
		$Username = urldecode($Username);
		$this->load->model('UsersModel');
		
		echo json_encode($this->UsersModel->search($Username,$limit));
	
	}
	public function respin(){
		$this->load->model('UsersModel');
		
		$users = $this->input->post('users');
		$song = $this->input->post('song');
		$from = $this->input->post('IdUser');
		
		$result=array();
		foreach($users as $user){
			$result[]=$this->UsersModel->respinSong($song,$from,$user);
		}
		echo json_encode($result);
	
	}
	public function unRespin(){
		$this->load->model('UsersModel');
		$IdSong = $this->input->post('IdSong');
		$IdUser = $this->input->post('IdUser');
		echo json_encode($this->UsersModel->unRespinSong($IdSong,$IdUser));
	}
	public function resetPassword(){
		
		$this->load->model('UsersModel');
		
		$Email = $this->input->post('Email');
		$Password = $this->input->post('Password');
		
		$this->UsersModel->resetPassword($Email,$Password);
		
		echo json_encode(array("msg"=>"Password changed. <a href='".base_url()."'>Click here</a> to log in."));
		
	}
	public function recentActivityFriends(){
		$this->load->model('UsersModel');
		
		
		
		
		$IdUser = $this->input->post("IdUser");
		
		/*
		Show the most recent 4 friends I have shared with OR who have shared with me.
		2) Rank by most recent at the top. (This should cover the base case of when
		I am new to the site - the top of the list should be the person who invited me).
		If there are not 4 friends to display, populate the empty spot with my friends' friends.
		*/
		
		echo json_encode($this->UsersModel->recentActivityFriends($IdUser));
	}
	public function reblogSong($IdUser,$IdSong){
	
		$this->load->model('UsersModel');
		
		echo json_encode($this->UsersModel->reblogSong($IdUser,$IdSong));
	}
	public function unReblogSong($IdUser,$IdSong){
	
		$this->load->model('UsersModel');
		
		echo json_encode($this->UsersModel->unReblogSong($IdUser,$IdSong));
	}
	//When a user is invited in the respin menu
	public function invite(){
		$this->load->model('UsersModel');
                $this->load->model('SongsModel');
		$Email=$this->input->post('EmailInvite');
		
		if($Email==''){
			
			echo json_encode(array('msg'=>'Email empty.'));
			return;
		}
		
		$user=$this->UsersModel->getById($this->input->post('IdUser'));
		$song=$this->SongsModel->getById($this->input->post('IdSong'));
		
                
                //print_r($user);
                //print_r($song);
                
                
                //return;
                if(!($IdInvited=$this->UsersModel->emailInvited($Email))){
                    $IdInvited=$this->UsersModel->inviteEmail($Email);
                }
                
		       
                        
			$this->load->library('email');
			$config['mailtype'] = 'html';

			$this->email->initialize($config);
			
			$this->email->from('no-reply@spinsetter.com', 'Spinsetter');
			$this->email->to($Email); 
			$this->email->subject($user->Firstname." wants you to hear this");
			
			
			
			$message = "<html><body>
						<p>
						<img src='".base_url()."img/email-header.png'  /><br><br>
                        Check this out! <br><br>

                        ".$user->Firstname."  just sent a track for you to check out, via <a href='".base_url()."main/index/".sha1($Email)."/".$Email."'>Spinsetter</a>: <a href='".base_url()."songs/source/".$song->IdSong."'>".$song->Title."</a> This could be the last time a friend ever sends you a track by email. 
						<br><br>
                        Check out <a href='".base_url()."index/".sha1($Email)."/".$Email."'>Spinsetter</a>, where friends can share music and play music in the same place. What a wild idea. Thanks to ".$user->Firstname." ".$user->Lastname.", you can cut the line and get access 
						to <a href='".base_url()."main/index/".sha1($Email)."/".$Email."'>Spinsetter</a> now.
						<br><br>
                        Rock on,<br> 
                        Spinsetter<br>
						</p>
						</body></html>";

			
			$this->email->message($message);	

			$this->email->send();


			$this->UsersModel->respinSong($song->IdSong,$user->IdUser,$IdInvited);
                        
                        $this->UsersModel->followProfile($user->IdUser,$IdInvited,'users');
                        $this->UsersModel->followProfile($IdInvited,$user->IdUser,'users');
                        
			echo json_encode(array('msg'=>'Invitation Sent.','IdInvited'=>$IdInvited));
		
	}
	public function followProfile($IdUser,$IdFollowed,$TypeFollowed){
		$this->load->model('UsersModel');
		
		echo json_encode($this->UsersModel->followProfile($IdUser,$IdFollowed,$TypeFollowed));
	}
	public function unfollowProfile($IdUser,$IdFollowed,$TypeFollowed){
		$this->load->model('UsersModel');
		
		echo json_encode($this->UsersModel->unfollowProfile($IdUser,$IdFollowed,$TypeFollowed));
	}
	public function picture(){
		
			$this->load->helper('imageCrop');
			
			$this->load->model('UsersModel');
			
			
			imageCrop($_FILES['profilePicture']['tmp_name'], "./profilePictures/temp/".$_REQUEST['IdUser'].".jpg",1000,1000);
			
			
			echo json_encode(array('pictureUrl'=>'profilePictures/temp/'.$_REQUEST['IdUser'].".jpg"));
		
	}
	public function cropAndSavePicture(){
			
		$this->load->model('UsersModel');
		

		$width = $this->input->post("width");
		$height = $this->input->post("height");
		$x = $this->input->post("x");
		$y = $this->input->post("y");
		$relativeWidth = $this->input->post("relativeWidth");
		$relativeHeight = $this->input->post("relativeHeight");
		
		
		
		
		$src = ImageCreateFromJpeg("./profilePictures/temp/".$_REQUEST['IdUser'].".jpg");
		
		
		$srcWidth = imagesx($src);
		$srcHeight = imagesy($src);
		
                
                $dest = @ImageCreateTrueColor($srcWidth, $srcHeight);
                
		$ratioX = $srcWidth/$relativeWidth;
		$ratioY = $srcHeight/$relativeHeight;
		
		imagecopyresampled ($dest,$src, 0,0,    floor($ratioX*$x), floor($ratioY*$y) ,  $srcWidth,$srcHeight,  floor($ratioX*$width),floor($ratioY*$height));
		
                //imagecopyresampled ( dest,src, int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
                
		//imagejpeg($dest,"profilePictures/temp/".$_REQUEST['IdUser'].".jpg", 80);
		
		$this->load->library('Circlecrop', array('img'=>$dest, "dstWidth"=>imagesx($dest), "dstHeight"=>imagesy($dest) ));
		
		$this->circlecrop->crop();
		$this->circlecrop->display("profilePictures/".$_REQUEST['IdUser'].".png");  // Object instances will always be lower case

		
		$this->UsersModel->UpdateProfilePicture($_REQUEST['IdUser'],"profilePictures/".$_REQUEST['IdUser'].".png");

		
		echo json_encode(array('pictureUrl'=>'profilePictures/'.$_REQUEST['IdUser'].".png"));
		
		
	}

}
