<?php

// args : request address and post arguments
// return : response text
function getResponstWithPost($address, $args) {
	$postdata = http_build_query($args);
	$opts = array('http' => array('method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
	$context = stream_context_create($opts);
	$response = file_get_contents("http://" . $_SERVER['HTTP_HOST'] . $address, false, $context);
	return $response;
}

function messageWithAlert($message) {
	echo "<script type=\"text/javascript\" charset=\"utf-8\">";
	echo "alert(\"$message\");";
	echo "</script>";
}

function redirectToURL($url, $delay = 0) {
	echo "<script type=\"text/javascript\" charset=\"utf-8\">";
	echo "window.location = \"$url\";";
	echo "</script>";
}

function validate_session() {
	session_start();
	
	$id = $_SESSION['id'];
	$token = $_SESSION['token'];
	if ($id == null || $token == null) {
		return false;
	}

	$args = array('id' => $id, 'token' => $token);
	$response = getResponstWithPost("/api/file/get_information/index.php?response_object=json", $args);
	$data = json_decode($response);
	
	// case별로 처리하기
	switch ($data->result_code) {
		case 401 :
		// session timeout
			messageWithAlert("세션 시간 초과로 로그아웃 됩니다.");
			$_SESSION['id'] = null;
			$_SESSION['token'] = null;
			return false;
	}

	return true;
}
?>