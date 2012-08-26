<?PHP
/*
 * openAPI
 * api/user/logout
 * created on 12.08.26 by LEETAEHO(tae1560@gmail.com)
 *
 * description : 서버의 token정보를 삭제하여 사용자 Session을 제거한다.
 * HTTP Method : POST
 *
 * Arguments
 * 	id
 * 		Required
 * 		Description : 회원의 ID
 * 		Example Values : test
 *
 * 	token
 * 		Required
 * 		Description : 회원 인증을 통해 서버로부터 받은 token
 * 		Example Values : e7ee5db94ed30dbf5da2d0ed4f93441a
 *
 * Result Values
 * 	result
 * 		Description : 결과에 대한 메시지
 * 		Example Values : Success, Failed to authenticate
 *
 * 	result_code
 * 		Description : 결과에 대한 숫자 코드
 * 		Example Values : 200
 *
 */

// load configurations and librarys
include_once "../../configure.php";
include_once "../../output.php";
include_once "../token_manager/token_manager.php";
include_once $configure['dbconn_path'];

// args : reference of user's id and token
function getArguments(&$id, &$token) {
	$id = $_POST['id'];
	$token = $_POST['token'];

	// DEBUG : using get for test
	//$id = $_GET['id'];
	//$token = $_GET['token'];
}

// args : user's id, token
// return : true if arguments are valid
function validArguments($id, $token) {
	if ($id == null || $token == null)
		return false;
	else
		return true;
}

// args : user's id
// return : true if remove is success
function removeSession($id) {
	return removeToken($id);
}

// args : reference of variable for return with json
function process(&$returnValue) {
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
	$result = removeSession($id);
	
	if ($result == true) {
		$returnValue['result'] = $configure['results']['success']['message'];
		$returnValue['result_code'] = $configure['results']['success']['code'];
	} else {
		$returnValue['result'] = $configure['results']['internal_error']['message'];
		$returnValue['result_code'] = $configure['results']['internal_error']['code'];
	}
}

function main() {
	process($returnValue);

	// output return value
	output_result($returnValue);
}

// start main
main();
?>