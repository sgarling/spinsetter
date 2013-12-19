    // static users
 
//Okery, after doing aaaaalll the site with Usernmae and Email, the client ask for changing into only using the email. This is not convinient at all
//Now we should totally eliminate Username from everywhere, but as its a very big change, we are duplicating email value into the username . 
    var users ={
		tracksCache:[],
		
		IdUser:0,
		Token:false,
		Username:'',
		Firstname:'',
		Lastname:'',
		Email:'',
		Followings:[],
		Followers:[],
		type:'users',
		currentUrl:'',
		ProfilePicture:'',
		Status:'',
		
		offset:0,
		
		usersToRespin:[],
		songToRespin:0,
		publicRespin:false,
		
		getToken: function(){
			return this.Token;
		},
		login: function(email,password){
			var user = this;
			$.secureAjax({
				url: base_url+"login/get",
				data:{'Email':email,'Password':password},
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				if(!data.Token){
					console.log("Login Failed...");
				}else{
					user.Email=data.Email;
					user.Token=data.Token;
					//user.Password=data.Password;
					
					user.getUser(user.Email);
				}
				
			});
			return this.Token;
			
		},
		logout: function(){
			sessionStorage.clear();
			return true;
		},
        getUser: function(email)
        {
           // var profileIndex = getProfileIndex(username);
            //return profiles[profileIndex];
			if(typeof email == 'undefined')
				email = this.Email;
				
			var user = this;
			$.secureAjax({
				url: base_url+"users/get",
				data:{"Email":email},
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				
				user.IdUser=data.IdUser;
				user.Username=data.Username;
				user.Firstname=data.Firstname;
				user.Lastname=data.Lastname;
				user.Followings = data.Followings;
				user.Followers = data.Followers;
				user.ProfilePicture = data.ProfilePicture;
				user.Status = data.Status;
				
				
                                
				stream.addFollowings(user.Followings);
                                stream.addFollowers(user.Followers);
				
				stream.userProfilePicture(user.ProfilePicture);
				
			});
			
			return this;
        },
		getFollowingsCount: function(){
			return this.Followings.length;
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
		getEmail: function(){
			return this.Email;
		},
		
		empty: function(){
			this.tracksCache=[];
		},
        //Returns an array of Soundcloud 'track' objects that correspond to the track urls in the 'profiles' object
        getFollowedTracks: function()
        {
			console.log('getTracks initialized...');
			
			
			this.empty();
			stream.empty();
			player.empty();
			$('.loading').show();
			
			if(this.Status!="registered"){
				this.getIntroCards();
			}
			
			this.currentUrl=base_url+"songs/getFollowed/"+this.IdUser;
			this.offset=0;
			this.getMoreTracks(this.offset);
		
		},
		getIntroCards: function(){
			$('.loading').show();
			$.secureAjax({
				url: "users/getIntro/"+this.IdUser+"/"+this.Status,
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(cards)
			{
				
				stream.addTracks(cards);
				
				$('.loading').hide();
			});
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
				
				var tracks = [];
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
		getRebloggedTracks: function()
        {
			console.log('getTracks initialized...');
			$('.loading').show();
			this.empty();
			stream.empty();
			player.empty();
			this.currentUrl=base_url+"songs/getReblogged/"+this.IdUser;
			this.offset=0;
			this.getMoreTracks(this.offset);
			
		
		},
                
		addTracksToStream: function(){
			$('.loading').show();
			stream.empty();
			
			stream.tracksToAdd = this.Tracks.length;
			stream.tracksAdded = 0;
			
			stream.addTracks(this.Tracks);
		},
		reblog: function(IdSong){
			$.secureAjax({
				url: base_url+"users/reblogSong/"+IdSong+"/"+this.IdUser,
				dataType:'json',
				context: document.body
			}).done(function(data)
			{
				console.log("Song reblogged...");
				
			});
		},
		unReblog: function(IdSong){
			$.secureAjax({
				url: base_url+"users/unReblogSong/"+IdSong+"/"+this.IdUser,
				dataType:'json',
				context: document.body
			}).done(function(data)
			{
				console.log("Song unReblogged...");
				//should we remove the song from the player if there?..
				
				console.log(stream.removeTrack(IdSong));
			});
		},
		invite: function(email,IdSong){
			var msg='';
                        if (email=="") {
                                    return "Email empty.";
                        }
			$.secureAjax({
				url: base_url+"users/invite",
				data:{'EmailInvite':email,IdUser:this.IdUser,"IdSong":IdSong},
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				msg=data.msg;
			});
			
			return msg;
		},
                sendResetRequest: function(email){
                        $.secureAjax({
				url: base_url+"login/resetRequest",
				data:{'Email':email},
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				console.log(data);
                                $("#forgot .msg").html(data.msg);
			});
                },
                resetPassword: function(password,repeat){
                        
                        if (password=="" || password!=repeat) {
                                    
                                    $("#reset .msg").html("You entered a wrong New Password.");
                                    
                                    return;
                        }
                        
                        $.secureAjax({
				url: base_url+"users/resetPassword",
				data:{'Email':this.Email,'Password':password},
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
                                $("#reset .msg").html(data.msg);
			});
                },
		recentActivityFriends: function(){
			
			
			
			var users=[];
			$.secureAjax({
				url: base_url+"users/recentActivityFriends",
				data:{'IdUser':this.IdUser},
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				users = data;
				
			});
			
			return users;
		},
		clearToRespin: function(){
			this.usersToRespin=[];
			this.songToRespin=0;
			this.publicRespin=false;
		},
		toggleUserToRespin: function(IdUser){
			
			var index = this.usersToRespin.indexOf(IdUser);
			if(index==-1){
				this.usersToRespin.push(IdUser);
			}else{
				this.usersToRespin.splice(index, 1);
			}
		},
		togglePublicRespin: function(){
			
			if(!this.publicRespin){
				this.publicRespin=true;
			}else{
				this.publicRespin=false;
			}
		},
		respin: function(){
			
			if(this.usersToRespin.length >0 && this.songToRespin!=0){
				$.secureAjax({
					url: base_url+"users/respin",
					data:{IdUser:this.IdUser,users:this.usersToRespin,song:this.songToRespin},
					dataType:'json',
					context: document.body
				}).done(function(data)
				{
					console.log(data);
					
				});
			}
			if(this.publicRespin){
				this.reblog(this.songToRespin);
			}
		},
		unRespin: function(IdSong){

			var status;
			
			$.secureAjax({
					url: base_url+"users/unRespin",
					data:{IdSong:IdSong,IdUser:this.IdUser},
					dataType:'json',
					async:false,
					context: document.body
				}).done(function(data)
				{
					//I think is better that stream class treats this html-css staff...
					$("#song"+IdSong).removeClass("song-card-respined");
					
					status = data.Status;
					
				});
			
			return status;
			
		},
		gotIt: function(IdCard){
			$.secureAjax({
				url: base_url+"users/gotIt/",
				data:{'IdUser':this.IdUser},
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				stream.hideElement(IdCard);
				stream.removeTrack(IdCard);
				stream.layoutAll();
				
			});
			
		},
		
		searchUsers: function(keyword,limit){
                        if(typeof limit == "undefined"){
                                var limit = "";
                        }
			var users=[];
                        
                        keyword = encodeData(keyword);
                        
			$.secureAjax({
				url: base_url+"users/search/"+keyword+"/"+limit,
				dataType:'json',
				async:false,
				context: document.body
			}).done(function(data)
			{
				users = data;
				
			});
			
			return users;
		},
		removeTrack: function(IdSong){
			
			
			
		},
		followProfile: function(profile){
			if(profile.type=='blogs'){
				var TypeFollowed = profile.type;
				var IdFollowed = profile.IdBlog;
			}else{
				var TypeFollowed = 'users';
				var IdFollowed = profile.IdUser;
			}
			var user = this;
			$.secureAjax({
				url: base_url+"users/followProfile/"+this.IdUser+"/"+IdFollowed+"/"+TypeFollowed,
				dataType:'json',
				context: document.body
			}).done(function(data)
			{
				console.log(data);
				if(data!=false){
					//alert("Following a new profile...");
					user.Followings.push(data);
					loadProfile(data.IdFollowed,data.TypeFollowed);
					stream.addFollowings(user.Followings);
					console.log("Following Profile...");
				}
			});
		},
		unfollowProfile: function(profile){
			if(profile.type=='blogs'){
				var TypeFollowed = profile.type;
				var IdFollowed = profile.IdBlog;
			}else{
				var TypeFollowed = 'users';
				var IdFollowed = profile.IdUser;
			}
			
			var user = this;
			$.secureAjax({
				url: base_url+"users/unfollowProfile/"+this.IdUser+"/"+IdFollowed+"/"+TypeFollowed,
				dataType:'json',
				context: document.body
			}).done(function(data)
			{
				if(data!=false){
					//alert("Profile unfollowed");
					//user.Followings.push(data);
					
					
					for(var i = 0 ; i<user.Followings.length; i++){
						if(user.Followings[i].IdFollowed == data.IdFollowed && user.Followings[i].TypeFollowed==data.TypeFollowed){
							user.Followings.splice(i,1);
						}
					}
					loadProfile(data.IdFollowed,data.TypeFollowed);
					stream.addFollowings(user.Followings);
					console.log("Unfollowing Profile...");
				}
			});
		},
		isFollowing: function(profile){
			
			if(profile.type=='blogs'){
				var TypeFollowed = profile.type;
				var IdFollowed = profile.IdBlog;
			}else if(profile.type=='users'){
				var TypeFollowed = 'users';
				var IdFollowed = profile.IdUser;
			}else{
				return false;
			}
			
			return _.find(this.Followings, function(item){ return (item.IdFollowed == IdFollowed && item.TypeFollowed==TypeFollowed); });
		}
	
	};