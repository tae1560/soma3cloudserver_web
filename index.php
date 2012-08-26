<?PHP

// return : true if login status
function isLogin() {
	if ($_SESSION['token'] == null) {
		return false;
	} 
	
	
}

// args : reference of variable for return with json
function process() {
	global $configure;

	// invalid argument block
	if (validArguments($id) == false) {
		$returnValue['result'] = $configure['results']['invalid_argument']['message'];
		$returnValue['result_code'] = $configure['results']['invalid_argument']['code'];
		return;
	}
	
	// token validation block
	if (validToken($token) == false) {
		$returnValue['result'] = $configure['results']['failed_authentication']['message'];
		$returnValue['result_code'] = $configure['results']['failed_authentication']['code'];
		return;
	}
	
	// remove session
	$result = removeUser($id);
	
	if ($result == true) {
		$returnValue['result'] = $configure['results']['success']['message'];
		$returnValue['result_code'] = $configure['results']['success']['code'];
	} else {
		$returnValue['result'] = $configure['results']['internal_error']['message'];
		$returnValue['result_code'] = $configure['results']['internal_error']['code'];
	}
}

function main() {
	//process($returnValue);

	// output return value
	//output_result($returnValue);
}

// start main
main();
?>