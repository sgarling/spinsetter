<body>
<div style="overflow:hidden;width:1px;height:1px;position:absolute;top:100%;right:0;">
<video id="youTubePlayer" type='video/youtube' src='http://www.youtube.com/watch?v=q6XoSzmLcZs'></video>
</div>
    <div class="container">
        <div class="nav-container">
            <div class="nav-bar" >
                <div class="logo" onClick="loadUser();" style="cursor:pointer;"></div>
                
                
				<!--
                <div class="genres-btn">
					<div class="genres-btn-txt">Genres</div>
					<div class="dropdown-ctrls">
						<ul>
							<li onClick="loadGenre('indie');">Indie/Alternative</li>
							<li onClick="loadGenre('electr');">Electronica/Dance</li>
							<li onClick="loadGenre('hip');">Hip Hop/Rap</li>
						</ul>
					</div>
				</div>
				-->
                                
				<div class="blogs-btn">
					<div class="blogs-btn-txt">Channels</div>
					<div class="dropdown-ctrls">
					<? foreach($blogs as $k=>$bs): ?>
					<ul>
					    <li class="title"><?=$k?></li>
					    <? foreach($bs as $blog): ?>
					    <li onClick="loadProfile('<?=$blog->IdBlog?>','blogs')"><?=$blog->NameBlog?></li>
					    <? endforeach; ?>
					</ul>
					<? endforeach; ?>
					<!--
					    <ul>
					    <? $c = floor(count($blogs)/3);?>
					    <? foreach($blogs as $k=>$blog): ?>
					    <? if($k!=0 && $k%$c==0) : ?>
					    </ul><ul>
					    <? endif; ?>  
					    <li onClick="loadProfile('<?=$blog->IdBlog?>','blogs')"><?=$blog->NameBlog?></li>
					    
					    <? endforeach;?>
					    </ul>
					-->
					
                                        <div style="clear:both"></div>
					</div>
				</div>
				<div class="following-btn">
					<div class="following-btn-txt" onClick="loadUser();" >Following</div>
					<div class="dropdown-ctrls">
                                            <ul>
                                            
                                            </ul>
					</div>
				</div>
				<div class="followers-btn">
                                    <div class="followers-btn-txt" >Followers</div>
                                    <div class="dropdown-ctrls">
                                            <ul>
                                            
                                            </ul>
                                    </div>
                                </div>
                                
				<div class="profile-btn"  >
					<div onClick="stream.loadUserProfile();" class="profile-btn-link"> </div>
                    <!--<div class="notification-circle profile"><p>4</p></div>-->
					<div class="dropdown-ctrls" >
						<ul>
							<li>
								<a href="#" onClick="showFindFriends();">
									Find Friends
								</a>
							</li>
							<li>
								<a href="#" onClick="uploadPicture();return false;">Upload Picture</a>
							</li>
							<li>
								<a href="https://docs.google.com/forms/d/1JtMzXzyJFMqIh45-17hF4CKdbyvLycricQfug88THQA/viewform" target="_blank">
									Feedback
								</a>
							</li>
							<li>
								<a href="http://blog.spinsetter.com/" target="_blank">
									About
								</a>
							</li>
							<li>
								<a href="#" onClick="showTOU();">
									Terms of Use
								</a>
							</li>
							<li>
								<a href="#" onClick="showPrivacyPolicy();">
									Privacy Policy
								</a>
							</li>
							<li>
								<a href="#" onClick="logout();return false;">Logout</a>
							</li>
							
						</ul>
					</div>
                </div>
				<!--
				<div class="calendar-btn" onClick="loadCalendar();">
                   <div class="notification-circle calendar"><p>3</p></div>
                </div>
				-->
				<div class="nav-bar-ctrls" ng-controller="PlayerCtrl">
                    <div class="rwd-btn" onClick="skipBack();"></div>
                    <div class="play-btn" onClick="playPause();"></div>
                    <div class="ffwd-btn" onClick="skipFwd()"></div>
                    <div class="dropdown-ctrls" >
                        <div class="song-title">
                            <marquee behavior="scroll" direction="left" scrollAmount="2" scrolldelay="1"></marquee>
						</div>
                        <div class="progress-section">
                            <div class="song-progress-wrapper">
                                <div class="progress-bar"></div>
                                <div class="song-progress"></div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="search">
					<form onSubmit="searchYouTube();return false;">
						<div class="search-btn" onclick="searchYouTube();"></div>
						<div class="search-input" >
						<input id="search-text" type="text" placeholder="Search" >
						</div>
					</form>
                </div>
                
                
                
            </div>
        
			<div class="profile-nav-container">
				<div class="profile-nav">
					<div class="user-info">
						<div class="profile-picture"></div>
						<p class="name"></p>
						<a class="location-link" target="_blank"><p class="location"></p></a>
					</div>
					
					<div class="stream-control">
						<div class="follow">
							<div class="follow-btn" Onclick='users.followProfile(stream.profile);$(this).hide();$(this).next().show();'>Follow</div>
							<div class="unfollow-btn" Onclick='users.unfollowProfile(stream.profile);$(this).hide();$(this).prev().show();'>Unfollow</div>
							<div class="edit-btn" Onclick='stream.editProfile();$(this).hide();$(this).next().show();'>Edit</div>
							<div class="done-btn" Onclick='stream.doneEditProfile();$(this).hide();$(this).prev().show();'>Done</div>
						</div>
						<!--
						<div class="posted" ><p class="type">Stream</p><p class="amount">0</p></div>
						<div class="likes" ng-click="viewLikes()"><p class="type">Likes</p><p class="amount">0</p></div>
						<div class="playlists"><p class="type">Playlists</p><p class="amount">0</p></div>
						-->
					</div>
				</div>
			</div>
		
		</div>
        
		<div class="clear"></div>
		<div class="loading">
			<img src="<?=base_url()?>img/loading.gif" />
		</div>
		<div class="tou">
		<iframe src="<?=base_url()?>tus.html" width="80%" height="80%"></iframe>
		</div>
		<div class="privacy-policy">
		<iframe src="<?=base_url()?>privacy-policy.html" width="80%" height="80%"></iframe>
		</div>
		
		<div class="upload-picture">
			<div onClick="hideUploadPicture();" style="position:absolute;cursor:pointer;right:5px;top:5px;font-size:16px;font-weight:bolder;">X</div>
			<h1>Upload a Profile Pic</h1>
			<h2>Drag a image into the box or click to add</h2>
			<input type="file" name="profilePicture" id="upload-picture-file">
			<div class="picture">
				<img class="photo" width="250" height="250" alt="Profile Picture" title="Profile Picture"/>
				<div class="upload-progress">
					<label></label>
					<img src="<?=base_url()?>img/loading.gif" />
				</div>
			<img class="mask" src="img/picture-mask.png" width="250" height="250" />
			</div>
			<div class="foot" onClick="cropAndSavePicture();" >
				Save Photo
			</div>
		</div>
			<div class="login-create">
				<div class="flat-form">
					<div id="sign-up" class="form-action hide">
						<div class="sign-up-btn" onclick="$('#sign-up').hide();$('#login').show();">
						LOGIN
						</div>
						<h1 style="margin-bottom:0;">
						<img src="<?=base_url()?>img/logo.login.png"/>
						</h1>
						<h2 style="padding-top:15px;">
						<img src="<?=base_url()?>img/svg/spin-logo.svg"/>
						</h2>
						<p class="msg">
						</p>
						<form class="bottom-form" action="" onSubmit="signUp($(this).serializeArray()[0].value);return false;">
							<ul>
								<li>
									<input type="text" name="Username" placeholder="EMAIL ADDRESS" />
								</li>
								<li>
									<input type="submit" value="SIGN UP" class="button" />
								</li>
							</ul>
						</form>
					</div>
					<div id="login" class="form-action hide">
						<div class="sign-up-btn" onclick="$('#login').hide();$('#sign-up').show();">
						SIGN UP
						</div>
						<h1>
						<img src="<?=base_url()?>img/logo.login.png"/>
						</h1>
						
						<p class="msg">
						</p>
						<form class="bottom-form" action="" onSubmit="loadUser($(this).serializeArray()[0].value,$(this).serializeArray()[1].value);return false;">
							<ul>
								<li>
									<input type="text" name="Email" placeholder="Username (Email)" />
								</li>
								<li>
									<input type="password" name="Password" placeholder="Password (Case Sensitive)" />
								</li>
								<li>
									<input type="submit" value="LOGIN" class="button" />
								</li>
								<li>
									<a class="forgot-btn" href="#" onClick="$('#login').hide();$('#forgot').show();return false;return false;">Forgot Password</a>
								</li>
							</ul>
						</form>
					</div>
					<div id="forgot" class="form-action hide">
						<div class="sign-up-btn" onclick="$('#forgot').hide();$('#login').show();">
						LOGIN
						</div>
						<h1>
						<img src="<?=base_url()?>img/logo.login.png"/>
						</h1>
						
						<p class="msg">
						</p>
						<form class="bottom-form" action="" onSubmit="users.sendResetRequest($(this).serializeArray()[0].value);return false;">
							<ul>
								<li>
									<input type="text" name="Email" placeholder="Username (Email)" />
								</li>
								<li>
									<input type="submit" value="RESET PASSWORD" class="button" />
								</li>
								
							</ul>
						</form>
					</div>
					<div id="reset" class="form-action hide">
						<h1>
						<img src="<?=base_url()?>img/logo.login.png"/>
						</h1>
						
						<p class="msg">
						</p>
						<form class="bottom-form" action="" onSubmit="users.resetPassword($(this).serializeArray()[0].value,$(this).serializeArray()[1].value);return false;">
							<ul>
								<li>
									<input type="text" name="Email" value="<?=$Email?>" disabled="disabled" />
								</li>
								<li>
									<input type="password" name="Password" placeholder="New Password*" />
								</li>
								<li>
									<input type="password" name="Repeat" placeholder="Repeat New Password*" />
								</li>
								<li>
									<input type="submit" value="RESET PASSWORD" class="button" />
								</li>
								
							</ul>
						</form>
					</div>
					<!--/#login.form-action-->
					<? if($createAccount): ?>
					<div id="register" class="form-action hide">
						<h2 style="padding-top:15px;">
						<img src="<?=base_url()?>img/svg/spin-logo.svg"/>
						</h2>
						<h3>
						Thanks for joining us
						</h3>
						<p class="msg">
						(*) Required
						</p>
						<form class="bottom-form" onSubmit="createUser($(this).serializeArray());return false;" >
							<input type="hidden" name="Email" value="<?=$Email?>"  />
							<ul>
								<li>
									<input type="text"  value="<?=$Email?>" placeholder="Username (Email)" disabled />
								</li>
								<li>
									<input type="password" name="Password" placeholder="Password*" />
								</li>
								<li>
									<input type="password" name="Repeat" placeholder="Repeat Password*" />
								</li>
								
								<li>
									<input type="text" name="Firstname" placeholder="First Name*" />
								</li><li>
									<input type="text" name="Lastname" placeholder="Last Name*" />
								</li>
								<li>
									<input type="submit" value="SIGN UP" class="button red-button" />
								</li>
							</ul>
						</form>
					</div>
					<? endif; ?>
					<!--/#register.form-action-->
				</div>
			</div>
        <div class="content-container" >
                        <div class="search-friends">
                            <h1>Find Friends</h1>
                            <form onSubmit="findFriends($(this).serializeArray()[0].value);return false;"  action="#">
                            <input type="text"  style="" name="keywords" />
                            <div class="search-btn" style='' onclick="$(this).parent().submit();"></div>
                            </form>
                            <div class="friends" >
                            
                            </div>
                        </div>
			<div class="view-container" >
			
			<!--<div class="clear"></div>-->
			</div>
			
        </div>
		<div class="goto-current-track" onClick="stream.scrollToTrack();">
			<img src="<?=base_url()?>img/scrollup-btn.png" />
		</div>
	</div>
	
	
	<script type="text/javascript" src="<?=base_url()?>js/main.js"></script>
	<script>
		var createAccount = <?=$createAccount?"'".$Email."'":'false'?>;
	</script>
