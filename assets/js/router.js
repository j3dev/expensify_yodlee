function router() {  // single page app routing  also called onhashchange to allow back button 
    if (window.location.hash.length === 0 ){ return; } ;   // if home do nothing

	var route = window.location.hash.substring(1);

	$('#msgArea').html('');

	menu();  // refreshes menu relevant when coming from login
	 
	Routes[route]();
} 

Routes = { 

	view_transactions : function(){
		
		if(auth_lvl == 0){ 
			this.login_required("view_transactions")
		}
		else{
			load_view("view_transactions");
			get_transactions();	
		}
		
	},

	create_transaction : function(){

		auth_lvl == 0 ? this.login_required("create_transaction"): load_view("create_transaction") ;
	
	},

	login : function(){
		load_view("login");
	},

	logout : function(){
		
		logout();

		load_view("login");

		menu();
	},

	help : function(){
		load_view("help");
	},

	login_required : function(cb){
		
		go_to = cb;  // sets call back to return to originally requested page after login

		load_view("login");

		msg("Login Required to Access This Page", "error", 1); 	
		
	}


}

function load_view(route) { //puls html from partials

	var hash = window.location.hash.substr(1) ;
	
	hash != route ? window.location.hash= route : null ; // if coming via direct input or back button don't update hash
	
	$('#content').load(view_path + route + '.html');
	
}