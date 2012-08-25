<?PHP
/*
 * openAPI
 * api/user/get_information
 * created on 12.08.25 by LEETAEHO(tae1560@gmail.com)
 *
 * description : id를 기반으로 회원 정보를 얻어온다. token이 같이 주어질 경우 더 많은 정보를 얻어온다.
 * HTTP Method : POST
 *
 * Arguments
 * 	id
 * 		Required
 * 		Description : 회원의 ID
 * 		Example Values : test
 *
 * 	token
 * 		Optional
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
 * 	user
 * 		Description : user 정보
 * 		Example Values : {"id":"test","space":10240}
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
	$id = $_GET['id'];
	$hashedPassword = $_GET['token'];
}

// args : user's id
// return : true if arguments are valid
function validArguments($id) {
	if ($id == null)
		return false;
	else
		return true;
}

// description : get userinformation
// args : user's id and token
// return : userinformation array
function getUserInformation($id, $token) {
	global $configure;
	global $dbconn;

	$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br>");
	$select = mysql_select_db($configure['user_database_name']);
	if (!$select) {
		echo "데이타베이스 선택시 오류가 발생하였습니다.";
		exit ;
	}

	// get user information from database
	$query = "SELECT * FROM " . $configure['user_information_table_name'] . " WHERE id='$id';";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}

	$rows = mysql_num_rows($result);
	
	if ($rows != 1) {
		return null;
	}
	
	$row = mysql_fetch_array($result);

	if ($token == searchToken($id) && validToken($token)) {
		// authentication mode

	}

	$user['id'] = $row['id'];
	$user['space'] = $row['space'];

	mysql_close($link);

	return $user;
}

// args : reference of variable for return with json
function process(&$returnValue) {
	global $configure;

	// get arguments
	getArguments($id, $token);

	// invalid argument block
	if (validArguments($id) == false) {
		$returnValue['result'] = $configure['results']['invalid_argument']['message'];
		$returnValue['result_code'] = $configure['results']['invalid_argument']['code'];
		return;
	}

	// get user information
	$returnValue['user'] = getUserInformation($id, $token);
	$returnValue['result'] = $configure['results']['success']['message'];
	$returnValue['result_code'] = $configure['results']['success']['code'];
}

function main() {
	process($returnValue);

	// output return value
	output_result($returnValue);
}

// start main
main();
?>