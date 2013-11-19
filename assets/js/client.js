// Client side navigation

$(document).ready(function(){

	router();

	menu();
	$(document).on('submit', function(){return false;}); // Prevent any unpredictability in js submits

	$(document).on('click', '#login', function(){
		var data = {
			email: $('#auth_email').val(),
			password : $('#password').val()
		}
		
		authenticate(data);
	});

	$(document).on('click', '#create_transaction', function(){
		amount = $('#amount').val().replace(/\D/g,'');
		var data = {
			date: $('#date').val(),
			merchant : $('#merchant').val(),
			amount: amount
		}
	
		create_transaction(data);
	});

	$(document).on('click', '#import', function(){
		$('#msgArea').html('');
		$('#manualCreate').hide();
		$('#yodleeImport').fadeIn();
	});


	$(document).on('click', '#yodlee_login', function(){
		var data = {
			login: $('#username').val(),
			password : $('#password').val()
		}
		
		yodlee_login(data);
		
	});

	$(document).on('click', '#import_transactions', function(){
		
		yodlee_import();
		
	});


});