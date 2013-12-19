 

/*
var profile;
var tracks = [];
*/
$('body').scrollTo(0,500);





function initMain(){

	//Initialize the players for Audio class from audio.js
	soundCloudInit();
	
	//Initialize stream
	stream.init();
	/*
	* This click listener allows the user to click on the song progress bar to
	* change the position in the currently playing track.
	*/
	$(document).on('click', '.song-progress-wrapper', function(e)
	{
		var pos = e.pageX - $(this).offset().left;
		player.setPosition(pos);
		
	});
	
	$('.nav-container').show()
			.css({ 'top':-65 })
			.animate({'top': 0}, 800);
	
	//Some responsive sizes:
	fontResize();
	
	
	//Endless Scroll:
	$(window).scroll(function() {
		clearTimeout($.data(this, 'scrollTimer'));
		$.data(this, 'scrollTimer', setTimeout(function() {
			
			if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
				
				stream.loadMore();
			}
		}, 250));
	});
	
	
	

	
		
}

function flipSongCard(element){
	/*
	//It would be cool to implement this plugin to show up the respin form
	$(element).flippy({
				duration: "500",
				onMidway: function(){
					//toggle songcard
					$(element+" .card").toggle();
					//toggle respin
					$(element+" .respin").toggle();
				}
	});
	*/

	//hide other respin cards

	
}
function showRespinCard(element){
	
	hideRespinCard();
	stream.respinProfilePicture(users.ProfilePicture);
	users.clearToRespin();
	users.togglePublicRespin();
	users.songToRespin=element.replace("#song","");
	
	$(element+" .respin h1").html(users.getName());
	
	var friends = users.recentActivityFriends(element);
	console.log(friends);
	replaceRespinUsers(element,friends);
	
	$(element+" .respin").toggle();
	$(element+":not(.intro-card)").height($(element+" .respin").height());
	$(element+":not(.intro-card)").css('overflow','hidden');
	stream.layoutAll();
}
function hideRespinCard(){
	$(".song-card .respin").hide();
	$(".song-card:not(.intro-card)").css('height','auto');
	$(".song-card:not(.intro-card)").css('overflow','visible');
	
	//$(".song-card .invite").hide();
	$(".song-card input").val("");
	//Check why title is not being desselected
	$(".song-card h1").addClass("selected");
	//stream.layoutAll();
}
function searchUsers(element,keywords){
	
	
	if(validateEmail(keywords)){
		
		
		sendInvite(element.substr(5),keywords);
		hideRespinCard();
		return;
	}
	var friends = users.searchUsers(keywords);
	
	
	replaceRespinUsers(element,friends);
	//$(element+" .invite").show();
}
function sendInvite(IdSong,email){
	
	var msg = users.invite(email,IdSong);
	
        $("#song"+IdSong+" .email").val(msg);
	return false;
}
function replaceRespinUsers(element,users){
	
	$(element+" .friends").empty();
	if(users.length == 0){
	
		$(element+" .friends").append("<li class='row' style='cursor:auto;height:50px;padding-left:5px;width:245px;'>Your friend isn't on the site yet, send them an invite</li>");
		return;
	}
	for(var i=0 ; i<users.length ; i++){
		$(element+" .friends").append("<li class='row' style='background-image:url(\""+base_url+users[i].ProfilePicture+"\")' onClick='users.toggleUserToRespin("+users[i].IdUser+");$(this).toggleClass(\"selected\")'>"+users[i].Firstname+" "+users[i].Lastname+"</li>");
	}
}
function respinSong(element){
	
	
	if(validateEmail( $(element+' .respin .bottom .keywords').val() ) ){
		
		
		sendInvite(element.substr(5),$(element+' .respin .bottom .keywords').val());
		
	}
	users.respin();
	hideRespinCard();
}

jQuery.fn.extend($,{
secureAjax :  function(options) {
        
            //Do stuff
			var token = users.getToken();
			var email = users.getEmail();
			//console.log(token);
			$.ajaxSetup({
				type:"POST",
				data:{"Token":token,"Email":email}
			});
			
			
			return $.ajax(options);
		
    }
});

function lenghtOfObject(o){
	var count = 0, k=null;
    for (k in o) {
        if (o.hasOwnProperty(k)) count++;
    }
    return count;
}

function searchYouTube(){

	//$('.content-container').css({'margin-top':'70px'});
	//$('.profile-nav-container').hide();
	
	var profile = search.init($("#search-text").val());
	player.newPlaylistFlag = true;
	stream.loadProfile(profile);
	$("#search-text").val("")
	
}

