<?php

include_once "util.php";

function get_list($id, $token) {
	// args

	$args = array('id' => $id, 'token' => $token);
	$response = getResponstWithPost("/api/file/get_list/index.php?response_object=json", $args);
	$data = json_decode($response);
	//echo $response;

	// case별로 처리하기
	switch ($data->result_code) {
		case 200 :
		// Success
			return $data -> list;

		default :
			return null;
	}
}

function get_list_session() {
	session_start();
	return get_list($_SESSION['id'], $_SESSION['token']);
}

function get_dir_list($id, $token) {
	// args

	$args = array('id' => $id, 'token' => $token);
	$response = getResponstWithPost("/api/file/get_list/index.php?response_object=json", $args);
	$data = json_decode($response);
	//echo $response;

	// case별로 처리하기
	switch ($data->result_code) {
		case 200 :
		// Success
			foreach ($data->list as $one) {
				if ($one -> type != "dir") {
					$data[$one] = null;
				}
			}

			return $data -> list;

		default :
			return null;
	}
}

function get_dir_list_session() {
	session_start();
	return get_dir_list(getId(), getToken());
}

function downloadFile($id, $token, $path) {
	// args

	$args = array('id' => $id, 'token' => $token, 'filepath' => $path);
	$response = getResponstWithPost("/api/file/download/index.php?response_object=json", $args);
	echo $response;
}
?>