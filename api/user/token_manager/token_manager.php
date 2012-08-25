<?PHP
/*
 * OpenAPI
 * created on 12.08.25 by LEETAEHO(tae1560@gmail.com)
 * Token Manager
 *
 * description : 회원의 Session에 해당하는 Token을 전체적으로 관리한다.
 * functions : 생성, get, search, remove
 */

include_once "../../configure.php";
include_once $configure['dbconn_path'];

// args : user's id and hashedpassword
// return : token string
function tokenGenerated($id, $hashedPassword) {

	$token = searchToken($id);
	if (validToken($token) == false) {
		// remove token
		removeToken($id);

		// generate token
		$token = $id . $hashedPassword . time();
		$token = md5($token);

		// add information to database
		global $configure;
		global $dbconn;

		$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect1<br>");
		$select = mysql_select_db($configure['user_database_name']);
		if (!$select) {
			echo "데이타베이스 선택시 오류가 발생하였습니다.";
			exit ;
		}

		// remove token information
		$query = "INSERT INTO " . $configure['token_information_table_name'] . " VALUES (NULL, '$id', '$token', now());";
		$result = mysql_query($query);
		if (!$result) {
			echo "질의 수행시 오류가 발생하였습니다.";
			exit ;
		}

		mysql_close($link);
	}

	return $token;
}

// args : user's id and token
// return : token for user id
function searchToken($id) {
	global $configure;
	global $dbconn;

	$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br>");
	$select = mysql_select_db($configure['user_database_name']);
	if (!$select) {
		echo "데이타베이스 선택시 오류가 발생하였습니다.";
		exit ;
	}

	// search token information
	$query = "SELECT * FROM " . $configure['token_information_table_name'] . " WHERE id='$id';";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}

	$rows = mysql_num_rows($result);

	$row = mysql_fetch_array($result);

	$token = $row['token'];

	mysql_close($link);

	return $token;
}

// args : user's id and token
// return : true if removing token was success
function removeToken($id) {
	global $configure;
	global $dbconn;

	$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br>");
	$select = mysql_select_db($configure['user_database_name']);
	if (!$select) {
		echo "데이타베이스 선택시 오류가 발생하였습니다.";
		exit ;
	}

	// remove token information
	$query = "DELETE FROM " . $configure['token_information_table_name'] . " WHERE id='$id'";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}

	mysql_close($link);

	return true;
}

// args : token
// return : true if token is valid
function validToken($token) {
	// invalid if token length is not 32
	if (32 != strlen($token))
		return false;

	global $configure;
	global $dbconn;

	$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br>");
	$select = mysql_select_db($configure['user_database_name']);
	if (!$select) {
		echo "데이타베이스 선택시 오류가 발생하였습니다.";
		exit ;
	}

	// check timeout
	$query = "SELECT * FROM " . $configure['token_information_table_name'] . " WHERE token='$token' AND created_time> DATE_ADD(now(), interval -".$configure['token_timeout']." minute)";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}

	$rows = mysql_num_rows($result);

	// token must unique
	if ($rows != 1) {
		mysql_close($link);
		return false;
	}

	// refresh token
	$query = "UPDATE " . $configure['token_information_table_name'] . " SET created_time=now() WHERE token='$token';";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}

	mysql_close($link);
	return true;
}
?>