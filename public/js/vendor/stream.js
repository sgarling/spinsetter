    
	console.log("LA CONSCHA DE LA RE MIL LORA");
	// Stream manager (layout,player,stream sources(user profiles,blogs,top 100,etc),etc..)
    
    var stream ={
		tracksToAdd:0,
		tracksAdded:0,
		cards:{},
		masonryElements:{},
		templates:{},
		profile:null,
		
		masonry:null,
		
       init:function(){
			/*
			this.masonry = new Masonry(document.querySelector('.view-container'), {
			  // options
			  hiddenStyle:{width:0,height:0,overflow:'hidden'},
			  /*hiddenStyle:{marginTop:'-99999999px'},
			  visibleStyle:{marginTop:0},
			  
			  itemSelector: '.song-card'
			});
			*/
			//this.masonry.bindResize();
			this.loadTemplate('youtube',function(template){
				stream.templates['youtube'] = template;
			});
			this.loadTemplate('soundcloud',function(template){
				stream.templates['soundcloud'] = template;
			});
			
	   },
	   loadTemplate:function(type,callback){
			$.secureAjax({
					url: base_url+"main/card/"+type,
					dataType:'html',
					async:false,
					context: document.body
				}).done(function(data)
				{
					callback(data);
					
				});
	   },
	   //Adds track to a stream layout and push it into the player
	   //This should check by id if the track has been already rendered, in that case it shouldnt request the template again...
	   //Need to check for duplicate tracks in future implementations
        loadProfile: function(profile){
			$('body').scrollTo(0,500);
			
			this.profile = profile;
			
			
			$('.content-container').css({'margin-top':'70px'});
			$('.profile-nav-container').hide();
			$('.profile-nav .user-info').css({'background-image':'url("img/svg/profile-default.svg")'});
			if(this.profile.type!='genres'){
				$('.profile-nav-container .user-info .name').html(profile.getName());
				$('.profile-nav-container .user-info .location').html(profile.getLocation());
				
				if(this.profile.type=='blogs'){
					
					$('.profile-nav-container .user-info .location-link').attr('href',profile.getLocation());
					$('.profile-nav-container .stream-control .following').hide();
					
					$('.profile-nav .user-info').css({'background-image':'url("blogsLogos/'+profile.getName()+'.jpg")'});
					
				}else{
					$('.profile-nav-container .user-info .location-link').attr('href','');
					$('.profile-nav-container .stream-control .following').hide();
				}
				
				
				$('.profile-nav-container .stream-control .followers .amount').html(this.profile.getFollowersCount());
			
				$('.profile-nav-container .stream-control .posted .amount').html(profile.Tracks.length);
						
				
				if(users.isFollowing(this.profile)){
					$('.profile-nav-container .follow .edit-btn').hide();
					$('.profile-nav-container .follow .follow-btn').hide();
					$('.profile-nav-container .follow .done-btn').hide();
					$('.profile-nav-container .follow .unfollow-btn').show();
				}else{
					$('.profile-nav-container .follow .follow-btn').show();
					$('.profile-nav-container .follow .unfollow-btn').hide();
					$('.profile-nav-container .follow .edit-btn').hide();
					$('.profile-nav-container .follow .done-btn').hide();
				}
				
				$('.content-container').css({'margin-top':'140px'});
				$('.profile-nav-container')
				.show()
				.css({ 'top':-65 })
				.animate({'top': 0}, 800);
			}
			
			profile.getTracks();
			
			
		},
		/*
		loadUserProfile: function(){
			
			
			
			
			$('body').scrollTo(0,500);
			
			$('.profile-nav-container .user-info .name').html(users.getName());
			$('.profile-nav-container .user-info .location').html(users.getLocation());
			
			$('.profile-nav-container .follow .follow-btn').hide();
			$('.profile-nav-container .follow .unfollow-btn').hide();
			$('.profile-nav-container .follow .done-btn').hide();
			$('.profile-nav-container .follow .edit-btn').show();
			
			$('.profile-nav .user-info').css({'background-image':'url("img/svg/profile-default.svg")'});
			
			$('.content-container').css({'margin-top':'140px'});
			$('.profile-nav-container')
			.show()
			.css({ 'top':-65 })
			.animate({'top': 0}, 800);
			
			
			$('.profile-nav-container .stream-control .following').show();
			
			$('.profile-nav-container .stream-control .following .amount').html(users.getFollowingsCount());
			$('.profile-nav-container .stream-control .followers .amount').html(users.getFollowersCount());
			
			this.profile=users;
			player.newPlaylistFlag = true;
			this.profile.getRebloggedTracks();
			
		},*/
		loadMain: function(){
			$('body').scrollTo(0,500);
			
			$('.profile-nav-container').hide();
			$('.content-container').css({'margin-top':'70px'});
			
			
			History.pushState(null,null,"");
			
			
			this.profile=users;
			
			this.profile.getFollowedTracks();
			
			
			
			
		},
		
		loadMore: function(){
			this.profile.getMoreTracks(lenghtOfObject(this.cards),function(){
				if($('.view-container .delete-btn').is(":visible")){
					$('.view-container .delete-btn').show();
				}
			});
		},
		editProfile:function(){
			$('.view-container .delete-btn').show();
		},
		doneEditProfile:function(){
			$('.view-container .delete-btn').hide();
		},
		addTrack: function(audio)
        {
			
			
			var track=audio.source.track;
			

			var stream = this;
			
			
			
			if(typeof this.templates[audio.type] == 'undefined'){
				this.loadTemplate(audio.type,function(data){
					stream.templates[audio.type] = data;
					stream.addTrack(audio);
				});
				return;
			}
			
			if(typeof this.cards[audio.source.track.IdSong]=='undefined'){
				var output = Mustache.render(stream.templates[audio.type], track);
				this.cards[audio.source.track.IdSong]=output;
				$('.view-container').append(this.cards[audio.source.track.IdSong]);
				
				//this.masonryElements[audio.source.track.IdSong] = this.masonry.addItems($('#song'+audio.source.track.IdSong));
				
			}
			
			//$('#song'+audio.source.track.IdSong).css({overflow:'visible',width:250,height:'auto'});
			//$('#song'+audio.source.track.IdSong).css({display:'block'});
			
			
			var item = this.masonryElements[audio.source.track.IdSong];
			
			/*
			if(item.isHidden){
				console.log(item.masonryElements[audio.source.track.IdSong]);
			}
			*/
			//this.masonry.layoutItems([this.masonryElements[audio.source.track.IdSong]],true);
			//this.masonry.reloadItems();
			//this.masonry.layout();
			
			
			player.trackList.push(audio);
			audio.loadPlayerElement();
			
			
			
			
        },
		addTracks: function(audios)
        {
			
			for(var i=0;i<audios.length;i++){
				this.addTrack(audios[i]);
				
			}
			//this.masonry.layout();
        },
		/*
		addCards: function(){
			
			for(var k in this.cards){
				
				this.showCard(this.cards[k],k);
			}
		},*/
		showCard: function(card,IdSong,pushCards){
			
			
			
			//this.masonry.addItems($('#song'+IdSong));
			//this.masonry.layout();
			
			return ;
		},
		
		addFollowings: function(followings){
			$('.nav-bar .following-btn .dropdown-ctrls ul').empty();
			for(var i=0;i<followings.length;i++){
				if(followings[i].TypeFollowed=='blogs'){
					$('.nav-bar .following-btn .dropdown-ctrls ul').append('<li onClick="loadProfile(\''+followings[i].IdFollowed+'\',\'blogs\')">'+followings[i].Name+'</li>');
				}else if(followings[i].TypeFollowed=='users'){
					$('.nav-bar .following-btn .dropdown-ctrls ul').append('<li onClick="loadProfile(\''+followings[i].IdFollowed+'\',\'users\')">'+followings[i].Name+'</li>');
				}
			}
		},
		addSearchResult: function(results){
			$('.nav-bar .search .dropdown-ctrls ul').empty();
			for(var i=0;i<results.length;i++){
				$('.nav-bar .search .dropdown-ctrls ul').append('<li onClick="loadProfile(\''+results[i].IdUser+'\',\'users\')">'+results[i].Username+'</li>');
				
			}
		},
		
		cardsEmpty:function(){
		
			//this.cards={};
			//this.masonry.hide(this.masonry.items);
			
			return;
		},
		setCardsContainers:function(){
			/*var colsNum = layoutColumns();
			
			$('.view-container').empty();
			
			for(var i=0;i<colsNum;i++){
				$('.view-container').append("<div class='col col-"+i+"' ></div>");
			}
			
			$('.view-container').append('<div class="clear"></div>');
			*/
			return;
		},
		removeTrack:function(IdSong){
			
			$('#song'+IdSong).remove();
			this.tracksAdded--;
			//streamImagesLoaded();
			return true;
		},
		unSelectAllTracks:function(){
			$('.song-card').removeClass('song-card-selected');
		},
		selectTrack: function(id){
			this.unSelectAllTracks();
			
			$('#song'+id).addClass('song-card-selected');
			
			
		},
		scrollToTrack: function(){
			id = player.currentTrack.source.track.IdSong;
			/* We should adjust the over parameter to a relative number in reference to the nav-bar*/
			$('body').scrollTo($('#song'+id),500,{over:-0.4});
		},
		empty: function(){
			
			//empty stream
			//$('.view-container').empty();
			//this.masonry.hide(this.masonry.items);
			this.cardsEmpty();
			
			
		}
	};



