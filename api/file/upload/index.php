<?php
/*
 * jQuery File Upload Plugin PHP Example 5.7
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

// load configurations and librarys
include_once "../../configure.php";
include_once "../../output.php";
include_once "../../user/token_manager/token_manager.php";
include_once "../common.php";
include_once $configure['dbconn_path'];
include_once "space_management.php";

function getArguments(&$id, &$token, &$folderpath) {
	$id = $_POST['id'];
	$token = $_POST['token'];
	$folderpath = $_POST['folderpath'];

	// DEBUG : using get for test
	//$id = $_GET['id'];
	//$token = $_GET['token'];
	//$folderpath = $_GET['folderpath'];
}

// args : inputed parameters
// return : true if arguments are valid
function validArguments($id, $token, $folderpath) {
	if ($id == null || $token == NULL)
		return false;
	else
		return true;
}

// args : reference of variable for return with json
function process() {
	global $configure;

	// get arguments
	getArguments($id, $token, $folderpath);
	
	// invalid argument block
	if (validArguments($id, $token, $folderpath) == false) {
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
	
	// ADDED by LEETAEHO on 120908 : restrict file size with user
	$total_file_size = 0;
	$upload = isset($_FILES['files']) ? $_FILES['files'] : null;
	if ($upload && is_array($upload['tmp_name'])) {
        // param_name is an array identifier like "files[]",
        // $_FILES is a multi-dimensional array:
        foreach ($upload['tmp_name'] as $index => $value) {
        	$size = isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                    $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index];
        	$filesize = intval($size);
			$total_file_size += $filesize;
        }
    } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
        // param_name is a single object identifier like "file",
        // $_FILES is a one-dimensional array:
        $size = isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                $_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ? $upload['size'] : null);
		$filesize = intval($size);
		$total_file_size = $filesize;	
    }	
	
	if (getFreeSpace($id) < $total_file_size/1024) { // KB
		$returnValue['result'] = $configure['results']['space_restriction']['message'];
		$returnValue['result_code'] = $configure['results']['space_restriction']['code'];
		return $returnValue;
	}
	

	error_reporting(E_ALL | E_STRICT);

	// protect subdir
	$path = removeSubdir($folderpath);

	// get list from folder
	$filename = $configure['storage_dir'] . $id . "/" . $path;

	if (!file_exists($filename)) {
		// 폴더가 없으면 폴더 생성
		mkdir($filename, 0777);
	}	

	if (file_exists($filename)) {
		require ('upload.class.php');
		
		// get full url
		$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
      	$getFullUrl = 
    		($https ? 'https://' : 'http://').
    		(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
    		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
    		($https && $_SERVER['SERVER_PORT'] === 443 ||
    		$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
    		substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
			
		// parameter
		$options = array (
			'upload_dir' => $filename,
            'upload_url' => $getFullUrl."/".$filename
		);
		$upload_handler = new UploadHandler($options);

		header('Pragma: no-cache');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Content-Disposition: inline; filename="files.json"');
		header('X-Content-Type-Options: nosniff');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
		header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

		switch ($_SERVER['REQUEST_METHOD']) {
			case 'OPTIONS' :
				break;
			case 'HEAD' :
			case 'GET' :
				$upload_handler -> get();
				break;
			case 'POST' :
				if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
					$upload_handler -> delete();
				} else {
					$upload_handler -> post();
				}
				break;
			case 'DELETE' :
				$upload_handler -> delete();
				break;
			default :
				header('HTTP/1.1 405 Method Not Allowed');
		}

		$returnValue['result'] = $configure['results']['success']['message'];
		$returnValue['result_code'] = $configure['results']['success']['code'];
		//$returnValue['list'] = $list;
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
	if ($returnValue) {
		output_result($returnValue);
	}
}

main();
