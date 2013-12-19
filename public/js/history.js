

var history ={
		
	ownURI:base_url+'profiles/users/own',
	blogURI:base_url+'profiles/blog/',
	userURI:base_url+'profiles/users/',
	baseURI:base_url,
    
	secureStateChange:true,
	
	init: function(){
		//Move state change and operations to a class instead of a function
		this.loadState();
		//History back/forward browser button
		
		var _this = this;
		
		$(window).on("statechange",function(){
			if(!_this.secureStateChange){
				_this.loadState();
			}		
		});
	},
	setState: function(URI){
                //Please do not hold the following line here... we should implement this other way:
                hideFindFriends();
                
        var email = localStorage.getItem('email');
		var pass = localStorage.getItem('pass');   
		
		
		
		this.secureStateChange=true;
		History.pushState(null,null,URI);
		this.secureStateChange=false;
		
		
		//localStorage.setItem('user',email);
		//localStorage.setItem('pass',pass);
	},
	setUser: function(id){
		this.setState(this.userURI+id);
	},
	setBlog: function(id){
		this.setState(this.blogURI+id);
	},
	setOwn: function(){
		this.setState(this.ownURI);
	},
	setMain: function(){
		this.setState(this.baseURI);
	},
	
	loadState: function(){
    
                var State = History.getState();
                
                var hash = State.url;
                
				
				
				
				var email = localStorage.getItem('email');
				var pass = localStorage.getItem('pass');
				
                if(hash.indexOf(this.ownURI)==0){
                    console.log("loading own profile");
					
					//load whatever
					if(typeof email !='undefined' && typeof pass!='undefined' && email!='null' && pass!='null' && email!='undefined' && pass!='undefined'){
			
						loadUser(email,pass,function(){
							stream.loadUserProfile();
						});
						return;
					}
                    
                }
               
                if(hash.indexOf(this.userURI)==0){
                    console.log("loading user profile");
                    var id = hash.substr(this.userURI.length);
                    
					
					if(typeof email !='undefined' && typeof pass!='undefined' && email!='null' && pass!='null' && email!='undefined' && pass!='undefined'){
			
						loadUser(email,pass,function(){
							loadProfile(id,'users');
						});
						return;
					}
					//load whatever with id
                    
                    
                }
				if(hash.indexOf(this.blogURI)==0){
                    console.log("loading blog profile");
					var id = hash.substr(this.blogURI.length);
                    
					//load whatever with id
					if(typeof email !='undefined' && typeof pass!='undefined' && email!='null' && pass!='null' && email!='undefined' && pass!='undefined'){
			
						loadUser(email,pass,function(){
							loadProfile(id,'blogs');
						});
						return;
					}
                    
                    
                }
				
				
				console.log("loading nothing...");
                //Default:
                if(createAccount){
					$('#register').show();
				
				}else{
					
					if(typeof email !='undefined' && typeof pass!='undefined' && email!='null' && pass!='null' && email!='undefined' && pass!='undefined'){
						
						loadUser(email,pass);
					}else{
						console.log("SESSION DESTROYED:"+email+" "+pass);
						$('#sign-up').show();
					}
				
				}
	}
	   
}