/*
Prototype Profile:
IdUser
Username
type
Tracks
currentUrl
offset
getName()
getLocation()
getFollowersCount()
getTracks()

*/
    var friends ={
		tracksCache:[],
		IdUser:0,
		Username:'',
                Firstname:'',
                Lastname:'',
		Followers:[],
		type:'users',
		currentUrl:'',
		offset:0,
        //Returns an array of Soundcloud 'track' objects that correspond to the track urls in the 'blog' object
        getUser: function(username){
			
			var friend = this;
			$.secureAjax({
				url: base_url+"users/getFriendById/"+username,
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				friend.IdUser=data.IdUser;
				friend.Username=data.Username;
                                friend.Firstname=data.Firstname;
                                friend.Lastname=data.Lastname;
				friend.Followers = data.Followers;
                                friend.ProfilePicture = data.ProfilePicture;
				
			});
			
			return this;
		},
		getFollowersCount: function(){
			return this.Followers.length;
		},
		getId: function(){
			return this.IdUser;
		},
		getName: function(){
			return this.Firstname+" "+this.Lastname;
		},
		getLocation: function(){
			return '';
		},
		getTracks: function()
        {
			console.log('getFriendTracks initialized...');
			this.tracksCache=[];
			//this shouldnt be:
			$('.loading').show();
			stream.empty();
			player.empty();
			
			this.currentUrl=base_url+"songs/getByFriend/"+this.IdUser;
			this.getMoreTracks(0);
		
		},
		getMoreTracks: function(offset,callback){
			
			
			if(offset==this.offset && offset!=0){
				return;
			}
			callback = typeof callback !== 'undefined' ? callback : function(){};
			this.offset=offset;
						var _this = this;
                        
                        if (_this.tracksCache.length==0){
                              var limit = 40;      
                        }else{
                              var limit = 10;
                              stream.addTracks(_this.tracksCache.shift());
                                    
                              callback();
                              $('.loading').hide();
                        }
			$.secureAjax({
				url: this.currentUrl+"/"+this.offset+"/"+limit,
				dataType:'json',
				/*async:false,*/
				context: document.body
			}).done(function(data)
			{
				var tracksInfo = data;
				
				console.log('data received:'+tracksInfo.length.toString());
				
				
				var tracks =[];
				for(var i=0;i<tracksInfo.length ; i++)
				{
					var metadata = tracksInfo[i];
					
					
					var track = new Audio(metadata);
					
					tracks[i]=track;
				
					
				}
								if (_this.tracksCache.length==0) {
                                    //code
									console.log("slice the cache");
                                    var current = tracks.slice(0,10);
									_this.tracksCache[0] = current;
									
									current = tracks.slice(10,20);
									_this.tracksCache[1] = current;
									
									current = tracks.slice(20,30);
									_this.tracksCache[2] = current;
									
									current = tracks.slice(30,tracks.length);
									_this.tracksCache[3] = current;
									
									
                                    
                                    stream.addTracks(_this.tracksCache.shift());
                                    
                                    callback();
                                    $('.loading').hide();
                                }else{
                                    _this.tracksCache.push(tracks);
                                    
                                }
			}); 
		},
	   //Adds respun track to given profile
	   //Need to check for duplicate tracks in future implementations
        addTrackToStream: function(audio)
        {

			this.Tracks.push(audio);
			
			stream.addTrack(audio);
			
        },
		
		addTracksToStream: function(){
			stream.empty();
			stream.addTracks(this.Tracks);
		}
        
	};