// DOM and Data retrieval / manipulation  

//////////////
//// DOM da DOM DOM
/////////////

//// !! Would be updated to use some kind of js templating such as handlebars 
///     to avoid messy .html() .append() calls

var assets = 'assets';
var view_path = assets + '/partials/';
var go_to = 'view_transactions'; // used for setting callback destination reset to default after function call


// No real security performed simply used for UI changes based on permissions
// auth required sections secured server side
var auth_lvl;

function auth_check(){
	// Would need to change to val check for multiple permission levels
	
	$.cookie('auth') === undefined ? auth_lvl = 0 : auth_lvl = 1;
}



function msg(msg, type, temp){  // Simple alert function type[success, error, info] temp bool determines whether to fadeOut

	$('#msgArea').html('<div id="newAlert" class="c4 centered alert ' + type +'">' + msg + '</div>');
	temp == 1 ? $('#newAlert').delay(2000).fadeOut() : null;
}

function menu() { // load top menu based on permissions

	var menu_html = "";
	var first = "first";

	auth_check(); // re-evalute auth cookie
	
	var items = [
		{link : "#login" , name:"Login", perms: 0},
		{link: "#view_transactions", name:"View Transactions", perms: 1},
		{link: "#create_transaction", name:"Create Transaction", perms: 1},
		{link : "#help" , name:"Help", perms: 0},
		{link : "#logout" , name:"Logout", perms: 1}
	]

	$.each(items, function(index, val){
		index != 0 ? first = "" : null;
		if(auth_lvl >= val.perms && index >= auth_lvl){

			menu_html += '<li class="tmItems '+ first + '"> <a href="' + val.link + '" >' + val.name + '</a></li>';
		}
	});

	$('#topMenu').html(menu_html);
}


/// View Transactions DOM
var list;
function display_transactions(start) {  // Create paginated transaction table

	var data = list.transactionList;
	var table = $("#transctionTable");
	var even;
	

	table.html(
		'<tr>'+
			'<th class="text-center">Date</th><th>Merchant</th><th>Amount</th><th>Status</th><th>ID</th>' +
		'</tr>'
		);

	$.each(data ,function(index, val){
		if(index >= start){
			index % 2 == 0 ? even = "even" : even = "";
			
			
			var amount = val.amount / 100;
			table.append(
						'<tr '+ even +'>' +
							'<td class="text-center">'+ val.created + '</td><td class="text-center">'+ val.merchant+'</td><td class="text-center">$ ' + 
							 amount.toString().replace(/(\d)(?=(\d{3})+\.)/g, "$1,") + ' ' + val.currency + '</td><td class="text-center">' + val.receiptState + '</td>'+  /// Replace statement adds commas for monetary
							 '<td class="text-center">' + val.transactionID + '</td>'+
						'</tr>'
					);
		}

		if (index == start + 19) { return false; }
	});

	table.append('</table>');
	

	// table pagination    !! Control errors need to be disabled if at beg or end
	$('#pagi').html('<ul class="pagin text-center">');
	
 	var new_start = 0;
 	var page_cnt = Math.round(data.length / 20) ;
 	var back = start - 20;
 	var fwd = start + 20;
 	page = start / 20 + 1;


 	$('#pagi').html(
 			'<a href="javascript:display_transactions(' + back + ')" > << </a>page '+  page +' of ' + page_cnt + ' <a href="javascript:display_transactions(' + fwd + ')" > >> </a>'
 		);

}

function last_transaction(list) {  // shows list of just imported transactions
	
	var table = $("#ltTable");
	

	table.html(
		'<tr>'+
			'<th class="text-center">Date</th><th>Merchant</th><th>Amount</th><th>Status</th><th>ID</th><th>View All</th>' +
		'</tr>'
		);
	$.each(list, function(index, val){
		console.log(val);
		$.each(val, function(index, data){

			var amount = data.amount / 100;

			table.append(
				'<tr>' +
					'<td class="text-center">'+ data.created + '</td><td class="text-center">'+ data.merchant+'</td><td class="text-center">$ ' + 
					 amount.toString().replace(/(\d)(?=(\d{3})+\.)/g, "$1,") + ' ' + data.currency + '</td><td class="text-center">' + data.receiptState + '</td>'+  /// Replace statement adds commas for monetary
					 '<td class="text-center">' + data.transactionID + '</td>'+ '<td class="text-center"> <a href="#view_transactions"> Go </a>' +
				'</tr>'
				);
		});
	});
	$('#lastTrans').fadeIn();

}




//////////
/// Data
/////////
var api = '/controllers/expy';
var yodlee = '/controllers/yodlee';

function get_transactions() {
	
	var params =  {
		action: 'get_trans'	
		}
	
		
	$('#midSection').append('<div class="c2 centered text-center loader"> <img src="'+assets+'/img/loader.gif"/></div');

	$.post(api ,params , function(data){

		$('.loader').fadeOut();
		$('#vtTable table').fadeIn();

		if(data.jsonCode == 200){
			list = data;
			display_transactions(0);
		}
		else{
		  	msg(data.message, "error", 1);
		  }

	},'json');
}

function create_transaction(info) {

	var params = {
			action: 'create_trans',	
			params: info,
		}
		
	$.post(api,params , function(data){
		 
		  $('#ctForm').find('input[type=text]').val(''); //reset form
		  
		  if(data.jsonCode == 200){

		  	last_transaction([data.transactionList]);
		  	msg('Saved! Transaction ID: ' + data.transactionList[0].transactionID , "success", 0);
		  }
		  else{
		  	console.log(data);
		  	msg(data.message, "error", 1);
		  }

	},'json')
			.fail( function(){
				msg('Server Down', 'error', 1)
			});

}


function authenticate(auth) {
	var params = {
			action: 'login',
			params : auth
		}
		
 	$.post(api,params, function(status){
 		
		if (status.status == 200){


				menu();
		    msg('Successfully Logged In', 'success', 1);
			
			setTimeout(function(){
				window.location.hash = go_to;
				go_to = 'view_transactions';
				}, 2000
			);
			
		}
		else{
			msg(status.msg, "error", 1);
		}

	},'json')
			.fail( function(){
				msg('Server Down', 'error', 1)
			});

	
}

function logout() {


	var params = { logout: 1 };

 	$.post(api,params, function(){

 		msg("Successfully Signed Out" , "success", 1);
 	});

 	$.removeCookie("auth");
}


function yodlee_login(auth){
	var params = {
			action: 'yl_login',
			params : auth
		}
		
 	$.post(yodlee,params, function(status){
 		
 		$('#yodleeImport').hide();

 		$('#yodleeResults').fadeIn();
 		console.log(status);
 		$('#yodleeResults').html( 
 				'<h4>'+status.count + ' Transactions Found <b><a id="import_transactions" href="#create_transaction">Import</a></b> </h4>'
 			);
 	}, 'json');
}

function yodlee_import(){

	var params = {
			action: 'import'
		}

	$.post(yodlee,params, function(trans){

		results = 'Success, Saved Transaction ID\'s';
		
		$.each(trans, function(index, data){
			data = $.parseJSON(data);
			
			results += '<br> ' + data.transactionID;
		});

		
		$('#create').hide();

		msg(results, "success", 0);
	}, 'json');

}