function createUser(form){
	
	
	
	var newUser ={};
	newUser.Password = form[1].value;
	newUser.Repeat = form[2].value;
	newUser.Email = form[0].value;
	newUser.Firstname = form[3].value;
	newUser.Lastname = form[4].value;
	
	
	
	var msg = "";
	var noProblem=true;
	

	if(newUser.Password!=newUser.Repeat || newUser.Password==''){
		msg+='Password error. Please Try Again.<br>';
		noProblem=false;
	}
	if(newUser.Firstname=='' || newUser.Lastname==''){
		msg+='All the fields are required. Please Try Again.<br>';
		noProblem=false;
	}
	
	
	
	if(!noProblem){
		$('#register .msg').html(msg);
		return false;
	}
	
	$.ajax({
		type:'POST',
		data:newUser,
		url: base_url+"login/create",
		dataType:'json',
		async:false,
		context: document.body
	}).done(function(data)
	{
		if(typeof data.error  != 'undefined'){
			msg+=data.error+'<br>';
			return;
		}else{
			newUser.Username=data.Username;
			newUser.Password=data.Password;
			newUser.Token=data.Token;
			
			
			localStorage.setItem('email',newUser.Email);
			localStorage.setItem('pass',newUser.Password);
			
			window.location.href = base_url;
		}
		
	});
	
	$('#register .msg').html(msg);
	
	
}

function showTOU(){
	$(".loading").show();
	$(".tou").show();
	
	$('body').css({'overflow':'hidden'});
	$(".loading").css({'cursor':'pointer'});
	$('.loading').click(function(){
		$(".tou").hide();
		$(".loading").css({'cursor':'auto'});
		$('body').css({'overflow':'visible'});
		$(".loading").hide();
		$('.loading').off('click');
	});
}
function showPrivacyPolicy(){
	$(".loading").show();
	$(".privacy-policy").show();
	$('body').css({'overflow':'hidden'});
	$(".loading").css({'cursor':'pointer'});
	$('.loading').click(function(){
		$(".privacy-policy").hide();
		$(".loading").css({'cursor':'auto'});
		$('body').css({'overflow':'visible'});
		$(".loading").hide();
		$('.loading').off('click');
	});
}

var uploadedPicture;
function uploadPicture(){
	$(".loading").show();
	$(".upload-picture").show();
	$('body').css({'overflow':'hidden'});
	$(".loading").css({'cursor':'pointer'});
	$('.loading').click(function(){
		hideUploadPicture();
		
		
	});
	
	
	$(function () {
		$('#upload-picture-file').fileupload({
			dataType: 'json',
			url:base_url+'users/picture',
			formData:{IdUser:users.IdUser,Token:users.Token},
			done: function (e, data) {
				var response = jQuery.parseJSON( data.jqXHR.responseText );
				
				console.log(response);
				$('.upload-picture .picture .photo').attr('src',base_url+response.pictureUrl+'#e' + Math.random());
				
				uploadedPicture = $('.upload-picture .picture .photo').imgAreaSelect({aspectRatio:"1:1",instance:true, x1: 30, y1: 30, x2: 220, y2: 220,minWidth:100,minHeight:100 });
				
				$(".upload-picture .picture .upload-progress").hide();
			},
			send:function(){
				$(".upload-picture .picture .upload-progress").show();
			},
			progress:function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				$(".upload-picture .picture .upload-progress label").html(progress+"%");
			}
		});
	});
	
}

function hideUploadPicture() {
	$(".upload-picture").hide();
	$('#upload-picture-file').fileupload('destroy');
	$('body').css({'overflow':'visible'});
	$(".loading").css({'cursor':'auto'});
	$(".loading").hide();
	$('.loading').off('click');
}
//finish this function!!!
function cropAndSavePicture(){
	var crop = uploadedPicture.getSelection();
	$.secureAjax({
				url: base_url+"users/cropAndSavePicture",
				data:{IdUser:users.IdUser,x:crop.x1,y:crop.y1,relativeWidth:$('.upload-picture .picture .photo').width(),relativeHeight:$('.upload-picture .picture .photo').height(),width:crop.width,height:crop.height},
				dataType:'json',
				context: document.body,
				success:function(data){
					console.log(data);
					//$('.upload-picture .picture .photo').attr('src',base_url+data.pictureUrl+'#e' + Math.random());
					stream.userProfilePicture(data.pictureUrl+'#e' + Math.random());
                                        
                                        uploadedPicture.cancelSelection();
                                        $(".loading").hide();
										$(".loading").css({'cursor':'auto'});
										$('body').css({'overflow':'visible'});
                                        $(".upload-picture").hide();
                                        users.ProfilePicture = data.pictureUrl+'#e' + Math.random();
				}
				
	});
}


