<?PHP
/*
 * openAPI
 * api/file/download
 * created on 12.08.30 by LEETAEHO(tae1560@gmail.com)
 *
 * description : 특정 파일을 다운로드 한다.
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
 * 	filepath
 * 		Required
 * 		Description : 파일의 위치
 * 		Example Values : folder1/test
 *
 * Result Values
 * 	파일 데이터 바이너리
 */

// load configurations and librarys
include_once "../../configure.php";
include_once "../../output.php";
include_once "../../user/token_manager/token_manager.php";
include_once $configure['dbconn_path'];

// args : reference of parameters
function getArguments(&$id, &$token, &$filepath) {
	$id = $_POST['id'];
	$token = $_POST['token'];
	$filepath = $_POST['filepath'];

	// DEBUG : using get for test
	$id = $_GET['id'];
	$token = $_GET['token'];
	$filepath = $_GET['filepath'];
	//$id = $_GET['id'];
	//$hashedPassword = $_GET['hashedPassword'];
}

// args : inputed parameters
// return : true if arguments are valid
function validArguments($id, $token, $filepath) {
	if ($id == null || $token == NULL || $filepath == NULL)
		return false;
	else
		return true;
}

// description : check userinformation with database
// args : user's id and hashedpassword
// return : true if userinformation is valid
function validUserInformation($id, $hashedPassword) {
	global $configure;
	global $dbconn;

	$link = mysql_connect($dbconn['address'], $dbconn['id'], $dbconn['password']) or die("Could not connect<br>");
	$select = mysql_select_db($configure['user_database_name']);
	if (!$select) {
		echo "데이타베이스 선택시 오류가 발생하였습니다.";
		exit ;
	}

	// get user information from database
	$query = "SELECT * FROM " . $configure['user_information_table_name'] . " WHERE id='$id' AND password='$hashedPassword';";
	$result = mysql_query($query);
	if (!$result) {
		echo "질의 수행시 오류가 발생하였습니다.";
		exit ;
	}

	$rows = mysql_num_rows($result);

	mysql_close($link);

	if ($rows == 1)
		return true;
	else
		return false;
}

// args : reference of variable for return with json
function process() {
	global $configure;

	// get arguments
	getArguments($id, $token, $filepath);

	// invalid argument block
	if (validArguments($id, $token, $filepath) == false) {
		$returnValue['result'] = $configure['results']['invalid_argument']['message'];
		$returnValue['result_code'] = $configure['results']['invalid_argument']['code'];
		return $returnValue;
	}

	// file download start
	ob_start();
	//$downfile = $_GET[downfile];
	$downfile = "index.php";
	//$downfiledir = "../upload/";
	$downfiledir = "./";
	// 파일이 있는 위치

	if (file_exists($downfiledir . $downfile)) {
		header("Content-Type: application/octet-stream");
		Header("Content-Disposition: attachment;; filename=$downfile");
		header("Content-Transfer-Encoding: binary");
		Header("Content-Length: " . (string)(filesize($downfiledir . $downfile)));
		Header("Cache-Control: cache, must-reval!idate");
		header("Pragma: no-cache");
		header("Expires: 0");
		$fp = fopen($downfiledir . $downfile, "rb");
		//rb 읽기전용 바이러니 타입
		while (!feof($fp)) {
			echo fread($fp, 100 * 1024);
			//echo는 전송을 뜻함.
		}
		fclose($fp);
		flush();
		//출력 버퍼비우기 함수..
	} else {
		echo("존재하지 않는 파일입니다.");
	}
	ob_end_clean();
	// file download finish

	$filename = "./index.php";
	if (file_exists($filename)) {
		$file_stat = stat($filename);
		print_r($file_stat);
		echo "<br/>";
		echo filetype($filename);
		echo "<br/>";
		echo filesize($filename);
		echo "<br/>";
		echo is_readable($filename);
		echo "<br/>";
		echo is_writable($filename);
		echo "<br/>";
		echo is_executable($filename);
		echo "<br/>";
		echo mime_content_type($filename);
		echo "<br/>";
		echo "exist";
	} else {
		echo "not exist";
	}

	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";

	$dir = ".";
	$fileList = scandir($dir) or die("scandir failed");
	print_r($fileList);

	echo "<br/>";
	echo "<br/>";

	// token validation block
	if (validToken($token) == false) {
		$returnValue['result'] = $configure['results']['failed_authentication']['message'];
		$returnValue['result_code'] = $configure['results']['failed_authentication']['code'];
		return $returnValue;
	}

	/*

	 // check if id and password is val
	 if (validUserInformation($id, $hashedPassword) == true) {
	 $token = tokenGenerated($id, $hashedPassword);
	 $returnValue['token'] = $token;
	 $returnValue['result'] = $configure['results']['success']['message'];
	 $returnValue['result_code'] = $configure['results']['success']['code'];
	 } else {
	 $returnValue['result'] = $configure['results']['failed_authentication']['message'];
	 $returnValue['result_code'] = $configure['results']['failed_authentication']['code'];
	 }

	 */

	return $returnValue;
}

function main() {
	$returnValue = process();

	// output return value
	output_result($returnValue);
}

// start main
main();
?>