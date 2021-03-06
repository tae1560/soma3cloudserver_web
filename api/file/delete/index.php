<?PHP
/*
 * openAPI
 * api/file/delete
 * created on 12.09.04 by LEETAEHO(tae1560@gmail.com)
 *
 * description : 
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
 * 		Description : 폴더의 위치
 * 		Example Values : folder1/
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
 * 	list
 * 		Description : 파일 및 폴더 리스트
 * 		Example Values : {{"name":"folder2","type":"folder"},{"name":"test","type":"file","size":15677}}
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
	// $id = $_GET['id'];
	// $token = $_GET['token'];
	// $folderpath = $_GET['filepath'];
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

	// get list from file
	$filename = $configure['storage_dir'] . $id . "/" . $path;

	if (file_exists($filename)) {
		// 파일 있으면 파일 삭제
		
		if (filetype($filename) == "dir") {
			rmdir($filename);			
		} else {
			unlink($filename);
		}
		
		$returnValue['result'] = $configure['results']['success']['message'];
		$returnValue['result_code'] = $configure['results']['success']['code'];
	}
	else {
		// file is not exist
		$returnValue['result'] = $configure['results']['not_found']['message'];
		$returnValue['result_code'] = $configure['results']['not_found']['code'];
	}

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