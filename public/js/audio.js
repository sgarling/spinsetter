
function soundCloudInit(){
	//Initialize Soundcloud API
	SC.initialize(
	{
		client_id: "7d1117f42417d715b28ef9d59af7d57c"
	});

}

function youTubeInit(element,url){
	
	$('#'+element).attr('src',url);
	return new MediaElement(element, {
							enablePluginDebug: false,
							plugins: ['flash','silverlight'],
							// specify to force MediaElement to use a particular video or audio type
							type: '',
							src:this.url,
							pluginPath: base_url+'js/mediaelement/',
							flashName: 'flashmediaelement.swf',
							silverlightName: 'silverlightmediaelement.xap',
							features:['fullscreen'],
							/*
							defaultVideoWidth: 250,
							defaultVideoHeight: 212,
							pluginWidth: 250,
							pluginHeight: 212,*/
							// rate in milliseconds for Flash and Silverlight to fire the timeupdate event
							// larger number is less accurate, but less strain on plugin->JavaScript bridge
							timerRate: 250,
							// method that fires when the Flash or Silverlight object is ready
							success: function (mediaElement, domObject) { 
								
								mediaElement.setSrc(url);
								mediaElement.load();
								
								
								
								mediaElement.addEventListener('ended', function(e) {
									mediaElement.exitFullScreen();
									player.skipFwd();
									
								}, false);
								
								mediaElement.addEventListener('pause',function(e){
									player.pause();
								},false);
							},
							// fires when a problem is detected
							error: function () { 
								console.log('Youtube Error');
						}
		});
	
}

