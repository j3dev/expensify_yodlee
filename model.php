<?php
// API interfacing models  

// considered doing in js but server does not seem to support CORS without too much hacking + 
// more work to do securely


class ExpyInterface{ 

	private $auth =  array(
		'partnerName' => 'PARTNERNAME',
		'partnerPassword' => 'PASSWORD',
		);

	public $api_url = 'https://applicant.expensify.com/api' ;
	

	public function expy_request($params){
		
		isset($params['partnerUserID']) ? $params = array_merge($params, $this->auth) : null;
		
		$r = new HttpRequest($this->api_url , HttpRequest::METH_POST);  /// pecl_http 

		$r->addPostFields($params);

		try {
		    return $r->send()->getBody();
		} catch (HttpException $ex) {
		    return $ex;
		}
	}
}


// Almost zero error handling... would update for real use

class YodleeInterface{

	private $auth = array(
		'cobrandLogin' 	  => 'COBLOGIN',
		'cobrandPassword' => 'COBPASSWORD'
		);



	public function api_url($method) {
		$url = 'https://rest.developer.yodlee.com/services/srest/restserver/v1.0/';

	    $method_url = array( 
				 			'cblogin'  => 'authenticate/coblogin',
					  		  'login'  => 'authenticate/login',
				'getUserTransactions'  => 'jsonsdk/TransactionSearchService/getUserTransactions',
			'executeUserSearchRequest' => 'jsonsdk/TransactionSearchService/executeUserSearchRequest'
						);


	    return $url . $method_url[$method];
	}

	public function set_cobtoken(){  // application token
		
		session_status() == PHP_SESSION_NONE ? session_start() : null;

		$response = $this->yodlee_request('cblogin', $this->auth);

		$data = json_decode($response);

		

		if(isset($data->cobrandConversationCredentials->sessionToken)){

			

			$_SESSION['cobToken'] = $data->cobrandConversationCredentials->sessionToken;

			return true;
		}

		return false;
	}

	public function set_usertoken($params){ // user token retrieval requires app token 
		
		session_status() == PHP_SESSION_NONE ? session_start() : null;

		$params['cobSessionToken'] = $_SESSION['cobToken'];

		$response = $this->yodlee_request('login', $params);

		$data = json_decode($response);
		

		if(isset($data->userContext->conversationCredentials->sessionToken)){

			setcookie('yodlee_auth' , 1,time()+3600 , '/' ); 

			

			$_SESSION['userToken'] = $data->userContext->conversationCredentials->sessionToken;
			
			return true;
		}

		return $response;
	}


	// creates a search result object on yodlee but only returns search info and first page of results
	// must use other calls referencing search id to use full search result
	public function transaction_search(){ 
	
		session_status() == PHP_SESSION_NONE ? session_start() : null;	

		$params = array(
			'cobSessionToken'  => $_SESSION['cobToken'],
			'userSessionToken' => $_SESSION['userToken'],

			'transactionSearchRequest.containerType' 			 			=> 'All',
			'transactionSearchRequest.higherFetchLimit' 		 			=> 10000,
			'transactionSearchRequest.lowerFetchLimit' 			 			=> 0,
			'transactionSearchRequest.resultRange.endNumber' 	 			=> 20,
			'transactionSearchRequest.resultRange.startNumber' 	 			=> 1,
			'transactionSearchRequest.searchClients.clientId' 	 			=> 1,
			'transactionSearchRequest.searchClients.clientName'  			=> 'DataSearchService',
			'transactionSearchRequest.searchFilter.currencyCode' 			=> '',
			'transactionSearchRequest.searchFilter.postDateRange.fromDate' 	=> '01-01-1960',  // should update to be dynamic by user input
			'transactionSearchRequest.searchFilter.postDateRange.toDate' 	=> '01-31-2015',  // ^
			'transactionSearchRequest.searchFilter.transactionSplitType.splitType' => 'A',
			'transactionSearchRequest.ignoreUserInput' 			 			=> 'True',
		
			);

		$response = $this->yodlee_request('executeUserSearchRequest', $params);
		
		$data = json_decode($response);

		$_SESSION['ysearch'] = $data->searchIdentifier->identifier;  // search identifier required to pull results in other calls


		return $data->numberOfHits;  // return number of transactions found
	}

	public function search_results(){  // Pulls search results

		session_status() == PHP_SESSION_NONE ? session_start() : null;

		$params = array(
			'cobSessionToken'  => $_SESSION['cobToken'],
			'userSessionToken' => $_SESSION['userToken'],
			'searchFetchRequest.searchIdentifier.identifier'   => $_SESSION['ysearch'],
			'searchFetchRequest.searchResultRange.startNumber' => 1,
			'searchFetchRequest.searchResultRange.endNumber'   => 50000
			);

		$response = $this->yodlee_request('getUserTransactions', $params);

		$data = json_decode($response);

		return $data->transactions;

	}

	public function yodlee_request($method, $params = null){  //  api call

		$r = new HttpRequest($this->api_url($method) , HttpRequest::METH_POST);

		$r->addPostFields($params);

		try {
		    return $r->send()->getBody();
		} catch (HttpException $ex) {
		    return $ex;
		}

	}

}





?>

