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
include_once "../common.php";
include_once $configure['dbconn_path'];

// args : reference of parameters
function getArguments(&$id, &$token, &$filepath) {
	$id = $_POST['id'];
	$token = $_POST['token'];
	$filepath = $_POST['filepath'];

	// DEBUG : using get for test
	//$id = $_GET['id'];
	//$token = $_GET['token'];
	//$filepath = $_GET['filepath'];
}

// args : inputed parameters
// return : true if arguments are valid
function validArguments($id, $token, $filepath) {
	if ($id == null || $token == NULL)
		return false;
	else
		return true;
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

	// token validation block
	if (validToken($token) == false) {
		$returnValue['result'] = $configure['results']['failed_authentication']['message'];
		$returnValue['result_code'] = $configure['results']['failed_authentication']['code'];
		return $returnValue;
	}

	// protect subdir
	$path = removeSubdir($filepath);
	
	// get list from folder
	$filename = "../../storage/" . $id . "/" . $path;

	// file download start
	//ob_start();

	if (file_exists($filename) && filetype($filename) != "dir") {
		header("Content-Type: application/octet-stream");
		Header("Content-Disposition: attachment;; filename=$downfile");
		header("Content-Transfer-Encoding: binary");
		Header("Content-Length: " . (string)(filesize($filename)));
		Header("Cache-Control: cache, must-reval!idate");
		header("Pragma: no-cache");
		header("Expires: 0");
		$fp = fopen($filename, "rb");
		//rb 읽기전용 바이러니 타입
		while (!feof($fp)) {
			echo fread($fp, 100 * 1024);
			//echo는 전송을 뜻함.
		}
		fclose($fp);
		flush();
		//출력 버퍼비우기 함수..

		return null;
	} else {
		$returnValue['result'] = $configure['results']['not_found']['message'];
		$returnValue['result_code'] = $configure['results']['not_found']['code'];
		return $returnValue;
	}
	//ob_end_clean();
	// file download finish
}

function main() {
	$returnValue = process();

	// output return value
	if ($returnValue) {
		output_result($returnValue);
	}
}

// start main
main();
?>