Audio = (function(){
	
	
	function Audio(metadata){
		
		
		
		this.type=metadata.TypeSource;
		this.url=metadata.UrlSong;
		this.source = {track:metadata,audio:null};
		
		
		this.spiner = metadata.spiner;
		
		if(typeof metadata.Respined != "undefined"){
			this.Respined=metadata.Respined;
		}else{
			this.Respined=false;
		}
		
		//this.IdBlog=metadata.Source.IdBlog;
		
		
		this.init();
		//console.log(this.source.track);
	}
	

	
	Audio.prototype = {
		init:function(){
			
		},
		loadPlayerElement:function(){
			
			switch(this.type){
				case 'soundcloud':
					//console.log("initialize soundcloud...");
					//Soundcloud API:
					/*
					soundCloudPlay(this.source,function(audio){
						console.log("Audio object:");
						console.log(audio);
					});
					*/
					
					//HTML5 Audio:
					/*
					var _this = this;
					$.secureAjax({
						url: base_url+"songs/getStreamUrl/"+this.source.track.IdSource,
						dataType:'json',
						context: document.body
					}).done(function(data)
					{
						_this.source.audio = document.getElementById("soundcloudPlayer-"+_this.source.track.IdSong);
						
						if(!data.src){
							console.log("SOURCE ERROR FOR:"+_this.source.track.IdSource);
						}else{
							_this.source.audio.src = data.src;
						}
						
						
						_this.source.audio.addEventListener('ended', function(e) {
							player.skipFwd(); 	 
						}, false);
						
					});
					*/
					//console.log("iframe"+this.source.track.IdSource);
					this.source.audio = SC.Widget("iframe"+this.source.track.IdSong);
					
					this.source.audio.bind(SC.Widget.Events.FINISH, function(){
						player.skipFwd(); 
					});
					
					
					
					
				break;
				case 'youtube':
					
					this.source.audio = youTubeInit("youTubePlayer-"+this.source.track.IdSong,this.url);
					
					
				break;
				
			}
		},
		getDuration:function(){
			switch(this.type){
				case 'soundcloud':
					//Soundcloud API:
					//return this.source.track.Duration;
					
					
					//HTML5 Audio
					//return this.source.audio.duration;
					
					//Iframe
					
					return parseInt(this.source.track.Duration/1000);
				break;
				case 'youtube':
					return this.source.track.Duration;
					
				break;
			}
		},

		play:function(){
			
			
			
			switch(this.type){
				case 'soundcloud':
					
					
					if(this.source.audio!=null && this.source.audio){
						
						this.source.audio.play();
						/*this.source.audio.play({
							onfinish: function() {
							  player.skipFwd();
							}
							
						});*/
					}
					else{
						console.log("null audio?... this is a problem");
						//Soundcloud API:
						/*
						var _this = this;
						soundCloudPlay(this.source,function(a){
							console.log("callback called...");
							_this.source.audio=a;
							console.log(a);
							_this.source.audio.play({
								onfinish: function() {
								  player.skipFwd();
								}
							
							});
						});
						*/
					}
				break;
				case 'youtube':
					
					this.source.audio.play();
					this.source.audio.enterFullScreen();
					this.source.audio.positionFullscreenButton(0,0);
					
					
				break;
				
			}
	},
		getMarqueeTitle:function(){
			switch(this.type){
				case 'soundcloud':
					
					return this.source.track.Author+' - '+this.source.track.Title;
					
				break;
				case 'youtube':
					
					return this.source.track.Title;
					
				break;
				
			}
		},
		pause : function(){
			switch(this.type){
				case 'soundcloud':
					//console.log('Soundcloud pause:'+this.source.track.id);
					this.source.audio.pause();
					
				break;
				case 'youtube':
					console.log('Youtube pause');
					this.source.audio.pause();
					this.source.audio.exitFullScreen();
				break;
			}
		},
		stop : function(){
			switch(this.type){
				case 'soundcloud':
					if(this.source.audio != null && this.source.audio){
						
						//Soundcloud API:
						/*
						//this.source.audio.stop();
						this.source.audio.pause();
						this.source.audio.currentTime = 0;
						*/
						
						//HTML5 Audio:
						//this.source.audio.currentTime = 0;
						this.source.audio.pause();
						this.source.audio.seekTo(0);
						//this.loadPlayerElement();
						//this.source.audio.setAttribute('src', base_url+"songs/getFile/"+this.source.track.IdSource);
					}
					
				break;
				case 'youtube':
					if(this.source.audio != null){
						console.log("Yutube stop...");
						this.source.audio.exitFullScreen();
						this.source.audio.stop();
						//this.source.audio.setPosition(0);
						
					}
				break;
			}
		},
		setPosition:function(newPos){
			switch(this.type){
				case 'soundcloud':
					
					//Soundcloud API:
					/*
					this.source.audio.pause();
					this.source.audio.setPosition(newPos);
					var audio = this;
					this.source.audio.play();
					//window.setTimeout(function(){audio.play();player.startProgressBar();},5000);
					*/
					
					//HTML5 Audio:
					//this.source.audio.currentTime = newPos;
					
					//Iframe
					this.source.audio.seekTo(newPos*1000);
				break;
				case 'youtube':
					this.source.audio.setCurrentTime(newPos);
				break;
			}
		},
		getPosition:function(callback){
			switch(this.type){
				case 'soundcloud':
					if(this.source.audio==null)
						return 0;
					else{
						//Soundcloud API:
						//return this.source.audio.position;
						
						//HTML5 Audio
						//return this.source.audio.currentTime;
						
						//Iframe
						this.source.audio.getPosition(function(a){
							
									callback(parseInt(a/1000));
						
						});
						
						return true;
					}
				break;
				case 'youtube':
					callback(this.source.audio.currentTime);
					return true;
				break;
			}
		}
	
	};
	
	return Audio;
})();


function soundCloudPlay(audioSource,callback){
	
	if (typeof callback =="undefined") {
		var callback = function(a){};
	}
	
	console.log("soundcloud api for:"+audioSource.track.IdSource);
	
	SC.stream('/tracks/' + audioSource.track.IdSource, {useConsole:true,debugMode:true,useHTML5Audio: true},function(audio)
	{
		
		audioSource.audio=audio;
		
		callback(audio);
		/*
		audio.play(
		{
			onfinish: function() {
			  player.skipFwd();
			}
			
		});*/
	});
}