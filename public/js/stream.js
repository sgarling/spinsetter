    
	// Stream manager (layout,player,stream sources(user profiles,blogs,top 100,etc),etc..)
    
    var stream ={
		tracksToAdd:0,
		tracksAdded:0,
		cards:{},
		masonryElements:{},
		templates:{},
		profile:null,
		
		masonry:null,
		
		cardsContainer:'.view-container',
		itemSelector:'.song-card',
       init:function(){
			
			this.masonryInit();
			
			this.loadTemplate('youtube',function(template){
				stream.templates['youtube'] = template;
			});
			this.loadTemplate('soundcloud',function(template){
				stream.templates['soundcloud'] = template;
			});
			
			this.profile=users;
			this.profile.getFollowedTracks();
			
			
	   },
	   masonryInit: function(){
			
						
			this.masonry={};
			
			
			this.masonry.itemWidth=250;
			this.masonry.gutter=10;
			
			this.masonry.visibleElements=0;
			
			this.masonry.currentOrder=0;
			
			this.masonry.lastWidth = $(this.cardsContainer).parent();
			$(window).resize(function(){
				if($(window).width() != stream.masonry.lastWidth){
					stream.masonry.lastWidth = $(window).width();
					stream.layoutAll();
				}
				fontResize();
			});
			
			
			this.masonrySizeInit();
			
			
			
			
	   },
	   masonrySizeInit: function(){
			/*
				We should add an own container div in the container, so to generalize this. 
				Then append the elements on the generated div which size/margin will be dinamically
				modified by us so it to be centered...
				
			*/
			
			
			this.masonry.cols=[];
			this.masonry.tops = [];
			
			this.masonry.colIndex = 0;
			
			var cols = this.getColumns();
			var left=0;
			for(var i =0; i<cols;i++){
				this.masonry.cols[i]=left;
				left+=this.masonry.itemWidth+this.masonry.gutter;
				this.masonry.tops[i]=0;
			}
			
			//Please check this after implementing the onResize event:
			var newMargin = Math.round(($(this.cardsContainer).parent().width()-this.masonry.cols.length*(this.masonry.itemWidth+this.masonry.gutter))/2);
			$(this.cardsContainer).css({marginLeft:newMargin,width:$(this.cardsContainer).parent().width()-newMargin });
	   },
	   masonryGetColsIndex:function(){
			var index = this.masonry.colIndex;
			this.masonry.colIndex++;
			if(this.masonry.colIndex>=this.masonry.cols.length){
				this.masonry.colIndex=0;
			}
			return index;
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
           setProfile: function(profile){
                this.profile = profile;
            },
	   //Adds track to a stream layout and push it into the player
	   //This should check by id if the track has been already rendered, in that case it shouldnt request the template again...
	   //Need to check for duplicate tracks in future implementations
        loadProfile: function(profile){
			$('body').scrollTo(0,500);
			
			this.setProfile(profile);
			
			
			this.hideProfileNavBar();
			
                        $('.profile-nav .user-info .profile-picture').css({'background-image':'url("'+base_url+profile.ProfilePicture+'")'});
              
			if(this.profile.type!='search'){
				$('.profile-nav-container .user-info .name').html(profile.getName());
				
				if(this.profile.type=='blogs'){
					//set url to profiles/blog/IdBlog
					history.setBlog(profile.getId());
					$('.profile-nav-container .user-info .location-link').show();
					$('.profile-nav-container .user-info .location-link').attr('href',profile.getLocation());
					$('.profile-nav-container .user-info .location-link .location').html(profile.getLocation());
					$('.profile-nav-container .stream-control .following').hide();
					
					$('.profile-nav .user-info .profile-picture').css({'background-image':'url("'+base_url+'blogsLogos/'+profile.getName()+'.jpg")'});
					
				}else{
					//set url to profiles/users/IdUser
					history.setUser(profile.getId());
					
					$('.profile-nav-container .user-info .location-link').attr('href','');
					$('.profile-nav-container .user-info .location-link .location').html('');
					$('.profile-nav-container .stream-control .following').hide();
				}
					
				
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
				
                                
                                    $('.profile-nav-container').show();
                                    /*.css({ 'top':-65 });
                                    .animate({'top': 0}, 800);*/
                                    
                                    var profileNav = $('.profile-nav-container').is(":visible")?$('.profile-nav-container').height():0;

                                    var newHeight = parseInt($('.nav-bar').css('padding-bottom')) + profileNav;
                        
                                
				$('.content-container').css({'margin-top':newHeight+'px'});
				
				
			}
			
			
			
			profile.getTracks();
			
			
		},
                hideProfileNavBar: function(){
                        
                        $('.profile-nav-container').hide();
                        
                        var profileNav = $('.profile-nav-container').is(":visible")?$('.profile-nav-container').height():0;

                        var newHeight = parseInt($('.nav-bar').css('padding-bottom')) + profileNav;
                        
                        $('.content-container').css({'margin-top':newHeight+'px'});
			
                },
		loadUserProfile: function(){
			//set url to profiles/users/own
			history.setOwn();
			
			$('body').scrollTo(0,500);
			
			$('.profile-nav-container .user-info .name').html(users.getName());
			$('.profile-nav-container .user-info .location-link').hide();
			$('.profile-nav-container .follow .follow-btn').hide();
			$('.profile-nav-container .follow .unfollow-btn').hide();
			$('.profile-nav-container .follow .done-btn').hide();
			$('.profile-nav-container .follow .edit-btn').show();
			
			$('.profile-nav .user-info .profile-picture').css({'background-image':'url("'+base_url+users.ProfilePicture+'")'});
			
                        $('.profile-nav-container').show();
			/*.css({ 'top':-65 });
			.animate({'top': 0}, 800);*/
                        
                        var profileNav = $('.profile-nav-container').is(":visible")?$('.profile-nav-container').height():0;

                        var newHeight = parseInt($('.nav-bar').css('padding-bottom')) + profileNav;
                        
                                
			$('.content-container').css({'margin-top':newHeight+'px'});
                        
			
			
			
			$('.profile-nav-container .stream-control .following').show();
			
			$('.profile-nav-container .stream-control .following .amount').html(users.getFollowingsCount());
			$('.profile-nav-container .stream-control .followers .amount').html(users.getFollowersCount());
			
			this.profile=users;
			player.newPlaylistFlag = true;
			this.profile.getRebloggedTracks();
			
		},
		loadMain: function(){
			
			history.setMain();		
			
			$('body').scrollTo(0,500);
			
			$('.profile-nav-container').hide();
			$('.content-container').css({'margin-top':'70px'});
			
			
		},
		
		loadMore: function(){
                    if(typeof this.profile.getMoreTracks !="undefined"){
			this.profile.getMoreTracks(this.masonry.visibleElements,function(){
				if($(this.cardsContainer+' .delete-btn').is(":visible")){
					$(this.cardsContainer+' .delete-btn').show();
				}
			});
                    }
		},
		editProfile:function(){
			$(this.cardsContainer+' .delete-btn').show();
		},
		doneEditProfile:function(){
			$(this.cardsContainer+' .delete-btn').hide();
		},
		addTrack: function(audio)
            {
			
			
			
			

			var stream = this;
			
			
			//this is just to load the templates in case they havent been loaded...
			if(typeof this.templates[audio.type] == 'undefined'){
				this.loadTemplate(audio.type,function(data){
					stream.templates[audio.type] = data;
					stream.addTrack(audio);
				});
				return;
			}
			
			
			if(audio.type=="introHowto" || audio.type=="introStart"){
				var IdSong = audio.IdCard;
				var track=audio;
			}else{
				var IdSong = audio.source.track.IdSong;
				var track=audio.source.track;
			}
			var setOrder=true;
			
			if(typeof this.cards[IdSong]=='undefined'){
				
				var output = Mustache.render(stream.templates[audio.type], track);
				this.cards[IdSong]=output;
				$(this.cardsContainer).append(this.cards[IdSong]);
				
				if(typeof audio.source != "undefined" && audio.source.track.Respined == "1"){
					$('#song'+IdSong).addClass('song-card-respined');
				}
			}
			if(typeof this.masonryElements[IdSong]=='undefined'){
				
				//this.masonryElements[audio.source.track.IdSong] = this.masonry.addItems($('#song'+audio.source.track.IdSong)[0])[0];
				this.masonryElements[IdSong]={};
				this.masonryElements[IdSong].element = $('#song'+IdSong);
				
			}else if(this.masonryElements[IdSong].visible){
				setOrder=false;
			}
			
			
			
			this.hideElement(IdSong);	
				
			this.masonryElements[IdSong].visible=true;
			
			
			
			
			
			//var setOrder=true;
			this.layoutElement(IdSong,this.masonryGetColsIndex(),setOrder);
			
			this.masonry.visibleElements++;
			
			
			
			
			if(audio.type!="introHowto" && audio.type!="introStart"){
				player.trackList.push(audio);
				audio.loadPlayerElement();
			
			}
			
			
			
        },
		addTracks: function(audios)
        {
			
			
			
			for(var i=0;i<audios.length;i++){
				this.addTrack(audios[i]);
				
			}
			
			//In case some pictures didn't loaded fast...:
			$(this.cardsContainer).imageready(function () {
			
				stream.layoutAll();
			});
			
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
                addFollowers: function(followers){
			$('.nav-bar .followers-btn .dropdown-ctrls ul').empty();
			for(var i=0;i<followers.length;i++){
				if(followers[i].TypeFollowed=='blogs'){
					$('.nav-bar .followers-btn .dropdown-ctrls ul').append('<li onClick="loadProfile(\''+followers[i].IdFollower+'\',\'blogs\')">'+followers[i].Name+'</li>');
				}else if(followers[i].TypeFollowed=='users'){
					$('.nav-bar .followers-btn .dropdown-ctrls ul').append('<li onClick="loadProfile(\''+followers[i].IdFollower+'\',\'users\')">'+followers[i].Name+'</li>');
				}
			}
		},
                /*
		addSearchResult: function(results){
			$('.nav-bar .search .dropdown-ctrls ul').empty();
			for(var i=0;i<results.length;i++){
				$('.nav-bar .search .dropdown-ctrls ul').append('<li onClick="loadProfile(\''+results[i].IdUser+'\',\'users\')">'+results[i].Username+'</li>');
				
			}
		},
		*/

		removeTrack:function(IdSong){
			
			$('#song'+IdSong).remove();
			this.tracksAdded--;

			return true;
		},
		unSelectAllTracks:function(){
			$(this.itemSelector).removeClass('song-card-selected');
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
		
		userProfilePicture: function(ProfilePicture){
			console.log("loading user profile picture...");
			
			this.navbarProfilePicture(ProfilePicture);
                        this.respinProfilePicture(ProfilePicture);
			
		},
                respinProfilePicture: function(ProfilePicture){
			$(".song-card .respin .title").css("background-image","url('"+base_url+ProfilePicture+"')");
			
		},
                navbarProfilePicture: function(ProfilePicture){
			$(".profile-btn, .profile-nav .song-card .respin .title, .user-info .profile-picture").css("background-image","url('"+base_url+ProfilePicture+"')");
			
		},
		empty: function(){
			
			this.masonryInit();
			
			for(i in this.masonryElements){
				this.hideElement(i);
				this.masonryElements[i].visible=false;
			}
			
			
		},
		getElementByOrder: function(currentIndex){
			for(k in this.masonryElements){
				if(this.masonryElements[k].order==currentIndex && this.masonryElements[k].visible){
					return k;
				}
				
			}
			return false;
		},
		layoutAll: function(){
			//I will use this function probably to layout everything again once the images are loaded, or i could do a collapse mechanism
			//to relayout an element when it gets collapsed.
			//var j =0;

			this.masonrySizeInit();
			
			var k = 0;
			var j=0;
			var setOrder=false;
			while(k=this.getElementByOrder(j)){
				//console.log(j+'.'+k+' '+this.masonryGetColsIndex());
				j++;
				this.layoutElement(k,this.masonryGetColsIndex(),setOrder);
			}
			
			
					
		},
		layoutElement: function(elementIndex,colIndex,setOrder){
			var k = elementIndex;
			var j = colIndex;
			if(setOrder){
				this.masonryElements[k].order = this.masonry.currentOrder++;
			}
			this.masonryElements[k].left=this.masonry.cols[j];
			this.masonryElements[k].top = this.masonry.tops[j]+this.masonry.gutter;
			this.masonry.tops[j] = this.masonryElements[k].top+this.masonryElements[k].element.height();
			this.masonryElements[k].element.css({top: this.masonryElements[k].top, left: this.masonryElements[k].left,position:'absolute'});
			//debug order:
			
			//$("#song"+k+" .song-title").html(this.masonryElements[k].order+". "+$("#song"+k+" .song-title").html());
		},
		hideElement: function(elementIndex){
			this.masonryElements[elementIndex].top=-1000;
			this.masonryElements[elementIndex].left=-1000;
			this.masonryElements[elementIndex].element.css({top: this.masonryElements[elementIndex].top, left: this.masonryElements[elementIndex].left,position:'absolute'});
			
		},
		getColumns: function(){
			return Math.floor($(this.cardsContainer).parent().width()/this.masonry.itemWidth);
		}
	};



