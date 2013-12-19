<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function get(){
		
		$this->load->model('UsersModel');
		
		$Email=$this->input->post('Email');
		$Password=$this->input->post('Password');
		
		
		//Maybe we shouldnt retrieve the password
		echo json_encode(array('Email'=>$Email/*,'Password'=>$Password*/,'Token'=>$this->UsersModel->login($Email,$Password)));
		
		
	}
	public function test(){
		
		echo json_encode($_REQUEST);
	}
	
	//When the user request an invitation
	public function signup(){
		
		$this->load->model('UsersModel');
	
		$Email=$this->input->post('Email');
		
		if($this->UsersModel->checkExistEmail($Email)){
			echo json_encode(array('msg'=>'Email '.$Email.' already registered.'));
		}else{
			
			
			
			
			$this->load->library('email');
                        
                        
                        //Send an email to the new user telling to wait
                        
			$config['mailtype'] = 'html';
			
			$this->email->initialize($config);
			
                        $bcc = "smgarling@gmail.com";
                        
			$this->email->from('no-reply@spinsetter.com', 'Spinsetter');
			$this->email->to($Email); 
                        //$this->email->bcc($bcc); 
			$this->email->subject(' Nice to meet you too!');
						
			$message ="<html><body>
						<p><img src='".base_url()."img/email-header.png'  /><br><br>
						We can&rsquo;t wait to show you Spinsetter and hear what you think. <br><br>

						However, like a packed and sweaty nightclub, it&rsquo;s no fun for anyone if we let you in before there&rsquo;s room. We&rsquo;re letting people in on a constant but limited basis, so look for an email from us soon! If you have a friend who&rsquo;s already on Spinsetter, ask them to share a song with you from the site right now, and you can cut the line. 
						<br><br>
						See you soon,<br>
						Spinsetter<br>
						</p></body></html>
						";
			
			
			
			$this->email->message($message);	

			$this->email->send();
			
                        
                        
                        //Send an email for being approved
                        $this->email->from('no-reply@spinsetter.com', 'Spinsetter');
			$this->email->to($bcc);
			$this->email->subject('A new user invitation Request for Spinsetter');
						
			$message ="<html><body>
						<p><img src='".base_url()."img/email-header.png'  /><br><br>
						".$Email." wants to be part of Spinsetter comunity.<br><br>
                                                <a href='".base_url()."login/accept/".urlencode($Email)."'>Approve it</a> .    
						<br><br>
						Spinsetter<br>
						</p></body></html>
						";
			
			
			
			$this->email->message($message);	

			$this->email->send();
                        
			echo json_encode(array('msg'=>"Thanks! You're now on the list."));
		}
	
	}
	public function resetRequest(){
		$this->load->model('UsersModel');
		
		$Email=$this->input->post('Email');
		
		if(!$this->UsersModel->checkExistEmail($Email)){
			echo json_encode(array('msg'=>"Email not registered."));
		}else{
			$Token = $this->UsersModel->getTokenByEmail($Email);
			
			$msg = "
			<html><body>
			<p><img src='".base_url()."img/email-header.png'  /><br><br>
			Hey,
			<br><br>
			It looks like you forgot your password. Don't worry though, we've got you covered. Just <a href='".base_url()."main/reset/".$Token."/".$Email."'>click this link</a> and you'll be good to go.
			<br><br>
			See you again soon,<br>
			Spinsetter<br>
			</p></body></html>
			";
			
			
			$this->load->library('email');
				
				
			//Send an email to the new user telling to wait
				
			$config['mailtype'] = 'html';
				
			$this->email->initialize($config);
				
				
			$this->email->from('no-reply@spinsetter.com', 'Spinsetter');
			$this->email->to($Email);
			
			$this->email->subject('Spinsetter Password Reset');
					
			$this->email->message($msg);	
	
			$this->email->send();
	
			
			echo json_encode(array('msg'=>"Reset Password Email sent to:".$Email));
		}
	}
	//When Samora accept the user
	public function accept($Email){
		
		$this->load->model('UsersModel');
		
		$Email = urldecode($Email);
		
		if(!$this->UsersModel->inviteEmail($Email)){
			echo json_encode(array('msg'=>'Email already registered.'));
		}else{
			$this->load->library('email');
			$config['mailtype'] = 'html';

			$this->email->initialize($config);
			
			$this->email->from('no-reply@spinsetter.com', 'Spinsetter');
			$this->email->to($Email); 

			$this->email->subject('Come on in!');
			
			
			$message = "<html><body>
                                    <p><img src='".base_url()."img/email-header.png'  /><br><br>
						Hey,
						<br><br>
						A spot just opened up for you at <a href='".base_url()."main/index/".sha1($Email)."/".$Email."'>Spinsetter</a>. Come join the party before everyone finds out!
						<br><br>
						See you inside,<br>
						Spinsetter<br>
						</p></body></html>
			";
		

			
			$this->email->message($message);	

			$this->email->send();

			
			print_r("Email accepted! An invitation with a link for creating an account has been sent to:".$Email);
			
			
			
		}
	
	}
	
	//When the user finally create the user
	public function create(){
		$this->load->model('UsersModel');
		
		$user=(object)array();
		$user->Username=$this->input->post('Email');
		$user->Password=$this->input->post('Password');
		$user->Email=$this->input->post('Email');
		$user->Firstname=$this->input->post('Firstname');
		$user->Lastname=$this->input->post('Lastname');
		
		$user->IdUser=$this->UsersModel->emailInvited($user->Email);
		if(!$user->IdUser || $this->UsersModel->getUserStatus($user->IdUser)!=$this->UsersModel->getInvitedStatus()){
			
			echo json_encode(array('error'=>'Email not invited.'));
			return false;
		}
		
		
		
		$this->UsersModel->register($user);
		
		
                
		
		//Email
			$this->load->library('email');
			$config['mailtype'] = 'html';

			$this->email->initialize($config);
			
			$this->email->from('no-reply@spinsetter.com', 'Spinsetter');
			$this->email->to($user->Email); 
			//$this->email->bcc("nachourpi@hotmail.com");
			$this->email->subject('Welcome to Spinsetter!');
			
			
			$message = "<html><body>
                                                <p>
						<img src='".base_url()."img/email-header.png'  /><br><br>
						Welcome to the Spinsetter party, it&acute;s great to have you. We supply the house, the furniture, the speakers and the red cups. You supply the awesome. 
						<br><br>
						If you want to invite a friend (no more than 5), it&acute;s easy - just share a song with them, and we&acute;ll email them both the song and the invite. Have fun and crank the tunes. Spinsetter starts now!.
						<br><br>
						PS: We are in beta and we want to hear what we can be doing better. Just reply to <a href='mailto:feedback@spinsetter.com'>feedback@spinsetter.com</a> with what gripes and excites you.<br>
						</p></body></html>
			";
			$this->email->message($message);	

			$this->email->send();
			
			/*
			//We should send Email1 to people who invited this guy now...
		
			Subject Line: [User A's First Name] Just Joined!

			Hey [Email recipient's First Name],

			[Friend’s first name] just joined Spinsetter at your recommendation. Now sharing music together is even easier, and their name will appear at the top of the respin list when you share songs on Spinsetter. 
			<br><br>
			Have fun!
			*/
                        $friends = $this->UsersModel->recentActivityFriends($user->IdUser,false);
                        
                        foreach($friends as $f){
                        
                            $this->email->to($f->Email); 
                            
                            $this->email->subject($user->Firstname.' Just Joined!');
                            
                            
                            $message = "<html><body>
                                        <p>
                                        <img src='".base_url()."img/email-header.png'  /><br><br>
                                        Hey ".$f->Firstname.",<br><br>

                                        ".$user->Firstname." just joined Spinsetter at your recommendation. Now sharing music together is even easier, and their name will appear at the top of the respin list when you share songs on Spinsetter. 
                                        <br><br>
                                        Have fun! <br>
                                        </p>           
                                        </body></html>
                            ";
                            $this->email->message($message);	

                            $this->email->send();
                        
                        }
			
		echo json_encode(array('Username'=>$user->Username,'Password'=>$user->Password,'Token'=>$this->UsersModel->login($user->Username,$user->Password)));
		
		return true;
	}
	
	
}