function logout(){
    localStorage.setItem('email',null);
    localStorage.setItem('pass',null); 
    window.location.href = base_url;
}
function loadUser(email,pass,callback){
	
	
	if(typeof email == 'undefined' || typeof pass == 'undefined'){
		
		var email = localStorage.getItem('email');
		var pass = localStorage.getItem('pass'); 
		
		
	}
	
	if(typeof callback == "undefined"){
		var callback = function(){
			
			//mmm.. this load main..
			stream.loadMain();
			
			
		}
	}
	
	if(users.login(email,pass)){
		
		//Should be always called
		$('.login-create').hide();
		//Should be always called
		initMain();
		localStorage.setItem('email',email);
		localStorage.setItem('pass',pass);
			
			
		callback();
		
		
	}else{
		$('#login').show();
		$('#sign-up').hide();
		$('#register').hide();
		$('#login .msg').html('Username or Password Incorrect. Please Try Again');
	}
}
function signUp(email){
	
	$("#sign-up .msg").html("Submiting email...");
	
	$.ajax({
		type:'POST',
		data:{"Email":email},
		url: base_url+"login/signup",
		dataType:'json',
		async:false,
		context: document.body
	}).done(function(data)
	{
		$("#sign-up .msg").html(data.msg);
		
	});
	
	

}
function loadProfile(id,type){
	if(type=='blogs'){
		var profile = blogs.getBlog(id);
		
	}else if(type=='users'){
		var profile = friends.getUser(id);
	}else{
		return false;
	}
	
	player.newPlaylistFlag = true;
	stream.loadProfile(profile);
}


function layoutColumns(){
	var colW = 262;
	// check if columns has changed
    //var currentColumns = Math.floor( ( $('.view-container').width() ) / colW );
    var currentColumns = Math.floor( ( $(window).width() ) / colW );
	
	return currentColumns;
}

function playPause(){
	
	
	player.playFromPlayer();

}
function playFromCard(id){
	
	
	player.newPlaylistFlag = false;
	player.playById(id);
}
function skipFwd(){
	player.skipFwd();

}

function skipBack(){
	player.skipBack();
}


function fontResize(){
		
		var windowWidth = $(window).width();
		
		
		var standardWidth=1437;
		var standarFont=10;
		
		var fontSize = Math.floor((windowWidth*standarFont)/standardWidth);
		
		//$('body').css({'font-size':(windowWidth/oldWindowWidth)*parseFloat($('body').css('font-size'))+'px'});
		$('body').css({'font-size':fontSize+'px'});
		
		
		centerText($('.following-btn-txt'), $('.following-btn'));
		centerText($('.blogs-btn-txt'), $('.blogs-btn'));
		centerText($('.followers-btn-txt'), $('.blogs-btn'));
		var navBarHeight = $('.nav-bar').css('padding-bottom');
		
		
		$(".nav-container .profile-nav").height(2 * parseInt(navBarHeight));
		
		//$('.nav-bar').css('width', '100%');
		$('.dropdown-ctrls').css('top', parseInt(navBarHeight));
			
			
			
			
		var profileNav = $('.profile-nav-container').is(":visible")?$('.profile-nav-container').height():0;

		var newHeight = parseInt(navBarHeight) + profileNav;
		
		$('.content-container').css({'margin-top':newHeight+'px'});
		
		
			
		function centerText(textDiv, containerDiv) {

			var fTextWidth = textDiv.css('width');
			var fTextHeight = textDiv.css('height');
			
			textDiv.css('margin-left', '-'+(parseFloat(fTextWidth)/2).toString()+'px');
			textDiv.css('margin-top', '-'+(parseFloat(fTextHeight)/2).toString()+'px');
			
		}	
		
		
}

function showFindFriends(){
        stream.empty();
        stream.hideProfileNavBar();
        $(".search-friends").show();
}

function hideFindFriends(){
        $(".search-friends .friends").empty();
        $(".search-friends").hide();
}

function findFriends(keywords){
    
    
    stream.setProfile({});
    $(".loading").show();
    var foundUsers = users.searchUsers(keywords,20);
    
    $(".search-friends .friends").empty();
    
    if(typeof findFriends.template == "undefinded"){
        findFriends.template=false;
    }
    
    if(!findFriends.template){
        console.log("Lets load the template");
        $.secureAjax({
            url: base_url+"main/card/user/",
            async:false,
            context: document.body,
            
        }).done(function(data){
            console.log("template loaded..");
            console.log(findFriends.template);
            findFriends.template = data;
        });
    }
    
    for(var i=0 ; i<foundUsers.length ; i++ ){
        var output = Mustache.render(findFriends.template,foundUsers[i]);
        console.log("Lets see the results");
        console.log(findFriends.template);
        $(".search-friends .friends").append(output);
    }
    $(".loading").hide();
    
}

function encodeData(s){
    return encodeURIComponent(s).replace(/\-/g, "%2D").replace(/\_/g, "%5F").replace(/\./g, "%2E").replace(/\!/g, "%21").replace(/\~/g, "%7E").replace(/\*/g, "%2A").replace(/\'/g, "%27").replace(/\(/g, "%28").replace(/\)/g, "%29");
}

function validateEmail(email) {
	
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

