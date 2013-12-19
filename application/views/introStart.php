		<div class="song-card intro-card song-card-respined"  id="song{{IdCard}}" style="height:300px">
			<div class="card" style="padding-left:10px;">
				<video id="youTubePlayer-{{IdCard}}" type='video/youtube' style="z-index:2" height="210"></video>
				
				<div style="cursor:pointer;position:absolute;top:30px;left:0;width:100%;height:150px;padding-top:60px;z-index:5;" onClick="toggleIntro();">
				<img src="img/card-play-icon.png" />
				</div>
				<script>
				var introStart = youTubeInit("youTubePlayer-{{IdCard}}","http://youtu.be/b9IGVtmqBFo");
				
				function toggleIntro(){
					if(introStart.paused){
						introStart.play();
						
						$("#song{{IdCard}}").removeClass("song-card-respined");
						$("#song{{IdCard}}").addClass("song-card-selected");
						
					}else{
						introStart.pause();
						$("#song{{IdCard}}").removeClass("song-card-selected");
						
					}
				}
				
				</script>
				<div class="bottom">
					<div  class="row gotit-btn" onClick="users.removeIntro('{{status}}',{{IdCard}})">Got it!</div>
				</div>
			</div>
		</div>
