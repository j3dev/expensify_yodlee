<?php
/////
// Routing for AJAX calls
/////
include_once '../model.php';

if (isset($_POST['logout'])){ 

	session_destroy(); 

	return;
}

$Route = new ExpyCntrl();

$enabled = array('login', 'get_trans', 'create_trans');  //Allowed function calls

isset($_POST['params']) ? $params = $_POST['params'] : $params = null;

if(isset($_POST['action'])){
	 
	 $action = $_POST['action'] ;

	 in_array($action, $enabled) ? $Route->$action($params) : null; // Validate allowed function
}
else{
	$action = '';
}


class ExpyCntrl {

	public function __construct(){
		$this->EI = new ExpyInterface(); //model
	}


	public function status_msgs($status){ // Expy Status Codes 

		 $msgs = array(  
			'200' => 'OK',
			'401' => 'Permission Denied',
			'400' =>'Unrecognized command',
			'402' =>'Missing argument',
			'404' => 'Not Found',
			'407' =>'Malformed token',
			'408' =>'Token expired',
			'411' =>'Insufficient privileges',
			'500' =>'Aborted',
			'501' =>'DB transaction error',
			'502' =>'Query error',
			'503' =>'Query response error',
			'504' =>'Unrecognized object state',
			);

		 return $msgs[$status];
	}

	public function restrict(){

		// Add functionality to protect authorized only partials / pages and wrap them
		// Not used for this project as content is not sensitive and api calls are protected	
	} 

	public function login($creds){  // Set Expensify api token
		
		$params = array(
			'command'	=> 'Authenticate',
			'partnerUserID' => $creds['email'],
			'partnerUserSecret' =>$creds['password']
		);

		$response = $this->EI->expy_request($params);

	 	$res = json_decode($response);
	 	
	 	if($res->httpCode == '200' ){

	 		$auth = $res->authToken;

	 		setcookie('auth' , substr($auth, 0, 4),time()+3600 , '/' ); 

	 		session_status() == PHP_SESSION_NONE ? session_start() : null;
	 		
	 		$_SESSION['authToken'] = $auth;
	 		$_SESSION['email'] = $res->email;
	 		$_SESSION['accountID'] = $res->accountID;

	 		$results['status'] = $res->httpCode;
	 		$results['msg'] = $_SESSION['authToken'];

	 	} 
	 	else{
	 		$results['status'] = $res->httpCode;
	 		
	 	}

	 	$results['msg'] = $this->status_msgs($res->httpCode);
	 	echo json_encode($results);
		//echo json_encode($results);


	}


	public function get_trans(){  //Download Transactions
		
		session_status() == PHP_SESSION_NONE ? session_start() : null;
		
		$params = array( 
			'command'	=> 'Get',
			'authToken' => $_SESSION['authToken'],
			'returnValueList' => 'transactionList' ,
			);

		$response = $this->EI->expy_request($params);
	 	
	 	echo $response;
	 	
	}


	public function create_trans($info, $local = null){  //Create Transactions
		
		session_status() == PHP_SESSION_NONE ? session_start() : null;
		
		$params = array( 
			'command'	=> 'CreateTransaction',
			'authToken' => $_SESSION['authToken'],
			'created' 	=> date('Y-m-d', strtotime($info['date'])),
			'amount'	=> $info['amount'],
			'merchant'	=> $info['merchant']
			);


		$response = $this->EI->expy_request($params);
	 	

	 	if(isset($local)){
	 		return $response ;
	 	}
	 	else{
	 		 echo $response;
	 	}

	 	

	 	
	}




}

?>
