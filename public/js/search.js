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
    var search ={
		Tracks:[],
		
		type:'search',
		currentUrl:'',
		offset:0,
                query:'',
		init: function(query){
			this.query = encodeData(query);
			
			return this;
		},
		getTracks: function()
                {
			console.log('getSearchTracks initialized...');
			this.tracksCache=[];
			//this shouldnt be:
			$('.loading').show();
                        
			stream.empty();
			player.empty();
			
			this.currentUrl=base_url+"songs/search/"+this.query;
			this.getMoreTracks(0);
		
		},
		getMoreTracks: function(offset,callback){
			
			
			if(offset==this.offset && offset!=0){
				return;
			}
			//In the future when we are able to implement search pagination, we should avoid the following if...
			if(offset!=0){
				return;
			}
			callback = typeof callback !== 'undefined' ? callback : function(){};
			this.offset=offset
			$.secureAjax({
				url: this.currentUrl+"/"+this.offset,
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
				stream.addTracks(tracks);
				
				callback();
				$('.loading').hide();
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