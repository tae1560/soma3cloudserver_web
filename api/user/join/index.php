<?PHP
/*
 * openAPI
 * api/user/join
 * created on 12.08.23 by LEETAEHO(tae1560@gmail.com)
 *
 * description : 회원정보를 등록하여 새로운 User를 생성한다.
 * HTTP Method : POST
 *
 * Arguments
 * 	id
 * 		Required
 * 		Description : 회원의 ID
 * 		Example Values : test
 *
 * 	hashedPassword
 * 		Required
 * 		Description : MD5로 hashing된 회원의 Password
 * 		Example Values : 098f6bcd4621d373cade4e832627b4f6
 *
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

// args : reference of user's id and hashedpassword
function getArguments(&$id, &$hashedPassword) {
	$id = $_POST['id'];
	$hashedPassword = $_POST['hashedPassword'];
	$returnValue = null;

	// DEBUG : using get for test
	//$id = $_GET['id'];
	//$hashedPassword = $_GET['hashedPassword'];
}

// args : user's id and hashedpassword
// return : true if arguments are valid
function validArguments($id, $hashedPassword) {
	if ($id == null || strlen($hashedPassword) != 32)
		return false;
	else
		return true;
}

// return : true if user information added successfully
function addUserInformation($id, $hashedPassword) {
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

	if ($rows == 0) {
		$query = "INSERT INTO " . $configure['user_information_table_name'] . " VALUES (NULL, '$id', '$hashedPassword', default);";
		$result = mysql_query($query);
		if (!$result) {
			echo "질의 수행시 오류가 발생하였습니다.1".mysql_error();
			exit ;
		}

		mysql_close($link);
		return true;
	} else {
		// id duplication
		mysql_close($link);
		return false;
	}
}

// args : reference of variable for return with json
function process(&$returnValue) {
	global $configure;

	// get arguments
	getArguments($id, $hashedPassword);

	// invalid argument block
	if (validArguments($id, $hashedPassword) == false) {
		$returnValue['result'] = $configure['results']['invalid_argument']['message'];
		$returnValue['result_code'] = $configure['results']['invalid_argument']['code'];
		return;
	}

	// check if id and password is val
	if (addUserInformation($id, $hashedPassword) == true) {
		$returnValue['result'] = $configure['results']['success']['message'];
		$returnValue['result_code'] = $configure['results']['success']['code'];
	} else {
		$returnValue['result'] = $configure['results']['conflict']['message'];
		$returnValue['result_code'] = $configure['results']['conflict']['code'];
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