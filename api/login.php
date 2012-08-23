<?PHP
/*
 * openAPI
 * login.php created on 12.08.23 by LEETAEHO(tae1560@gmail.com)
 *
 * description : Login with arguments and return token.
 * This token can be used for another pages to authenticate user.
 *
 * Arguments
 * 	- id
 * 		- method : POST
 * 		- type : string
 * 		- description : User's id
 *
 * 	- hashedPassword
 * 		- method : POST
 * 		- type : string
 * 		- description : Hashed user's password by MD5
 *
 *
 * Return Values
 * 	- result
 * 		- type : string
 * 		- description : result log message 
 *
 * 	- token
 * 		- type : string
 * 		- description : session authentication value
 * 
 * 	- 
 */

// load configurations
include_once "configure.php";

// args : reference of user's id and hashedpassword
function getArguments(&$id, &$hashedPassword) {
	$id = $_POST['id'];
	$hashedPassword = $_POST['hashedPassword'];
	$returnValue = null;

	// DEBUG : using get for test
	$id = $_GET['id'];
	$hashedPassword = $_GET['hashedPassword'];
}

// args : user's id and hashedpassword
// return : true if arguments are valid
function validArguments($id, $hashedPassword) {
	if ($id == null || $hashedPassword == NULL)
		return false;
	else
		return true;
}

// description : check userinformation with database
// args : user's id and hashedpassword
// return : true if userinformation is valid
function validUserInformation($id, $hashedPassword) {
	global $configure;

	include_once $configure['dbconn_path'];

	$link = mysql_connect($dbconn_address, $dbconn_id, $dbconn_password) or die("Could not connect<br>");
	$select = mysql_select_db($configure['user_database_name']);
	if (!$select) {
		echo "데이타베이스 선택시 오류가 발생하였습니다.";
		exit ;
	}

	// $query = "CREATE TABLE  sample (num tinyint NOT NULL, name char(11),  ".
	// "  job varchar(20), salary int, primary key (num))";

	// get user information from database
	$query = "SELECT * FROM users WHERE id='$id' AND password='$hashedPassword'";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}
	
	$rows  = mysql_num_rows($result);
	
	if ($rows == 1) return true;
	else return false;
}

// args : user's id and hashedpassword
// return : token string
function tokenGenerated($id, $hashedPassword) {

	$token = $id . $hashedPassword . time();
	$token = md5($token);
	return $token;
}

// args : reference of variable for return with json 
function process(&$returnValue) {
	// get arguments
	getArguments($id, $hashedPassword);

	// invalid argument block
	if (validArguments($id, $hashedPassword) == false) {
		$returnValue['result'] = "Invalid Argument";
		return;
	}

	// check if id and password is val
	if (validUserInformation($id, $hashedPassword) == true) {
		$token = tokenGenerated($id, $hashedPassword);
		$returnValue['token'] = $token;
		$returnValue['result'] = "Success";				
	} else {
		$returnValue['result'] = "Failed to authenticate";
	}
}

function main() {

	process($returnValue);

	// output return value
	echo json_encode($returnValue);
}

// start main
main();
?>