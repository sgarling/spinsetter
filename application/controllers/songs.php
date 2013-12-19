<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Songs extends MY_Controller {

	
	public function getStreamUrl($IdSource){
		
		
		//$this->readfile_chunked("http://api.soundcloud.com/tracks/".$IdSource."/stream?client_id=7d1117f42417d715b28ef9d59af7d57c",100,false);
		// Read a file and display its content chunk by chunk
		
		echo json_encode(array(
				       "src"=>$this->getLocationUrl("http://api.soundcloud.com/tracks/".$IdSource."/stream?client_id=7d1117f42417d715b28ef9d59af7d57c")
				      )
				);
		
		//echo file_get_contents("http://api.soundcloud.com/tracks/".$IdSource."/stream?client_id=7d1117f42417d715b28ef9d59af7d57c");
	}
	private function remotefileSize($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		$data = curl_exec($ch);
		$filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		curl_close($ch);
		if ($filesize) return $filesize;
	}
	
	private function getLocationUrl($url) {
			$ch = curl_init();
			$timeout = 0;
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_HEADER, TRUE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			// Getting binary data
			$header = curl_exec($ch);
			$retVal = array();
			$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
			foreach( $fields as $field ) {
			    if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
				$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
				if( isset($retVal[$match[1]]) ) {
				    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
				} else {
				    $retVal[$match[1]] = trim($match[2]);
				}
			    }
			}
		//here is the header info parsed out
		/*
		echo '<pre>';
		print_r($retVal);
		echo '</pre>';
		*/
		//here is the redirect
		if (isset($retVal['Location'])){
		     $location = $retVal['Location'];
		} else {
			$location = false;
		     //keep in mind that if it is a direct link to the image the location header will be missing
		     //echo $_GET[$urlKey];
		}
		curl_close($ch);
		return $location;
	}
	
	public function source($IdSong){
		$this->load->model('SongsModel');
		$song = $this->SongsModel->getById($IdSong);
		$meta = $this->SongsModel->getMetadata($song->UrlSong,$song->TypeSource,false);
		redirect($meta['UrlSource']);
	}
	public function checkSoundCloud(){
		
		$url = $this->input->post('url');
		
		$page='http://api.soundcloud.com/resolve.json?url='.$url.'&client_id=7d1117f42417d715b28ef9d59af7d57c&format=json&_status_code_map[302]=200';
		$ch = curl_init();
		//$useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
		//curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_URL, $page);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);
		//print_r($response);
		//print_r($ch);
		curl_close($ch);
		
	}
	public function getYouTubeData($id){
		
		$page = 'http://gdata.youtube.com/feeds/api/videos/'.$id.'?v=2&alt=jsonc';
		$ch = curl_init();
		//$useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
		//curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ch, CURLOPT_URL, $page);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
	}
	
	public function getFollowed($IdUser,$offset,$limit=10)
	{
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		$this->load->model('UsersModel');
		
		$respined_songs=array();
		
		
		$followed_songs = $this->SongsModel->getFollowed($IdUser,$offset,$limit);
		
		$songs = $this->getSpiners($followed_songs);
		
		
		if($offset==0){
			$respined_songs = $this->SongsModel->getRespined($IdUser);
			
			$respined_songs = $this->getSpiners($respined_songs,"users");
			$songs = array_merge($respined_songs,$songs);
		
		
		
			//Now we remove duplicated, priority to respined songs i think...
			$songs = array_reduce($songs, function(&$output, $item) {
				static $tmp = array();
	
				if (!array_key_exists($item->IdSong, $tmp)) {
					$output[] = $item;
					$tmp[$item->IdSong] = true;
				}
	
				return $output;
			}, array());
			
			
			//PubDate
			//Sort songs: first respined, then order by PubDate
			usort($songs, function($a,$b){
				//$a<$b : -1
				//$a==$b: 0
				//$a>$b: 1
				
				if(isset($a->Respined) && $a->Respined=="1" && ( (isset($b->Respined)  && $b->Respined=="2") || (!isset($b->Respined)) )  ){
					return -1;
				}
				if(isset($b->Respined) && $b->Respined=="1" && ( (isset($a->Respined)  && $a->Respined=="2") || (!isset($a->Respined)) )  ){
					return 1;
				}
				
				return strtotime($a->PubDate) - strtotime($b->PubDate);
			});  
			
		}
		
		
		
		
		echo json_encode($songs);
		
	}
	public function getReblogged($IdUser,$offset,$limit=10)
	{
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
		$songs = $this->SongsModel->getReblogged($IdUser,$offset,$limit);
		
		$songs = $this->getSpiners($songs);
		
		echo json_encode($songs);
		
	}
	public function getByFriend($IdUser,$offset,$limit=10){
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
		$songs = $this->SongsModel->getReblogged($IdUser,$offset,$limit);
		
		$songs = $this->getSpiners($songs);
		
		echo json_encode($songs);
		
	}
	public function getByBlog($id,$offset,$limit=10){
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
		$blog = $this->BlogsModel->getById($id);
		
		$songs = $this->SongsModel->getByBlog($blog->IdBlog,$offset,$limit);
		
		$songs = $this->getSpiners($songs);
		
		echo json_encode($songs);
		
	}
	public function getByGenre($name,$offset,$limit=10){
		$this->load->model('SongsModel');
		
		
		$songs = $this->SongsModel->getByGenre($name,$offset,$limit);
		
		$songs = $this->getSpiners($songs);
		
		echo json_encode($songs);
		
	}
	private function getSpiners($songs,$type='blogs'){
		$this->load->model('SongsModel');
		
                switch($type){
                        case "blogs":
                            foreach($songs as $k=>$song){
                                    $source = $this->SongsModel->getBlogSource($song->IdSong);
                                    
                                    $songs[$k]->spiner = $source;
                            }
                            break;
                        case "users":
                            foreach($songs as $k=>$song){
                                    $source = $this->SongsModel->getSpinerSource($song->IdSong);
                                    
                                    $songs[$k]->spiner = $source;
                            }
                            break;
                }
		return $songs;
	}
	
	public function search($q,$offset=0,$limit=10){
		$this->load->model('SongsModel');
		
		require_once (APPPATH.'third_party/google-api-php-client/src/Google_Client.php');  
		require_once (APPPATH.'third_party/google-api-php-client/src/contrib/Google_YouTubeService.php');  
		
		
		$q = urldecode($q);
		
		$maxResults = $limit;
		
		/* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the 
		Google APIs Console <http://code.google.com/apis/console#access> 
		Please ensure that you have enabled the YouTube Data API for your project. */  
		$DEVELOPER_KEY = 'AIzaSyBegbA2zYVL1FWJ78hWu7AAIxDznSa2Sw4';
		
		$client = new Google_Client();  
		
		$client->setDeveloperKey($DEVELOPER_KEY);  
		  
		  $youtube = new Google_YoutubeService($client);  
		
		$songs = array();
		
		
			
		  try {
			
			$searchResponse = $youtube->search->listSearch('id,snippet', array(  
			  'q' => $q,  
			  'maxResults' => $limit
			));
			
			foreach ($searchResponse['items'] as $searchResult) {  
			  switch ($searchResult['id']['kind']) {  
				case 'youtube#video':  
				  
				  
				  
					$urlSong="http://www.youtube.com/v/".$searchResult['id']['videoId'];
				  
					$song = $this->SongsModel->getByUrl($urlSong);
					
					if($song){
						$songs[]=$song;
						continue;
					}else{
						$song=array();
					}
					
					$song['UrlSong']=$urlSong;
					$song['TypeSource']='youtube';
					
					
					
					
					
					$meta= $this->SongsModel->getMetadata($song['UrlSong'],$song['TypeSource']);
					if(!$meta){
						//No metadata
						continue;
					}
					$song['TypeSong']=$meta['TypeSong'];
					if($meta['TypeSong']!='playlist' && $meta['TypeSong']!='track'){
						continue;
					}
					$song['PubDate']=date("Y-m-d H:i:s");
					
					$song = array_merge($song,$meta);
					
					$song['IdSong']=$this->SongsModel->insert((object)$song);
					
					$songs[]=(object)$song;
				  
				  break;  
			   }  
			}
			
			} catch (Google_ServiceException $e) {  
				$htmlBody = sprintf('<p>A service error occurred: <code>%s</code></p>',  
				htmlspecialchars($e->getMessage()));  
				print_r($htmlBody);
			} catch (Google_Exception $e) {  
				$htmlBody = sprintf('<p>An client error occurred: <code>%s</code></p>',  
				htmlspecialchars($e->getMessage()));  
				print_r($htmlBody);
			}
		
		$songs = $this->getSpiners($songs);
		
		echo json_encode($songs);
			
		
	}
	
	
	public function card($type){
		$this->load->view('profileStream',array('type'=>$type));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */