<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SongsModel extends CI_Model {
    
	var $IdSong = '';
	var $UrlSong   = '';
	var $TypeSong ='';
	var $TypeSource ='';
	
	var $Title='';
	var $Author='';
	var $Description='';
	var $Duration='';
	var $Genres='';
	var $Artwork='';
	var $IdSource='';
	var $PubDate='';
	
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    private function getSongsTable(){
		return 'songs';
	}
	private function getSongsBlogsTable(){
		return 'songsblogs';
	}
    function get($q=false)
    {
		$this->db->order_by('IdSong','DESC');
		if($q){
			//$query = $this->db->get('songs',$q);
			$query = $this->db->get_where($this->getSongsTable(), array('TypeSong'=>'track'),$q);
		}else{
			$query = $this->db->get($this->getSongsTable(),array('TypeSong'=>'track'));
		}
        return $query->result();
    }
	function getFollowed($IdUser,$offset=0,$limit=500){
		/*
		SELECT s.*,sb.IdBlog FROM songs s 
		LEFT JOIN songsblogs sb ON s.IdSong=sb.IdSong 
		LEFT JOIN songsusers su ON s.IdSong=su.IdSong
		JOIN followings f ON (sb.IdBlog=f.IdFollowed OR su.IdUser=f.IdFollowed) 
		WHERE f.IdFollower='1'
		*/
		$this->db->select($this->getSongsTable().".*");
		$this->db->order_by($this->getSongsTable().'.PubDate','DESC');
		//$this->db->from($this->getSongsTable(),$limit,$offset);
		$this->db->join('songsblogs', $this->getSongsTable().'.IdSong = songsblogs.IdSong','left');
		$this->db->join('songsusers', $this->getSongsTable().'.IdSong = songsusers.IdSong','left');
		$this->db->join('followings f', "  songsblogs.IdBlog = f.IdFollowed AND f.TypeFollowed='blogs' OR songsusers.IdUser = f.IdFollowed AND f.TypeFollowed='users'");
		$this->db->where(array('f.IdFollower'=> $IdUser));
		$this->db->group_by($this->getSongsTable().".IdSong");
		$query = $this->db->get($this->getSongsTable(),$limit,$offset);
                
                return $query->result();
		
		
	}
	function getRespined($IdUser,$offset=0,$limit=500){
		/*
		SELECT s.*,f.IdUserFrom,f.Status FROM songs s 
		JOIN respins f ON s.IdSong=f.IdSong 
		WHERE f.IdUserTo='1' AND f.Status>0 ORDER BY f.Date DESC
		*/
		$this->db->select($this->getSongsTable().".*,respins.Status AS Respined, respins.Status as RespinStatus,respins.IdUserFrom");
		$this->db->order_by('respins.Date','DESC');
		$this->db->join('respins', $this->getSongsTable().'.IdSong = respins.IdSong');
		$this->db->where('respins.IdUserTo',$IdUser);
		$this->db->where('respins.Status',1);
		$query = $this->db->get($this->getSongsTable(),$limit,$offset);
        return $query->result();
		
		
	}
	function getReblogged($IdUser,$offset=0,$limit=500){
		/*
		SELECT s.*,sb.IdBlog FROM songs s 
		LEFT JOIN songsblogs sb ON s.IdSong=sb.IdSong 
		LEFT JOIN songsusers su ON s.IdSong=su.IdSong
		JOIN followings f ON (sb.IdBlog=f.IdFollowed OR su.IdUser=f.IdFollowed) 
		WHERE f.IdFollower='1'
		*/
		$this->db->select($this->getSongsTable().".*");
		$this->db->order_by('songsusers.PubDate','DESC');
		//$this->db->from($this->getSongsTable());
		$this->db->join('songsusers', $this->getSongsTable().'.IdSong = songsusers.IdSong');
		$this->db->where(array('songsusers.IdUser'=> $IdUser));
		$query = $this->db->get($this->getSongsTable(),$limit,$offset);
        return $query->result();
		
		
	}
	function getSoundCloud()
    {
		$query = $this->db->get_where($this->getSongsTable(), array('TypeSource' => 'soundcloud','TypeSong'=>'track'),1);
		
        $result = $query->result();
		$result = $result[0];
		return $result;
    }
	function getYouTube()
    {
		$query = $this->db->get_where($this->getSongsTable(), array('TypeSource' => 'youtube','TypeSong'=>'track'),1);
		
        $result = $query->result();
		$result = $result[0];
		return $result;
    }
	function getByBlog($IdBlog,$offset=0,$limit=500)
    {
		$this->db->order_by($this->getSongsTable().'.PubDate','DESC');
		//$this->db->from($this->getSongsTable());
		$this->db->join('songsblogs', $this->getSongsTable().'.IdSong = songsblogs.IdSong');
		
		//This is baddly hardcoded to consolidate Pitchfork,Disco Naivete and This Song is Sick
		if($IdBlog==1){
			$where = 'songsblogs.IdBlog IN("1","2","5","6") AND songs.TypeSong="track" ';
			$this->db->where($where);
		}else{
			$this->db->where(array('songsblogs.IdBlog'=> $IdBlog,$this->getSongsTable().'.TypeSong'=>'track'));
		}
		
		
		$query = $this->db->get($this->getSongsTable(),$limit,$offset);
        return $query->result();
    }
	function getByGenre($name,$offset=0,$limit=500)
    {
		$this->db->order_by($this->getSongsTable().'.PubDate','DESC');
		$this->db->or_like(array('Title'=>$name,'Genres'=>$name)); 
		$query = $this->db->get($this->getSongsTable(),$limit,$offset);
        return $query->result();
    }
	function getBlogSource($IdSong){
		
		/*
		
		$this->db->select('blogs.*');
		$this->db->join('blogs', $this->getSongsBlogsTable().'.IdBlog = blogs.IdBlog');
		
		$this->db->where(array($this->getSongsBlogsTable().'.IdSong'=> $IdSong));
		
		
		$query = $this->db->get($this->getSongsBlogsTable());
                $result = $query->result();
		if(isset($result[0])){
			$source['IdSpiner']=$result[0]->IdBlog;
			$source['NameSpiner']=str_replace("'","",$result[0]->NameBlog);//this must be done in the model
			$source['TypeSpiner']='blogs';
			
			return (object)$source;
		}
		//Search for a users as source in songsusers
		
		$this->db->select('users.*');
		$this->db->join('users', 'songsusers.IdUser = users.IdUser');
		
		$this->db->where(array('songsusers'.'.IdSong'=> $IdSong));
		
		
		$query = $this->db->get('songsusers');
                $result = $query->result();
		
		if(isset($result[0])){
			$source['IdSpiner']=$result[0]->IdUser;
			$source['NameSpiner']=$result[0]->Username;
			$source['TypeSpiner']='users';
				
			return (object)$source;
		}
                
                */
				
				/*
				if(track.spiner.TypeSpiner=='blogs'){
					track.PicSpiner = 'blogsLogos/'+track.spiner.NameSpiner+'.jpg';
				}else if(track.spiner.TypeSpiner=='users'){
					track.PicSpiner = 'blogsLogos/youtube.jpg';
				}else if(track.spiner.TypeSpiner=='youtube'){
					track.PicSpiner = 'blogsLogos/youtube.jpg';
				}
				*/
                if($s = $this->getSongSource($IdSong,"IdBlog","blogs","IdBlog",$this->getSongsBlogsTable())){
                
                    $source['IdSpiner']=$s->IdBlog;
                    $source['NameSpiner']=str_replace("'","",$s->NameBlog);
                    $source['TypeSpiner']='blogs';
                    $source['Picture']='blogsLogos/'.$source['NameSpiner'].'.jpg';
					
                    return (object)$source;
				}		
                
                if($s = $this->getSongSource($IdSong,"IdUser","users","IdUser","songsusers")){
                
                    $source['IdSpiner']=$s->IdUser;
                    $source['NameSpiner']=$s->Username;
                    $source['TypeSpiner']='users';
					$source['Picture']=$s->ProfilePicture;
		
                    return (object)$source;
                }		
                
                
		$source['IdSpiner']='';
		$source['NameSpiner']='Youtube Search';
		$source['TypeSpiner']='youtube';
				
		return (object)$source;
	}
    function getSpinerSource($IdSong){
		
		
		$s = $this->getSongSource($IdSong,"IdUser","users","IdUserFrom","respins");
                
		$source['IdSpiner']=$s->IdUser;
		$source['NameSpiner']=$s->Firstname." ".$s->Lastname;
		$source['TypeSpiner']='users';
		$source['Picture']=$s->ProfilePicture;		
		return (object)$source;
		
	}
    private function getSongSource($IdSong,$sourceIdName,$sourceTable,$songIdName,$songTable){
                $this->db->select($sourceTable.'.*');
		$this->db->join($sourceTable, $songTable.'.'.$songIdName.' = '.$sourceTable.'.'.$sourceIdName);
		
		$this->db->where(array($songTable.'.IdSong'=> $IdSong));
		
		
		$query = $this->db->get($songTable);
                $result = $query->result();
		
                $source = array();
		if(isset($result[0])){
			$source = $result[0];
                        return (object)$source;

		}
				
		return false;
        
    }
    function insert($a)
    {
		
        $this->UrlSong   = $a->UrlSong;
		$this->TypeSong = $a->TypeSong;
		$this->TypeSource = $a->TypeSource;

		
		$this->Title=$a->Title;
		$this->Author=$a->Author;
		$this->Description=$a->Description;
		$this->Duration=$a->Duration;
		$this->Genres=$a->Genres;
		$this->Artwork=$a->Artwork;
		$this->IdSource=$a->IdSource;
		$this->PubDate=$a->PubDate;
		
        $this->db->insert($this->getSongsTable(), $this);
		$IdSong = $this->db->insert_id();
		
		if(isset($a->IdBlog)){
			$this->insertSongsBlogs($IdSong,$a->IdBlog);
		}
		
		return $IdSong;
		
    }
	function insertSongsBlogs($IdSong,$IdBlog){
		
		$insert_query = $this->db->insert_string( $this->getSongsBlogsTable(),(object)array('IdSong'=>$IdSong,'IdBlog'=>$IdBlog));
		$insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
		$this->db->query($insert_query);
		
	}
	function update($id,$data){
		$this->db->where('IdSong', $id);
		$this->db->update($this->getSongsTable(), $data); 
	}
	function getByUrl($url)
    {
            
		$query = $this->db->get_where($this->getSongsTable(), array('UrlSong' => $url));
		$count= $query->num_rows();    //counting result from query
		$result = $query->result();
		if($query->num_rows()==0){
			return false;
		}else{
			return $result[0];
		}
    } 
	
	
	
	
	
	
	function getMetadata($url,$type,$artwork=true){
		
		/*
		Metadata:(soundcloud,youtube)

		-artwork_url(artwork_url,data.thumbnail.hqDefault)
		-author(user.username,data.uploader)
		-title(title,data.title)
		-description(description,data.description)
		-duration(duration,data.duration)
		-genres(genre,data.category)
		-id(id,data.id)
		*/
		
		$meta = array();
		switch($type){
			case "soundcloud":
				$sc = $this->getSoundCloudMetadata($url);
				if(!$sc){
					return false;
				}
				if(!isset($sc->id))
					return array();
					
				$meta['Author']=(isset($sc->user->username)?$sc->user->username:'Unknown Author');
				$meta['Title']=(isset($sc->title)?$sc->title:'Unknown Title');
				$meta['Description']=(isset($sc->description)?$sc->description:'');
				$meta['Duration']=(isset($sc->duration)?$sc->duration:'');
				$meta['Genres']=(isset($sc->genre)?$sc->genre:'');
				$meta['IdSource']=$sc->id;
				$meta['Artwork']=$meta['IdSource'].'.jpg';
				$meta['TypeSong']=$sc->kind;
				$meta['UrlSource'] = $sc->permalink_url;
				if($artwork){
					if(!isset($sc->artwork_url) || $sc->artwork_url==''){
					
						$this->saveArtwork(base_url().'img/default-player-artwork.png','./artwork/'.$meta['Artwork']);
					}else{
						$this->saveArtwork(str_replace('large','t500x500',$sc->artwork_url),'./artwork/'.$meta['Artwork']);
					}
				}
				break;
			case "youtube":
				$yt = $this->getYouTubeMetadata($url);
				if(!$yt){
					return false;
				}
				if(!isset($yt->data->id))
					return array();
					
				$meta['Author']=$yt->data->uploader;
				$meta['Title']=$yt->data->title;
				$meta['Description']=$yt->data->description;
				$meta['Duration']=$yt->data->duration;
				$meta['Genres']=$yt->data->category;
				$meta['IdSource']=$yt->data->id;
				$meta['Artwork']=$meta['IdSource'].'.jpg';
				$meta['TypeSong']='track';
				$meta['UrlSource'] = "http://www.youtube.com/watch?v=".$yt->data->id;
				if($artwork){
					if(!isset($yt->data->thumbnail->hqDefault) || $yt->data->thumbnail->hqDefault==''){
					
						$this->saveArtwork(base_url().'img/default-player-artwork.png','./artwork/'.$meta['Artwork']);
					}else{
						$this->saveArtwork($yt->data->thumbnail->hqDefault,'./artwork/'.$meta['Artwork']);
					}
				
				}
				break;
		
		}
		
		return $meta;
			
		
	}
	
	function saveArtwork($url,$dest){
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close ($ch);

		$fp = @fopen($dest,'w');
		@fwrite($fp, $raw);
		@fclose($fp);
	}
	
	function getSoundCloudMetadata($url){
		
		$page='http://api.soundcloud.com/resolve.json?url='.$url.'&client_id=7d1117f42417d715b28ef9d59af7d57c&format=json&_status_code_map[302]=200';
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $page);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($ch));
		
		if(!isset($response->status)){
			
			return false;
		}
		if($response->status=='302 - Found'){
			curl_setopt($ch, CURLOPT_URL, $response->location);
			
			$response = json_decode(curl_exec($ch));
			
			
		}else{
			$response = json_decode('{}');
		}
		curl_close($ch);
		return $response;
		
		
		
	}

	function getYouTubeMetadata($url){
		
		
		preg_match_all('/(v\/|watch?v=|embed\/)[a-zA-Z0-9\+&@#\-]+/',$url, $id, PREG_PATTERN_ORDER);
		
		$regex = '/(v\/|watch\?v=|embed\/)/';

		$id=preg_split($regex,$url);

		if(count($id) !=2){
			return false;
		}
		$id=$id[1];

		$page = 'http://gdata.youtube.com/feeds/api/videos/'.$id.'?v=2&alt=jsonc';
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $page);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		return $response;
	}
	
	
	public function getById($IdSong){
	
		$query = $this->db->get_where($this->getSongsTable(), array('IdSong' => $IdSong));
		$count= $query->num_rows();    //counting result from query
		$result = $query->result();
		if($query->num_rows()==0){
			return false;
		}else{
			return $result[0];
		}
	}
	
	
} 