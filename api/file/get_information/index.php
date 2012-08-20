<?PHP
/*
 * openAPI
 * api/file/get_information
 * created on 12.08.30 by LEETAEHO(tae1560@gmail.com)
 *
 * description : 특정 파일에 대한 정보를 얻어온다.
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
 * 	result
 * 		Description : 결과에 대한 메시지
 * 		Example Values : Success, Failed to authenticate
 * 
 * 	result_code
 * 		Description : 결과에 대한 숫자 코드
 * 		Example Values : 200
 * 
 * 	information
 * 		Description : 파일 정보
 * 		Example Values : {"name":"test","type":"file","size":30503}
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
	if (validArguments($id, $token) == false) {
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
	$filename = "../../storage/".$id."/".$path;
	if (file_exists($filename)) {
		$file_stat = stat($filename);
		
		$file_information['name'] = basename($filename); 
		$file_information['type'] = filetype($filename);
		$file_information['size'] = $file_stat['size'];
		
		$returnValue['result'] = $configure['results']['success']['message'];
		$returnValue['result_code'] = $configure['results']['success']['code'];
		$returnValue['information'] = $file_information; 
	} else {
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