
  

  

  var player = {
    autoPlay : false,
	playing : false,

	playlist : '',
	trackList : [],
	trackIndex : 0,
	
	playDelay:250,
	currentTrack : null,
	currentSound : null,
	currentPos : 0,
	progressTimer:0,
	newPlaylistFlag:false,
	startProgressBar:function(){
		this.progressTimer=window.setInterval(function(){
		
			player.currentTrack.getPosition(function(pos){
			    
			    var duration=player.currentTrack.getDuration();
			    var newPos = (pos / duration) * $('.song-progress-wrapper').width();
			    //console.log('Progress:'+pos);
			    player.updatePosition(newPos);
			    
			});
			
			
		},1000);
	},
	stopProgressBar:function(){
		window.clearInterval(this.progressTimer);
	},
	updatePosition:function(pos){
		
		this.currentPos=pos;
		$('.song-progress').css('left', '' + this.currentPos + 'px');
		//console.log(pos);
	},
	setPosition:function(pos){
		this.stopProgressBar();
		this.updatePosition(pos);
		
		var newPos = Math.floor((pos/$('.song-progress-wrapper').width()) * this.currentTrack.getDuration());
		
		this.currentTrack.setPosition(newPos);
		//console.log('New Position:'+newPos);
		this.startProgressBar();
		
	},
    getCurrentTrack: function()
    {
      return player.currentTrack;
    },
    getTrackList: function() {
      return player.trackList;
    },
    empty: function(){
		//this.pause();
		this.playlist = '';
		this.trackList=[];
		this.trackIndex = 0;
		
	},
	//Setters
    setTrackList: function(tracks)
    {
      player.trackList = tracks;
	  trackIndex = 0;
    },

    updateTrackIndex: function()
    {
      if (player.currentTrack != null) { 
        player.trackIndex = _.indexOf(player.trackList, currentTrack); 
        console.log("playing track at index " + trackIndex);
      }
    },
	
	playById: function(id){
		
		//console.log(this.trackList);
		
		var index = 0;
		var track = _.find(this.trackList,function(item){
			index++;
			if(item.source.track == null){
				console.log("playById error");
				console.log(item);
				return false;
			}
			return item.source.track.IdSource==id;
		});
		index--;
		
		player.playPauseTrack(track,index);
	
		return;
	},
    //Player Control Logic || playFromPlayer could be made cleaner. call playPauseTrack(trackList[0], 0)
    playFromPlayer: function()
    {
		if(this.trackList.length === 0)
			return false;
			
		this.playPauseTrack(this.trackList[this.trackIndex],this.trackIndex);
		
		return true;
	
    },
    
    playPauseTrack: function(track, index)
    {
		if(track==null){
			console.log("Track error, index:"+index);
			$('.play-btn').removeClass('pause-btn');
			this.skipFwd();
		}
	  //Toggle play if user intend to play/pause the current song
      if (player.currentTrack === track)
      {
        
        
		if (!player.playing)
        {
          player.play(player.currentTrack);
		//player.currentTrack.play();
          player.playing = true;
		  player.startProgressBar();
		  stream.selectTrack(player.currentTrack.source.track.IdSong);
		  $('.song-title marquee').html(player.currentTrack.getMarqueeTitle());
		  $('.play-btn').addClass('pause-btn');
		  
		  this.togglePlayPauseIcon(track.source.track.IdSong);
		  
        } else {
          player.currentTrack.pause();
          player.playing = false;
		  player.stopProgressBar();
		  stream.unSelectAllTracks();
		  $('.play-btn').removeClass('pause-btn');
		  
		  this.togglePlayPauseIcon();
		  
        }
		
      } else {
        //Lets play a new song:
		$('.song-progress').css('left', '0px');
		
		//If its a respined song, lets ungrey it:
		if(track.Respined=="1"){
			
			track.Respined = users.unRespin(track.source.track.IdSong);
		}
		
		
        if (player.playing)
        {
		  
          player.currentTrack.stop();
          player.playing = false;
		  player.stopProgressBar();
		  stream.unSelectAllTracks();
		  $('.play-btn').removeClass('pause-btn');
		  
		  this.togglePlayPauseIcon();
		  
        }
		player.currentTrack=track;
		player.currentTrack.stop();
		player.play(player.currentTrack);
		//player.currentTrack.play();
		
		player.playing = true;
		player.startProgressBar();
		player.trackIndex = index;
		stream.selectTrack(player.currentTrack.source.track.IdSong);
		$('.song-title marquee').html(player.currentTrack.getMarqueeTitle());
		$('.play-btn').addClass('pause-btn');
		
		this.togglePlayPauseIcon(track.source.track.IdSong);
		
        
		
      }
    },
	
	togglePlayPauseIcon: function(IdSong){
		if(typeof IdSong == "undefined"){
			var IdSong = false;
		}
		$(".song-card .card-play-pause img").attr('src',base_url+'img/card-play-icon.png');
		
		if(IdSong){
				$("#song"+IdSong+" .card-play-pause img").attr('src',base_url+'img/pause-btn.png');
		}
		
	},
	
	startPlaying:null,
	play: function(track){
		
		clearTimeout(this.startPlaying);
		this.startPlaying = setTimeout(function(){
			  track.play();
		},this.playDelay);
		
	},
    pause: function()
    {
      if(player.currentTrack)
      {
        if (player.playing)
        {
          player.currentTrack.pause();
          player.playing = false;
		  $('.play-btn').removeClass('pause-btn');
		  
		  this.togglePlayPauseIcon();
        }
      }
    },
	
    skipFwd: function()
    {
		
		
		if (player.currentTrack)
			  {
				console.log(player.trackIndex);
				
				
				player.currentTrack.stop();
				player.currentTrack.playIconState = "play";
				player.playing = false;
				player.stopProgressBar();
				
				if(this.newPlaylistFlag){
					player.trackIndex=0;
					this.newPlaylistFlag=false;
				}else{
					player.trackIndex++;
				
				}
				if (player.trackIndex >= player.trackList.length) { player.trackIndex = 0; }
				var track = player.trackList[player.trackIndex];
				track.playIconState = "pause";
				
				player.playPauseTrack(track, player.trackIndex);

			  }
    },
    skipBack: function()
    {
      if (player.currentTrack)
      {
        player.currentTrack.stop();
        player.currentTrack.playIconState = "play";
        player.playing = false;
		player.stopProgressBar();
        
		this.playFromPlayer();
		
		
		if(this.newPlaylistFlag){
			player.trackIndex=0;
			this.newPlaylistFlag=false;
		}else{
			player.trackIndex--;
				
		}
		
		
        if (player.trackIndex === -1) { player.trackIndex = player.trackList.length - 1; }
        var track = player.trackList[player.trackIndex];
        track.playIconState = "pause";
        player.playPauseTrack(track, player.trackIndex);
		
		
      }
    }
  };