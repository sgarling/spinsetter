	
	
		<div class="song-card"  id="song{{IdSong}}">
			<div class="card">
				<div class="show-respin-btn" onClick=" showRespinCard('#song{{IdSong}}');/*users.reblog('{{IdSong}}')*/">
					Respin
				</div>
			
				<div class="delete-btn" onClick="users.unReblog('{{IdSong}}');"></div>
				<div class="song-title" >{{Title}}</div>
				<div class="card-prof-pic" onClick="loadProfile('{{spiner.IdSpiner}}','{{spiner.TypeSpiner}}');" title="{{spiner.NameSpiner}}" style="background-image:url('<?=base_url()?>{{spiner.Picture}}');">
				
				</div>
				<div class="song-artist" >{{Author}}</div>
				<div class="clear"></div>
				
				<div class="song-artwork" <? if($type=='youtube'):?>style="height:212px;" <?endif;?> >
					
					<? if($type=='youtube'):?>
						<!-- This is a transparent div just to avoid user clicks in the youtube video and it starts playing -->
						<div style="position:absolute;top:10%;left:0;width:100%;height:90%;z-index:2;"></div>
						<div style="position:absolute;top:0;left:30%;width:70%;height:10%;z-index:2;"></div>
						<div class="video-container">
							<video id="youTubePlayer-{{IdSong}}" type='video/youtube' style="z-index:2" height="208" width="250" >
								
							</video>
						</div>
					<? else: ?>
						<img src="<?=base_url()?>artwork/{{Artwork}}" onError="this.src='<?=base_url()?>img/logo.login.png'" >
						<audio id="soundcloudPlayer-{{IdSong}}" type="audio/mp3"  ></audio>
						<iframe id="iframe{{IdSong}}" width="0" height="0" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/{{IdSource}}"></iframe>
						
						<a href="<?=base_url()?>songs/source/{{IdSong}}" target="_blank" class="card-logo">
						<img src="<?=base_url()?>img/{{TypeSource}}-logo.png"/>
						</a>
					<? endif;?>
						
					
					<div class="card-play-pause" onClick="playFromCard('{{IdSource}}')"><img src="<?=base_url()?>img/card-play-icon.png" /></div>
					
				</div>
				</div>
				
				<div class="respin">
					<h1 class="row title selected" onClick="$(this).toggleClass('selected');users.togglePublicRespin();">
                                        My Profile
					</h1>
					<ul class="friends">
					</ul>
					
					<div class="bottom">
						<form action="#" class="invite" onSubmit="sendInvite('{{IdSong}}',$(this).serializeArray()[0].value); return false;" >
										<!--
										<input class="row email" title="Invite this person" name="email"  placeholder="Invite a friend (Enter Email)"  type="text" />
										-->
										<p class="row">To invite a friend, type their email into the search field</p>
						</form>
						<form action="#" onSubmit="searchUsers('#song{{IdSong}}',$(this).serializeArray()[0].value); return false;" >
											<input class="row keywords" title="Search for friends" name="keywords" placeholder="Search for friends"  type="text" />
											<img src="<?=base_url()?>img/search-btn.png" title="Search for friends" width="23" height="23" onClick="$(this).parents('form:first').submit();" class="form-btn" />
						</form>
						<div  class="row respin-btn" onClick="respinSong('#song{{IdSong}}');">Respin</div>
					</div>
				</div>
			
			
		</div>
	

