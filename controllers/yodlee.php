<?php

include_once '../model.php';
include 'expy.php';

$Route = new YodleeCntrl();

$enabled = array('yl_login', 'import');  //Allowed function calls

isset($_POST['params']) ? $params = $_POST['params'] : $params = null;

//Routing
if(isset($_POST['action'])){
	 
	 $action = $_POST['action'] ;

	 in_array($action, $enabled) ? $Route->$action($params) : null; // Validate allowed function
}
else{
	$action = '';
}

class YodleeCntrl{
	
	public function __construct(){
		$this->YL = new YodleeInterface(); //model 
		$this->Expy = new ExpyCntrl(); 
	}

	public function yl_login($params){

		$results['cob'] = $this->YL->set_cobtoken();  // Gets app token for yodlee
		$results['auth'] = $this->YL->set_usertoken($params); // Gets user token for yodlee
		$results['count'] = $this->YL->transaction_search();  
		// ^
		// This is interesting you have to create a search object on yodlee
		// and then reference the returned id in other calls to use the results
		// would be seperate from the login process and accept user input 
		// if not being used only for demonstration purposes.

		echo json_encode($results);
	}

	public function import(){  // must have access tokens set

		$entries = $this->YL->search_results();

		

		
		foreach($entries as $entry){

			isset($entry->description->merchantName) ? $merchant = $entry->description->merchantName : $merchant = 'YL import' ;

			$info = array(
				'date' 	   => $entry->postDate,
				'amount'   => preg_replace("/[^0-9,.]/", "", $entry->amount->amount),
				'merchant' => $merchant
				);
			
			$results[] =$this->Expy->create_trans($info, 1);  // Creates expensify transaction see expy.php

		}

		echo json_encode($results);
	}

	
}