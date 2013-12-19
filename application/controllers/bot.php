<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bot extends CI_Controller {

	/*
	private function getMetadata($url,$type,$artwork=true){
		
		
		//Metadata:(soundcloud,youtube)

		//-artwork_url(artwork_url,data.thumbnail.hqDefault)
		//-author(user.username,data.uploader)
		//-title(title,data.title)
		//-description(description,data.description)
		//-duration(duration,data.duration)
		//-genres(genre,data.category)
		//-id(id,data.id)
		
		
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

	private function saveArtwork($url,$dest){
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
	
	private function getSoundCloudMetadata($url){
		
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

	private function getYouTubeMetadata($url){
		
		
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
	*/
	public function parseFeeds($IdTest=false){
		set_time_limit (0);
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
                //Getting all blogs from the database...
		if(!$IdTest){
			$blogs = $this->BlogsModel->getAll();
		}else{
			$blogs = array($this->BlogsModel->getById($IdTest));
		}
		
		
		$sources =array();
		$songs = array();
		
		
		$c=0;
		
		foreach($blogs as $blog){		
			
                        
                        //Gather all the posible urls from the feed
			$sources = $this->parseCandidates($blog,true);
			
	
			
			foreach($sources['urlSongs'] as $k=>$urlSong){
				
				
				$song = $this->SongsModel->getByUrl($urlSong);
				
				if($song){
					
					$this->SongsModel->insertSongsBlogs($song->IdSong,$blog->IdBlog);
					//count number of songs added:
					$c++;
					continue;
				}else{
					$song=array();
				}
				
				$song['UrlSong']=$urlSong;
				
				if(strpos($urlSong,'soundcloud')){
					$song['TypeSource']='soundcloud';
					
				}else{
					$song['TypeSource']='youtube';
					
				}
				
				$song['IdEntity']=$blog->IdBlog;
				$song['TypeEntity']='blog';
				
				$meta= $this->SongsModel->getMetadata($song['UrlSong'],$song['TypeSource']);
				if(!$meta){
					//No metadata
					continue;
				}
				$song['TypeSong']=$meta['TypeSong'];
                                
                                
				if($meta['TypeSong']!='playlist' && $meta['TypeSong']!='track'){
					continue;
				}
				$song['PubDate']=$sources['pubDates'][$k];
				
				$song['IdBlog'] = $blog->IdBlog;
				$song = array_merge($song,$meta);
				
				$songs[]=$this->SongsModel->insert((object)$song);
				
				//count number of songs added:
				$c++;
			}
			
		}
		
		echo "Task finished<br>";
		echo"Found ".$c." songs in the feeds.";
		
		

	}

	
	public function parseCandidates($blog,$return=false){
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
		
		$urlSongs=array();
		$pubDates=array();
		
		$ch = curl_init();
			
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
		
		
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		
                        
                        
                        
                        if(!isset($blog->FeedBlog) || is_null($blog->FeedBlog) || $blog->FeedBlog == ""){
                            curl_setopt($ch, CURLOPT_URL, $blog->UrlBlog);
                        }else{
                            curl_setopt($ch, CURLOPT_URL, $blog->FeedBlog);
                        }
                        
			
			if(!$return){
				echo $blog->NameBlog." ( <a href='".$blog->FeedBlog."' target='_blank'>".$blog->FeedBlog."</a> ) <br>";
			}
			$response = curl_exec($ch);
			
			if($response==''){
				if(!$return){
					echo 'Empty response.';
				}
				
				
			}else{
                                //Okay because of this lines of code, this wont work for those which doesnt have <item> elements, maybe we can avoid them
				
                                //$items = explode('<item>',$response);
				//$items = array_slice($items,1);
				
				//foreach($items as $resp){
				
					$sources=array();
					//preg_match_all('/(https?\:)?\/\/((w\.)?soundcloud\.com|www\.youtube\.com)\/[a-zA-Z0-9\+&@#\-\_\/=\?%\.]+/',$resp, $sources, PREG_PATTERN_ORDER);
					preg_match_all('/(https?\:)?\/\/((w\.)?soundcloud\.com|www\.youtube\.com)\/[a-zA-Z0-9\+&@#\-\_\/=\?%\.]+/',$response, $sources, PREG_PATTERN_ORDER);
					
					$sources = $sources[0];
					
					//$startsAt = strpos($resp, "<pubDate>") + strlen("<pubDate>");
					//$endsAt = strpos($resp, "</pubDate>", $startsAt);
					
					//if(!$startsAt || !$endsAt){
						$pubDate = date('Y-m-d H:i:s');
					//}else{
					//	$pubDate = substr($resp, $startsAt, $endsAt - $startsAt);
					//	$pubDate = date('Y-m-d H:i:s',strtotime($pubDate));
					//}
					
					foreach($sources as $k=>$urlSong){
						
						$pubDates[]=$pubDate;
						
						$urlSong=str_replace(
							array(
							'https://w.soundcloud.com/player/?url=',
							'http://w.soundcloud.com/player/?url=',
							'%3A',
							'%2F',
							'?feature=player',
							'?secret',
							'%3Fsecret',
							'?wmode=transparent',
							'/download',
							'http://w.soundcloud.com/player?url=',
							'/embed/',
							'watch?v=',
							'?feature=watch',
							'_embedded',
							'_detailpage'
							),
							array('','',':','/','','','','','','','/v/','v/','','',''),$urlSong);
							
							$urlSong=explode('&',$urlSong);
							$urlSong=$urlSong[0];
							
							if(strpos($urlSong,'youtube')){
								
								$urlSong = explode('outube.com/v/',$urlSong);
								$urlSong=(isset($urlSong[1])?$urlSong[1]:'');
								$urlSong='http://www.youtube.com/v/'.substr($urlSong,0,11);
							}else{
								$urlSong = explode('_',$urlSong);
								$urlSong=$urlSong[0];
							}
							
							
						$urlSongs[]=$urlSong;
						if(!$return){
							echo $k.".".$urlSong."<br>";
						}
					}
				//}
			}
		
		curl_close($ch);
		
		return array('urlSongs'=>$urlSongs,'pubDates'=>$pubDates);
	}
	public function checkFeeds(){
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
		$blogs = $this->BlogsModel->getAll();
		
		$ch = curl_init();
			
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
		
		
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		
		foreach($blogs as $blog){
			curl_setopt($ch, CURLOPT_URL, $blog->FeedBlog);
			
			echo $blog->NameBlog." ( <a href='".$blog->FeedBlog."' target='_blank'>".$blog->FeedBlog."</a> ) <br>";
			
			$response = curl_exec($ch);
			
			if($response==''){
				echo '<span style="color:red;">We got an empty response from the feed.</span>';
			}else{
				$sources=array();
				preg_match_all('/(https?\:)?\/\/((w\.)?soundcloud\.com|www\.youtube\.com)\/[a-zA-Z0-9\+&@#\-\_\/=\?%\.]+/',$response, $sources, PREG_PATTERN_ORDER);
				$sources = $sources[0];
				
				echo "Should be like ";
				echo substr_count( $response,'youtube.com')+substr_count( $response,'soundcloud.com')." candidates<br>";
				echo "Found <a href='".base_url()."bot/parseRss/".$blog->IdBlog."' target='_blank'>".count($sources)." candidates</a>.<br>";
			}	
			echo "<hr>";
			
		}
		curl_close($ch);
	}
	
	public function parseRss($IdBlog,$printFlag=false){
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
		$blog = $this->BlogsModel->getById($IdBlog);
		$urlSongs=array();
		$pubDates=array();
		
		if(!$printFlag){
				echo $blog->NameBlog." ( <a href='".$blog->FeedBlog."' target='_blank'>".$blog->FeedBlog."</a> ) <br>";
			}
		
		$this->load->library('rssparser');                          // load library
		$this->rssparser->set_feed_url($blog->FeedBlog);  // get feed
		//$this->rssparser->set_cache_life(30); 
		$rss = $this->rssparser->getFeed(); 
		
		$j=0;
		foreach($rss as $k=>$r){
		
				$sources=array();
				preg_match_all('/(https?\:)?\/\/((w\.)?soundcloud\.com|www\.youtube\.com)\/[a-zA-Z0-9\+&@#\-\_\/=\?%\.]+/',$r['description'], $sources, PREG_PATTERN_ORDER);
				$sources = $sources[0];
				
				foreach($sources as $urlSong){
					
					$urlSong=str_replace(
						array(
						'https://w.soundcloud.com/player/?url=',
						'http://w.soundcloud.com/player/?url=',
						'%3A',
						'%2F',
						'?feature=player',
						'?secret',
						'%3Fsecret',
						'?wmode=transparent',
						'/download',
						'http://w.soundcloud.com/player?url=',
						'/embed/',
						'watch?v=',
						'?feature=watch',
						'_embedded',
						'_detailpage'
						),
						array('','',':','/','','','','','','','/v/','v/','','',''),$urlSong);
						
						$urlSong=explode('&',$urlSong);
						$urlSong=$urlSong[0];
						
						if(strpos($urlSong,'youtube')){
							
							$urlSong = explode('outube.com/v/',$urlSong);
							$urlSong=(isset($urlSong[1])?$urlSong[1]:'');
							$urlSong='http://www.youtube.com/v/'.substr($urlSong,0,11);
						}else{
							$urlSong = explode('_',$urlSong);
							$urlSong=$urlSong[0];
						}
						
						
					$urlSongs[]=$urlSong;
					$pubDates[]=date ('Y-m-d H:i:s',strtotime($r['pubDate']));
					
					if(!$printFlag){
						echo $j.".".$urlSong."<br>";
						$j++;
					}
				}
		}
		
		return array('urlSongs'=>$urlSongs,'pubDates'=>$postDates);
		
	}
	
	
	
	public function addBlogs(){
		$this->load->model('BlogsModel');
		
				$blogs = array(
		'name'=>array(
"A New Band A Day",
"All Things Go",
"Blah Blah Blah Science",
"Buzz Bands LA",
"Decoder Magazine",
"Disco Naivete",
"I Guess I'm Floating",
"KEXP Blog",
"The Blue Walrus",
"The Burning Ear",
"The Line of Best Fit",
"We All Want Someone to Shout For",
"Sidewalk Hustle",
"My Old Kentucky Blog",
"You Ain't No Picasso",
"An Aquarium Drunkard",
"Brooklyn Vegan",
"Drowned in Sound",
"Hear Ya",
"KickKickSnare",
"2DopeBoyz",
"A Volta",
"DJ Booth",
"Fake Shore Drive",
"illroots",
"Mostly Junkfood",
"Ruby Hornet",
"The Masked Gorilla",
"Complex Music",
"SoulCulture",
"The Smoking Section",
"NahRight",
"24 Hour Hip Hop",
"HipHopDX",
"Rap Radar",
"Hip-Hop N More",
"Xclusives Zone",
"You Heard That New",
"Beat My Day",
"Beats Per Minute",
"Bizzies",
"Blah Blah Blah",
"Chemical Jump",
"Dancing Astronaut",
"Discobelle",
"Gotta Dance Dirty",
"Harder Blogger Faster",
"I Can Give You House",
"ISO50",
"Last Gas Station",
"Peanut Butter and Jams",
"Salacious Sound",
"Stoney Roads",
"ThisSongIsSick",
"White Folks Get Crunk",
"Rolling Tuff",
"SlothBoogie",
"Do Andriods Dance"
			),
		'feed'=>array(
"http://www.anewbandaday.com/feed/",
"http://feeds.feedburner.com/allthingsgomusic",
"http://blahblahblahscience.com/feed",
"http://buzzbands.la/feed/",
"http://www.secretdecoder.net/feeds/posts/default?alt=rss",
"http://disconaivete.com/rss",
"http://www.iguessimfloating.net/feed",
"http://blog.kexp.org/feed",
"http://feeds.feedburner.com/TheBlueWalrus",
"http://feeds2.feedburner.com/theburningear",
"http://www.thelineofbestfit.com/new-music/feed",
"http://feeds.feedburner.com/weallwantsomeonetoshoutfor",
"http://sidewalkhustle.com/category/music/feed/",
"http://feeds.feedburner.com/myoldkyblog",
"http://feeds.feedburner.com/youaintnopicasso/Jyux",
"http://feeds.feedburner.com/AnAquariumDrunkard",
"http://feeds.feedburner.com/brooklynvegan",
"http://drownedinsound.com/feed",
"http://feeds.feedburner.com/Hearya",
"http://kickkicksnare.com/feed/",
"http://feeds.feedburner.com/2dopeboyz",
"http://feeds2.feedburner.com/avoltabr",
"http://www.djbooth.net/index/tracks/rss/",
"http://feeds.feedburner.com/fakeshoredrive",
"http://feeds.feedburner.com/illroots",
"http://feeds.feedburner.com/mostlyjunkfoodfeed",
"http://www.rubyhornet.com/feed/",
"http://www.themaskedgorilla.com/rss",
"http://cdnl.complex.com/feeds/channels/music.xml",
"http://www.soulculture.co.uk/feed",
"http://feeds.feedburner.com/uproxx/tss",
"http://feeds.feedburner.com/nah_right",
"http://24hourhiphop.com/feed/",
"http://feeds.feedburner.com/hiphopdx/news",
"http://feeds.feedburner.com/rapradar",
"http://hiphop-n-more.com/feed/",
"http://www.xclusiveszone.net/feed/",
"http://feeds.feedburner.com/youheardthatnew",
"http://www.beatmyday.com/feed/",
"http://feeds.feedburner.com/BPMfeed",
"http://hotbizzies.com/feed/",
"http://www.weareblahblahblah.com/feed/",
"http://feeds.feedburner.com/ChemicalJump",
"http://feeds.feedburner.com/dancingastronaut",
"http://feeds.feedburner.com/discobelle",
"http://www.gottadancedirty.com/feed/",
"http://www.harderbloggerfaster.com/feed/",
"http://www.icangiveyouhouse.com/feed/",
"http://blog.iso50.com/feed/",
"http://www.lagasta.com/feed/",
"http://pnutbutterjams.com/feed/",
"http://feeds.feedburner.com/salacioussound/",
"http://stoneyroads.com/feed/",
"http://feeds.feedburner.com/thissongissick",
"http://feeds.feedburner.com/whitefolksgetcrunk",
"http://feeds.feedburner.com/Rollingtuff",
"http://feeds.feedburner.com/slothboogie",
"http://doandroidsdance.com/audio/feed/"
			),
		'url'=>array(
"http://www.anewbandaday.com/",
"http://allthingsgomusic.com/",
"http://blahblahblahscience.com/",
"http://buzzbands.la/",
"http://www.secretdecoder.net/",
"http://disconaivete.com/",
"http://www.iguessimfloating.net/",
"http://blog.kexp.org/",
"http://www.thebluewalrus.com/",
"http://www.theburningear.com/",
"http://www.thelineofbestfit.com/",
"http://www.weallwantsomeone.org/",
"http://sidewalkhustle.com/category/music/",
"http://www.myoldkentuckyblog.com/",
"http://www.youaintnopicasso.com/",
"http://www.aquariumdrunkard.com/",
"http://www.brooklynvegan.com/",
"http://drownedinsound.com/",
"http://www.hearya.com/",
"http://kickkicksnare.com/",
"http://www.2dopeboyz.com/",
"http://avoltabr.com/",
"http://www.djbooth.net/",
"http://www.fakeshoredrive.com/",
"http://illroots.com/",
"http://www.mostlyjunkfood.com/",
"http://www.rubyhornet.com/",
"http://www.themaskedgorilla.com/",
"http://www.complex.com/music/",
"http://www.soulculture.co.uk/",
"http://smokingsection.uproxx.com/TSS/",
"http://nahright.com/news/",
"http://24hourhiphop.com/music/",
"http://www.hiphopdx.com/",
"http://rapradar.com/category/blog/music/",
"http://hiphop-n-more.com/",
"http://www.xclusiveszone.net/category/music/",
"http://youheardthatnew.com/",
"http://www.beatmyday.com/",
"http://beatsperminute.com/media/",
"http://hotbizzies.com/",
"http://www.weareblahblahblah.com/",
"http://chemicaljump.com/",
"http://www.dancingastronaut.com/",
"http://www.discobelle.net/",
"http://www.gottadancedirty.com/",
"http://www.harderbloggerfaster.com/",
"http://www.icangiveyouhouse.com/",
"http://blog.iso50.com/category/observatory/music/",
"http://www.lagasta.com/",
"http://pnutbutterjams.com/",
"http://salacioussound.com/",
"http://stoneyroads.com/",
"http://thissongissick.com/blog/",
"http://www.whitefolksgetcrunk.com/",
"http://www.rollingtuff.com/",
"http://www.slothboogie.com/",
"http://doandroidsdance.com/"
			)
	
		);
		
		foreach($blogs['url'] as $k=>$b){
			echo $this->BlogsModel->insert($blogs['url'][$k],$blogs['name'][$k],$blogs['feed'][$k]);
		}
	
	}
	
	
	public function test(){
		
		
		
		$this->load->model('BlogsModel');
		$this->load->model('SongsModel');
		
		$this->SongsModel->insertSongsBlogs(1,1);
		
	}
}
